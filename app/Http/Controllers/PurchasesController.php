<?php

namespace App\Http\Controllers;

use App\Models\departmentCredits;
use App\Models\pettyCash;
use App\Models\Purchases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function pettyCashes($id, $args = array())
    {
        if (Auth::check() && isCashier()) {
            $id = sanitize($id);

            if (!empty($id) && verifyDepartment($id)) {
                $purchases = Purchases::where('pos_code', company()->pos_code)->where('department', $id)->get();
                $credits = departmentCredits::where('department', $id)->where('amount', '>', '0')->get();
                $balance = pettyCash::where('pos_code', company()->pos_code)->where('department', $id)->sum('balance');

                return view('pos.add-petty-cash')->with(['purchases' => $purchases, 'balance' => $balance, 'credits' => $credits, 'id' => $id, 'args' => $args]);
            }

            return display404();
        }
        return redirect('/signin');
    }

    public function transferPattyCash(Request $request)
    {
        if (Auth::check() && isCashier()) {
            $amount = sanitize($request->input('amount'));
            $dep = sanitize($request->input('department'));
            $id = sanitize($request->input('model_id'));

            if (!empty($id) && verifyDepartment($id)) {

                if (empty($dep) || !verifyDepartment($dep)) {
                    return redirect('/dashboard/petty-cash/' . $id . '?error=1&message=Invalid-department');
                }

                if ($dep == $id) {
                    return redirect('/dashboard/petty-cash/' . $id . '?error=1&message=Cannot-transfer-fund-to-same-department');
                }

                if (!is_numeric($amount)) {
                    //return $this->pettyCashes($id, array('error' => 1, 'message' => 'Enter amount in number', 'note' => $note));
                    return redirect('/dashboard/petty-cash/' . $id . '?error=1&message=Enter-amount-in-number');
                }

                $pettycash = new pettyCash();
                $pettycash->department = $id;
                $pettycash->amount = -$amount;
                $pettycash->balance = -$amount;
                $pettycash->pos_code = company()->pos_code;
                $pettycash->note = "Transfer fund to " . getDepartment($dep) . " From " . getDepartment($id);

                if ($pettycash->save()) {
                    $pettycash = new pettyCash();
                    $pettycash->department = $dep;
                    $pettycash->amount = $amount;
                    $pettycash->balance = $amount;
                    $pettycash->pos_code = company()->pos_code;
                    $pettycash->note = "Transfer fund to " . getDepartment($dep) . " From " . getDepartment($id);
                    $pettycash->save();

                    $credit = new departmentCredits();
                    $credit->department = $dep;
                    $credit->from_dep = $id;
                    $credit->amount = $amount;
                    $credit->save();

                    return redirect('/dashboard/petty-cash/' . $id . '?error=0&message=Fund-transfer-successfull');
                }

                return redirect('/dashboard/petty-cash/' . $id . '?error=1&message=Something-went-wrong');
            }

            return display404();
        }
        return redirect('/signin');
    }

    public function listPettyCash($id)
    {
        if (Auth::check() && isCashier()) {
            $id = sanitize($id);

            if (!empty($id) && verifyDepartment($id)) {
                $balance = pettyCash::where('pos_code', company()->pos_code)->where('department', $id)->get();
                return view('pos.list-pettycash')->with(['lists' => $balance, 'id' => $id]);
            }

            //return display404();
        }
        return redirect('/signin');
    }

    public function addPattyCash(Request $request)
    {
        if (Auth::check() && isCashier()) {
            $amount = sanitize($request->input('amount'));
            $note = sanitize($request->input('note'));
            $id = sanitize($request->input('model_id'));

            if (empty($id) || !verifyDepartment($id)) {
                return display404();
            }

            if (!is_numeric($amount)) {
                //return $this->pettyCashes($id, array('error' => 1, 'message' => 'Enter amount in number', 'note' => $note));
                return redirect('/dashboard/petty-cash/' . $id . '?error=1&message=Enter-amount-in-number&note=' . str_replace(' ', '-', $note));
            }

            $pettycash = new pettyCash();
            $pettycash->department = $id;
            $pettycash->amount = $amount;
            $pettycash->balance = $amount;
            $pettycash->pos_code = company()->pos_code;
            $pettycash->note = $note;

            if ($pettycash->save()) {
                return redirect('/dashboard/petty-cash/' . $id . '?error=0&message=Petty-cash-saved-successfully');
            }

            return redirect('/dashboard/petty-cash/' . $id . '?error=1&message=Error-while-saving-petty-cash');
        }
        return redirect('/signin');
    }

    public function payDepartmentCredit(Request $request)
    {
        if (Auth::check() && isCashier()) {
            $amount = sanitize($request->input('amount'));
            $id = sanitize($request->input('id'));
            $dep = sanitize($request->input('from_dep'));
            $currentdep = sanitize($request->input('current_dep'));

            $creditTotal = departmentCredits::where('id', $id)->sum('amount');
            if ($creditTotal > 0) {
                $minus = $creditTotal - $amount > 0 ? $creditTotal - $amount : 0;
                $payCredit = departmentCredits::where('id', $id)->update(['amount' => ($minus)]);

                if ($payCredit) {
                    $payBack = new pettyCash();
                    $payBack->department = $dep;
                    $payBack->amount = $creditTotal > $amount ? $amount : $creditTotal;
                    $payBack->balance = $creditTotal > $amount ? $amount : $creditTotal;
                    $payBack->pos_code = company()->pos_code;
                    $payBack->note = "Credit payback";
                    $payBack->save();

                    $pettycash = new pettyCash();
                    $pettycash->department = $currentdep;
                    $pettycash->amount = $creditTotal > $amount ? -$amount : -$creditTotal;
                    $pettycash->balance = $creditTotal > $amount ? -$amount : -$creditTotal;
                    $pettycash->pos_code = company()->pos_code;
                    $pettycash->note = "Credit pay minus";
                    $pettycash->save();

                    return response(json_encode(["error" => 0, "message" => "Credit paid"]));
                }
            }

            return response(json_encode(["error" => 1, "message" => "Error while paying credit"]));
        }
        return redirect('/signin');
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
        if (Auth::check() && isCashier()) {
            $purchase_number = sanitize($request->input('purchase_number'));
            $purchase_number = str_replace(' ', '', $purchase_number);
            (float)$price = sanitize($request->input('price'));
            (float)$stock = sanitize($request->input('stock'));
            (float)$discount = sanitize($request->input('discount'));
            (float)$shipping_charge = sanitize($request->input('shipping_charge'));
            $supplier = sanitize($request->input('supplier'));
            $note = sanitize($request->input('note'));
            $department = isset($_POST['department']) ? sanitize($request->input('department')) : '';

            $code_verify = Purchases::where('purshace_no', $purchase_number)->where('pos_code', company()->pos_code)->get();

            if ($code_verify && $code_verify->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Purchase Number Already Exists")));
            } elseif (!is_numeric($price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price")));
            } elseif (!is_numeric($shipping_charge)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Shipping Charge")));
            } elseif (!is_numeric($stock)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Stock")));
            } elseif (empty($purchase_number) || empty($price) || empty($stock) || empty($supplier)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            if ($supplier != "other" && getSupplier($supplier)->pos_code != company()->pos_code) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Supplier")));
            }

            $purchase = new Purchases();
            $purchase->purshace_no = $purchase_number;
            $purchase->price = $price;
            $purchase->qty = $stock;
            $purchase->discount = $discount;
            $purchase->shipping_charge = $shipping_charge;
            $purchase->supplier_id = $supplier == 'other' ? '0' : $supplier;
            $purchase->pos_code = company()->pos_code;
            $purchase->note = $note;

            if (!empty($department) && verifyDepartment($department)) {
                $purchase->department = $department;
            }

            if ($purchase->save()) {

                if (!empty($department) && verifyDepartment($department)) {
                    $pettycash = new pettyCash();
                    $pettycash->department = $department;
                    $pettycash->amount = - ((($price - $discount) * $stock) + $shipping_charge);
                    $pettycash->balance = - ((($price - $discount) * $stock) + $shipping_charge);
                    $pettycash->pos_code = company()->pos_code;
                    $pettycash->note = "Purchase of purchase number " . $purchase_number;
                    $pettycash->save();
                }

                return response(json_encode(array("error" => 0, "msg" => "Purchase Added Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchases $purchases)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (!Auth::check() && DashboardController::check(true)) {
            return redirect('/signin');
        }

        $purchase = Purchases::where('purshace_no', sanitize($id))->where('pos_code', company()->pos_code)->get();

        if ($purchase && $purchase->count() > 0) {
            return view('pos.add-purchase')->with('purchase', $purchase[0]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchases $purchases)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $purchase_number = sanitize($request->input('purchase_number'));
            $purchase_number = str_replace(' ', '', $purchase_number);
            (float)$price = sanitize($request->input('price'));
            (float)$stock = sanitize($request->input('stock'));
            (float)$discount = sanitize($request->input('discount'));
            (float)$shipping_charge = sanitize($request->input('shipping_charge'));
            $supplier = sanitize($request->input('supplier'));
            $note = sanitize($request->input('note'));

            $id_verify = Purchases::where('id', $id)->where('pos_code', company()->pos_code)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            $code_verify = Purchases::where('id', '!=', $id)->where('purshace_no', $purchase_number)->where('pos_code', company()->pos_code)->get();

            if ($code_verify && $code_verify->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Purchase Number Already Exists")));
            } elseif (!is_numeric($price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price")));
            } elseif (!is_numeric($shipping_charge)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Shippiing Charge")));
            } elseif (!is_numeric($stock)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Stock")));
            } elseif (empty($purchase_number) || empty($price) || empty($stock) || empty($supplier)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            if ($supplier != "other" && getSupplier($supplier)->pos_code != company()->pos_code) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Supplier")));
            }

            $purchase = Purchases::where('id', $id)->update([
                "purshace_no" => $purchase_number,
                "price" => $price,
                "qty" => $stock,
                "discount" => $discount,
                "shipping_charge" => $shipping_charge,
                "supplier_id" => $supplier == 'other' ? '0' : $supplier,
                "note" => $note,
            ]);

            if ($purchase) {
                return response(json_encode(array("error" => 0, "msg" => "Purchase Updated Successfully", 'code' => $purchase_number)));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchases $purchases)
    {
        //
    }
}
