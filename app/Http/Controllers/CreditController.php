<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\CreditHistory;
use App\Models\customers;
use App\Models\partners;
use App\Models\Repairs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && DashboardController::check(true)) {
            $credits = Credit::where('customer_id', '!=', 0)->where('pos_code', company()->pos_code)->where('ammount', '>', 0)->whereDate('created_at', Carbon::today())->get();
            if ($credits) {
                foreach ($credits as $key => $credit) {
                    $credit['history'] = CreditHistory::where('credit_id', $credit->id)->get();
                }
            }
            $customers = customers::where('pos_code', company()->pos_code)->get();
            return view('pos.credits')->with(['credits' => $credits, 'customers' => $customers]);
        } else {
            return redirect('/signin');
        }
    }

    public function partnerIndex()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && DashboardController::check(true)) {
            $credits = Credit::where('pos_code', company()->pos_code)->where('ammount', '>', 0)->whereDate('created_at', Carbon::today())->get();
            if ($credits) {
                foreach ($credits as $key => $credit) {
                    $credit['history'] = CreditHistory::where('credit_id', $credit->id)->get();
                    $credit['partner'] = ['id' => '', 'name' => '', 'company' => '', 'phone' => ''];
                }
            }
            $partners = partners::all();
            return view('pos.partnerCredits')->with(['credits' => $credits, 'partners' => $partners]);
        } else {
            return redirect('/signin');
        }
    }

    public function payCredit(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {

            $customerId = sanitize($request->input('params')['credit']);
            $paymentAmount = sanitize($request->input('params')['amount']);
            $paymentType = sanitize($request->input('params')['type']);
            $paid = $paymentAmount;

            if ($paymentType == 'manual') {
                $credit = Credit::where('id', $customerId)->first();
                if ($credit) {


                    $total_due = DB::table('credits')
                        ->where('customer_id', $credit->customer_id)
                        ->where('ammount', '>', 0) // Select bills with balance remaining
                        ->sum('ammount');

                    DB::table('credits')
                        ->where('id', $credit->id)
                        ->update([
                            'ammount' => ($credit->ammount - $paymentAmount < 0 ? 0 : $credit->ammount - $paymentAmount), // Reduce balance
                            'status' => ($credit->ammount - $paymentAmount < 0 ? 'paid' : 'partially paid'),
                            'updated_at' => now()
                        ]);

                    $this->recordPayment($credit, $paymentAmount);

                    if ($total_due < $paymentAmount) {
                        if ($paymentAmount > 0) {
                            $this->storeAdvancePayment($credit->customer_id, $total_due - $paymentAmount, 'customer');
                        }
                    }

                    $bill_name = time() . rand(1111, 9999999) . rand(111, 9999) . '.pdf';
                    $payment = generateCreditPay($total_due, $paid, $credit->customer_id, Carbon::now(), $bill_name);

                    return response(json_encode(array('error' => 0, 'msg' => 'Payment recorded successfully', 'bill' => $bill_name)));
                }

                return response(json_encode(array('error' => 1, 'msg' => 'Error recording payment')));
            }


            $total_due = DB::table('credits')
                ->where('customer_id', $customerId)
                ->where('ammount', '>', 0) // Select bills with balance remaining
                ->sum('ammount');

            $bills = DB::table('credits')
                ->where('customer_id', $customerId)
                ->where('ammount', '>', 0) // Select bills with balance remaining
                ->orderBy('created_at', 'asc') // FIFO order
                ->get();

            foreach ($bills as $bill) {
                if ($paymentAmount <= 0) {
                    break; // Stop if no more money left
                }

                if ($paymentAmount >= $bill->ammount) {
                    // Fully pay the bill
                    DB::table('credits')
                        ->where('id', $bill->id)
                        ->update([
                            'ammount' => 0, // Balance cleared
                            'status' => 'paid',
                            'updated_at' => now()
                        ]);

                    $this->recordPayment($bill, $bill->ammount);
                    $paymentAmount -= $bill->ammount; // Deduct used amount
                } else {
                    // Partially pay the bill
                    DB::table('credits')
                        ->where('id', $bill->id)
                        ->update([
                            'ammount' => $bill->ammount - $paymentAmount, // Reduce balance
                            'status' => 'partially paid',
                            'updated_at' => now()
                        ]);

                    $this->recordPayment($bill, $paymentAmount);
                    $paymentAmount = 0; // Entire payment used
                }
            }

            if ($paymentAmount > 0) {
                $this->storeAdvancePayment($customerId, $paymentAmount, 'customer');
            }

            $bill_name = time() . rand(1111, 9999999) . rand(111, 9999) . '.pdf';
            $payment = generateCreditPay($total_due, $paid, $customerId, Carbon::now(), $bill_name);

            return response(json_encode(array('error' => 0, 'msg' => 'Payment recorded successfully', 'bill' => $bill_name)));
        }
        return response(json_encode(array('error' => 1, 'msg' => 'Not logged In')));
    }

    private function recordPayment($bill, $amount)
    {
        $bill_name = time() . rand(1111, 9999999) . rand(111, 9999) . '.pdf';
        $payment = generateCreditPay($bill->ammount, $amount, $bill->customer_id, Carbon::now(), $bill_name);
        $credit = new CreditHistory();
        $credit->credit_id = $bill->id;
        $credit->customer_id = $bill->customer_id;
        $credit->ammount = $amount;
        $credit->bill = $bill_name;
        $credit->save();
    }

    public function getCredits(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $credits = array();

            if (sanitize($request->input('params')['customer_id']) == 'all') {
                $credits = Credit::where('pos_code', company()->pos_code)->where('ammount', '>', 0)->orderBy('id', 'DESC')->get();
            } else {
                $credits = Credit::where('customer_id', sanitize($request->input('params')['customer_id']))->where('pos_code', company()->pos_code)->where('ammount', '>', 0)->orderBy('id', 'DESC')->get();
            }

            foreach ($credits as $key => $credit) {
                $credithistory = CreditHistory::where('credit_id', $credit->id)->where('customer_id', sanitize($request->input('params')['customer_id']))->orderBy('id', 'DESC')->get();
                $credit['history'] = $credithistory;
            }

            return response(json_encode($credits));
        }
    }

    public function getPartnerCredits(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $credits = array();

            if (sanitize($request->input('params')['partner_id']) == 'all') {
                $credits = Credit::where('ammount', '>', 0)->orderBy('id', 'DESC')->get();
            } else {
                $billNumbers = [];

                foreach (Repairs::where('partner', sanitize($request->input('params')['partner_id']))->where('status', 'Delivered')->get() as $key => $value) {
                    $billNumbers[] = $value->bill_no;
                }

                $credits = DB::table('credits')
                    ->whereExists(function ($query) use ($billNumbers) {
                        $query->select(DB::raw(1))
                            ->from('repairs')
                            ->whereRaw('FIND_IN_SET(repairs.bill_no, credits.order_id) > 0')
                            ->whereIn('repairs.bill_no', $billNumbers);
                    })
                    ->where('ammount', '>', 0)
                    ->get()
                    ->toArray();
            }

            foreach ($credits as $key => $credit) {
                $credithistory = CreditHistory::where('credit_id', $credit->id)->orderBy('id', 'DESC')->get();
                $credit->history = $credithistory;
                $credit->partner = partners::where('id', sanitize($request->input('params')['partner_id']))->first(['id', 'name', 'company', 'phone']);
            }

            return response(json_encode($credits));
        }
    }

    public function payPartnerCredit(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {

            $credit = sanitize($request->input('params')['credit']);
            $partner_id = sanitize($request->input('params')['partner_id']);
            $paymentAmount = sanitize($request->input('params')['amount']);
            $paid = $paymentAmount;

            if (sanitize($request->input('params')['type']) == 'manual') {
                $credit = Credit::where('id', $credit)->first();
                if ($credit) {

                    foreach (Repairs::where('partner', $partner_id)->where('status', 'Delivered')->get() as $key => $value) {
                        $billNumbers[] = $value->bill_no;
                    }

                    $total_due = DB::table('credits')
                        ->whereExists(function ($query) use ($billNumbers) {
                            $query->select(DB::raw(1))
                                ->from('repairs')
                                ->whereRaw('FIND_IN_SET(repairs.bill_no, credits.order_id) > 0')
                                ->whereIn('repairs.bill_no', $billNumbers);
                        })
                        ->where('ammount', '>', 0)
                        ->sum('ammount');

                    DB::table('credits')
                        ->where('id', $credit->id)
                        ->update([
                            'ammount' => ($credit->ammount - $paymentAmount < 0 ? 0 : $credit->ammount - $paymentAmount), // Reduce balance
                            'status' => ($credit->ammount - $paymentAmount < 0 ? 'paid' : 'partially paid'),
                            'updated_at' => now()
                        ]);

                    $this->recordPayment($credit, $paymentAmount);

                    if ($total_due < $paymentAmount) {
                        if ($paymentAmount > 0) {
                            $this->storeAdvancePayment($partner_id, $total_due - $paymentAmount, 'partner');
                        }
                    }

                    $bill_name = time() . rand(1111, 9999999) . rand(111, 9999) . '.pdf';
                    $payment = generateCreditPay($total_due, $paid, $credit->customer_id, Carbon::now(), $bill_name);

                    return response(json_encode(array('error' => 0, 'msg' => 'Payment recorded successfully', 'bill' => $bill_name)));
                }

                return response(json_encode(array('error' => 1, 'msg' => 'Error recording payment')));
            }

            $billNumbers = [];

            foreach (Repairs::where('partner', $credit)->where('status', 'Delivered')->get() as $key => $value) {
                $billNumbers[] = $value->bill_no;
            }

            $total_due = DB::table('credits')
                ->whereExists(function ($query) use ($billNumbers) {
                    $query->select(DB::raw(1))
                        ->from('repairs')
                        ->whereRaw('FIND_IN_SET(repairs.bill_no, credits.order_id) > 0')
                        ->whereIn('repairs.bill_no', $billNumbers);
                })
                ->where('ammount', '>', 0)
                ->sum('ammount');

            $bills = DB::table('credits')
                ->whereExists(function ($query) use ($billNumbers) {
                    $query->select(DB::raw(1))
                        ->from('repairs')
                        ->whereRaw('FIND_IN_SET(repairs.bill_no, credits.order_id) > 0')
                        ->whereIn('repairs.bill_no', $billNumbers);
                })
                ->where('ammount', '>', 0)
                ->get()
                ->toArray();

            foreach ($bills as $bill) {
                if ($paymentAmount <= 0) {
                    break; // Stop if no more money left
                }

                if ($paymentAmount >= $bill->ammount) {
                    // Fully pay the bill
                    DB::table('credits')
                        ->where('id', $bill->id)
                        ->update([
                            'ammount' => 0, // Balance cleared
                            'status' => 'paid',
                            'updated_at' => now()
                        ]);

                    $this->recordPayment($bill, $bill->ammount);
                    $paymentAmount -= $bill->ammount; // Deduct used amount
                } else {
                    // Partially pay the bill
                    DB::table('credits')
                        ->where('id', $bill->id)
                        ->update([
                            'ammount' => $bill->ammount - $paymentAmount, // Reduce balance
                            'status' => 'partially paid',
                            'updated_at' => now()
                        ]);

                    $this->recordPayment($bill, $paymentAmount);
                    $paymentAmount = 0; // Entire payment used
                }
            }

            if ($paymentAmount > 0) {
                $this->storeAdvancePayment($partner_id, $paymentAmount, 'partner');
            }

            $bill_name = time() . rand(1111, 9999999) . rand(111, 9999) . '.pdf';
            $payment = generateParterCreditPay($total_due, $paid, $credit, Carbon::now(), $bill_name);

            return response(json_encode(array('error' => 0, 'msg' => 'Payment recorded successfully', 'bill' => $bill_name)));
        }
        return response(json_encode(array('error' => 1, 'msg' => 'Not logged In')));
    }

    private function storeAdvancePayment($customerId, $amount, $type)
    {
        if ($type == 'customer') {
            $credit = getCustomer($customerId);
            customers::where('id', $customerId)->update([
                'store_credit' => abs($amount) + (isset($credit->store_credit) ? $credit->store_credit : 0),
            ]);

            return;
        }

        if ($type == 'partner') {
            $credit = getPartner($customerId);
            partners::where('id', $customerId)->update([
                'store_credit' => abs($amount) + (isset($credit->store_credit) ? $credit->store_credit : 0),
            ]);
            return;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Credit $credit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Credit $credit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Credit $credit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Credit $credit)
    {
        //
    }
}
