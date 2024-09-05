<?php

namespace App\Http\Controllers;

use App\Models\Purchases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        if (Auth::check() && DashboardController::check(true)) {
            $purchase_number = sanitize($request->input('purchase_number'));
            $purchase_number = str_replace(' ', '', $purchase_number);
            (float)$price = sanitize($request->input('price'));
            (float)$stock = sanitize($request->input('stock'));
            (float)$discount = sanitize($request->input('discount'));
            (float)$shipping_charge = sanitize($request->input('shipping_charge'));
            $supplier = sanitize($request->input('supplier'));
            $note = sanitize($request->input('note'));

            $code_verify = Purchases::where('purshace_no', $purchase_number)->where('pos_code', company()->pos_code)->get();

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

            $purchase = new Purchases();
            $purchase->purshace_no = $purchase_number;
            $purchase->price = $price;
            $purchase->qty = $stock;
            $purchase->discount = $discount;
            $purchase->shipping_charge = $shipping_charge;
            $purchase->supplier_id = $supplier=='other'? '0' : $supplier;
            $purchase->pos_code = company()->pos_code;
            $purchase->note = $note;

            if ($purchase->save()) {
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
                "supplier_id" => $supplier=='other'? '0' : $supplier,
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
