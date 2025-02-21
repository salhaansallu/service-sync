<?php

namespace App\Http\Controllers;

use App\Models\ProductPurchases;
use App\Models\Products;
use App\Models\purchaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductPurchasesController extends Controller
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
        if (Auth::check() && isCashier()) {
            $purchase_number = 10001;
            $p_no = ProductPurchases::orderBy('id', 'DESC')->limit(1)->get();
            if ($p_no && $p_no->count() > 0) {
                $purchase_number = $p_no[0]->purshace_no+1;
            }

            (float)$total = $request->has('total')? sanitize($request->input('total')) : 0;
            $product_count = sanitize($request->input('product_count'));
            $qty = 0;
            (float)$cbm_price = $request->has('cbm_price')? sanitize($request->input('cbm_price')) : 0;
            (float)$currency = $request->has('currency')? sanitize($request->input('currency')) : 'USD';
            (float)$shipping_charge = $request->has('shipping_charge')? sanitize($request->input('shipping_charge')) : 0;
            (float)$total_in_currency = 0;
            $supplier = $request->has('supplier')? sanitize($request->input('supplier')) : 'other';
            $note = sanitize($request->input('note'));
            $status = $request->has('status')? sanitize($request->input('status')) : 'pending';

            if (!is_numeric($total)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Total")));
            } elseif (!is_numeric($shipping_charge)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Shippiing Charge")));
            }elseif (!is_numeric($cbm_price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For CBM Price")));
            }

            if ($supplier != "other") {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Supplier")));
            }

            $product = [];

            for ($i=0; $i < $product_count; $i++) {
                if (!empty(sanitize($request->input('product_'.$i)))) {
                    if (!is_numeric(sanitize($request->input('price_'.$i)))) {
                        return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price", 'product'=>$i+1)));
                        break;
                    }

                    if (!is_numeric(sanitize($request->input('qty_'.$i)))) {
                        return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Quantity", 'product'=>$i)));
                        break;
                    }

                    $temp = Products::where('id', sanitize($request->input('product_'.$i)))->first();
                    if ($temp != null) {
                        $total_in_currency += sanitize($request->input('price_'.$i)) * sanitize($request->input('qty_'.$i));
                        $qty += sanitize($request->input('qty_'.$i));
                        $product[] = [
                            "name" => $temp->pro_name,
                            "id" => $temp->id,
                            "sku" => $temp->sku,
                            "price" => sanitize($request->input('price_'.$i)),
                            "qty" => sanitize($request->input('qty_'.$i)),
                        ];
                    }
                }
            }

            $purchase = new ProductPurchases();
            $purchase->purshace_no = $purchase_number;
            $purchase->products = json_encode($product);
            $purchase->total = $total;
            $purchase->pending = $total;
            $purchase->qty = $qty;
            $purchase->cbm_price = $cbm_price;
            $purchase->currency = $currency;
            $purchase->total_in_currency = $total_in_currency;
            $purchase->shipping_charge = $shipping_charge;
            $purchase->supplier_id = $supplier=='other'? '0' : $supplier;
            $purchase->status = $status;
            $purchase->note = $note;

            if ($purchase->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Product Purchase Added Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductPurchases $productPurchases)
    {
        //
    }

    public function report($id)
    {
        if (!Auth::check() || !isAdmin()) {
            return redirect('/signin');
        }

        $purchase = ProductPurchases::where('purshace_no', sanitize($id))->get();

        if ($purchase && $purchase->count() > 0) {
            return view('pos.add-productPurchaseReport')->with('purchase', $purchase[0]);
        } else {
            return display404();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (!Auth::check() || !isAdmin()) {
            return redirect('/signin');
        }

        $purchase = ProductPurchases::where('purshace_no', sanitize($id))->get();

        if ($purchase && $purchase->count() > 0) {
            return view('pos.add-productPurchase')->with('purchase', $purchase[0]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductPurchases $purchases)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            (float)$total = $request->has('total')? sanitize($request->input('total')) : 0;
            $product_count = sanitize($request->input('product_count'));
            $qty = 0;
            (float)$cbm_price = $request->has('cbm_price')? sanitize($request->input('cbm_price')) : 0;
            (float)$currency = $request->has('currency')? sanitize($request->input('currency')) : 'USD';
            (float)$shipping_charge = $request->has('shipping_charge')? sanitize($request->input('shipping_charge')) : 0;
            (float)$total_in_currency = 0;
            $supplier = $request->has('supplier')? sanitize($request->input('supplier')) : 'other';
            $note = sanitize($request->input('note'));
            $status = $request->has('status')? sanitize($request->input('status')) : 'pending';

            if (!is_numeric($total)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Total")));
            } elseif (!is_numeric($shipping_charge)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Shippiing Charge")));
            }elseif (!is_numeric($cbm_price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For CBM Price")));
            }

            if ($supplier != "other") {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Supplier")));
            }

            $product = [];

            for ($i=0; $i < $product_count; $i++) {
                if (!empty(sanitize($request->input('product_'.$i)))) {
                    if (!is_numeric(sanitize($request->input('price_'.$i)))) {
                        return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price", 'product'=>$i+1)));
                        break;
                    }

                    if (!is_numeric(sanitize($request->input('qty_'.$i)))) {
                        return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Quantity", 'product'=>$i)));
                        break;
                    }

                    $temp = Products::where('id', sanitize($request->input('product_'.$i)))->first();
                    if ($temp != null) {
                        $total_in_currency += sanitize($request->input('price_'.$i)) * sanitize($request->input('qty_'.$i));
                        $qty += sanitize($request->input('qty_'.$i));
                        $product[] = [
                            "name" => $temp->pro_name,
                            "id" => $temp->id,
                            "sku" => $temp->sku,
                            "price" => sanitize($request->input('price_'.$i)),
                            "qty" => sanitize($request->input('qty_'.$i)),
                        ];
                    }
                }
            }


            $purchase = ProductPurchases::where('id', $id)->update([
                "products" => json_encode($product),
                "total" => $total,
                "qty" => $qty,
                "cbm_price" => $cbm_price,
                "currency" => $currency,
                "total_in_currency" => $total_in_currency,
                "shipping_charge" => $shipping_charge,
                "supplier_id" => $supplier=='other'? '0' : $supplier,
                "note" => $note,
                "status" => $status,
            ]);

            if ($purchase) {
                return response(json_encode(array("error" => 0, "msg" => "Product Purchase Updated Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    public function updateStock(Request $request, ProductPurchases $purchases)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('sku'));
            $purchase_number = sanitize($request->input('purchase_number'));

            $purchase = ProductPurchases::where('id', $purchase_number)->first();

            if ($purchase == null) {
                return response(json_encode(array("error" => 1, "msg" => "Purchase not found")));
            }

            $update = false;

            foreach (json_decode($purchase->products) as $key => $value) {
                if ($value->id == $id) {
                    $product = Products::where('id', $id)->first();

                    if ($product == null) {
                        return response(json_encode(array("error" => 1, "msg" => "Product not found")));
                        break;
                    }

                    $update = Products::where('id', $product->id)->update([
                        "qty" => $product->qty + $value->qty,
                    ]);
                }
            }

            if ($update) {
                return response(json_encode(array("error" => 0, "msg" => "Product Stock Updated Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    public function pay(Request $request, ProductPurchases $purchases)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('id'));
            $amount = sanitize($request->input('amount'));

            if (!is_numeric($amount)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers on amount")));
            }

            $purchase = ProductPurchases::where('id', $id)->first();

            if ($purchase == null) {
                return response(json_encode(array("error" => 1, "msg" => "Purchase not found")));
            }

            $update = ProductPurchases::where('id', $id)->update([
                "pending" => ($purchase->pending - $amount) > 0? $purchase->pending - $amount : 0 ,
            ]);

            if ($update) {
                $transaction = new purchaseTransactions();
                $transaction->amount = ($purchase->pending - $amount) > 0? $amount : $purchase->pending;
                $transaction->note = 'Paid by admin';
                $transaction->purchase_id = $purchase->id;
                $transaction->save();

                return response(json_encode(array("error" => 0, "msg" => "Purchase Paid Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong, please try again later")));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ProductPurchases $purchases)
    {
        if (Auth::check() && DashboardController::check()) {
            $id = sanitize($request->input('code'));
            $verify = ProductPurchases::where('id', $id);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Purchase deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Purchase not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }
}
