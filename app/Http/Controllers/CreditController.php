<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\CreditHistory;
use App\Models\customers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        login_redirect('/'. request()->path());

        if (Auth::check() && DashboardController::check(true)) {
            $credits = Credit::where('pos_code', company()->pos_code)->where('ammount', '>', 0)->whereDate('created_at', Carbon::today())->get();
            if ($credits) {
                foreach ($credits as $key => $credit) {
                    $credit['history'] = CreditHistory::where('credit_id', $credit->id)->get();
                }
            }
            $customers = customers::where('pos_code', company()->pos_code)->get();
            return view('pos.credits')->with(['credits'=>$credits, 'customers'=> $customers]);
        }
        else{
            return redirect('/signin');
        }
    }

    public function payCredit(Request $request) {
        if (Auth::check() && DashboardController::check(true)) {
            
            $credits = Credit::where('id', sanitize($request->input('params')['credit']))->where('pos_code', company()->pos_code)->where('ammount', '>', 0);
            if (!$credits->get() || $credits->count() == 0) {
                return response(json_encode(array('error'=> 1, 'msg'=>'Invalid Attempt')));
            }

            $credit_dtl = $credits->get();

            if (!is_numeric(sanitize($request->input('params')['amount'])) || $credit_dtl[0]->ammount < sanitize($request->input('params')['amount'])) {
                return response(json_encode(array('error'=> 1, 'msg'=>'Pay amount greater than due balance')));
            }

            $bill_name = time().rand(1111, 9999999).rand(111, 9999).'.pdf';
            
            $credit = new CreditHistory();
            $credit->credit_id = $credit_dtl[0]->id;
            $credit->customer_id = $credit_dtl[0]->customer_id;
            $credit->ammount = sanitize($request->input('params')['amount']);
            $credit->bill = $bill_name;
            if ($credit->save()) {
                $credit_update = $credits->update([
                    'ammount' => $credit_dtl[0]->ammount - (float)sanitize($request->input('params')['amount']),
                ]);

                if ($credit_update) {
                    $payment = generateCreditPay($credit_dtl[0]->ammount, sanitize($request->input('params')['amount']), $credit_dtl[0]->customer_id, Carbon::now(), $bill_name);
                    if ($payment) {
                        return response(json_encode(array('error'=> 0, 'bill'=>$bill_name)));
                    }
                    return response(json_encode(array('error'=> 1, 'msg'=>'Due paid successfully, error while generating invoice')));
                }
                return response(json_encode(array('error'=> 1, 'msg'=>'Error while paying due')));
            }

            return response(json_encode(array('error'=> 1, 'msg'=>'Error while paying due')));
        }
        return response(json_encode(array('error'=> 1, 'msg'=>'Not logged In')));
    }

    public function getCredits(Request $request) {
        if (Auth::check() && DashboardController::check(true)) {
            $credits = array();

            if (sanitize($request->input('params')['customer_id']) == 'all') {
                $credits = Credit::where('pos_code', company()->pos_code)->where('ammount', '>', 0)->orderBy('id', 'DESC')->get();
            }
            else {
                $credits = Credit::where('customer_id', sanitize($request->input('params')['customer_id']))->where('pos_code', company()->pos_code)->where('ammount', '>', 0)->orderBy('id', 'DESC')->get();
            }

            foreach ($credits as $key => $credit) {
                $credithistory = CreditHistory::where('credit_id', $credit->id)->where('customer_id', sanitize($request->input('params')['customer_id']))->orderBy('id', 'DESC')->get();
                $credit['history'] = $credithistory;
            }

            return response(json_encode($credits));
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
