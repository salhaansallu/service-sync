<?php

namespace App\Http\Controllers;

use App\Models\favourits;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getSpares()
    {
        $response = [];
        if (PosDataController::check()) {
            return response(json_encode(Products::where('pos_code', PosDataController::company()->pos_code)->get()));
        } else {
            $response['error'] = 1;
            $response['msg'] = "not_logged_in";
            return response(json_encode($response));
        }
    }

    public function getFavourits()
    {

        if (PosDataController::check()) {
            $fav = favourits::where('user_id', Auth::user()->id)->where('pos_code', PosDataController::company()->pos_code)->get();
            return response(json_encode($fav));
        }
        return response(json_encode(array('error' => 1, 'msg' => 'not_logged_in')));
    }

    public function addFavourits(Request $request)
    {
        if (PosDataController::check()) {
            $sku = sanitize($request->input('pro_sku'));
            if (empty($sku)) {
                return response(json_encode(array('error' => 1, 'msg' => 'Error while adding to favourits')));
            }
            $verify = favourits::where('user_id', Auth::user()->id)->where('pos_code', PosDataController::company()->pos_code)->where('sku', $sku)->get();
            if ($verify->count() == 0) {
                $fav = new favourits();
                $fav->sku = $sku;
                $fav->pos_code = PosDataController::company()->pos_code;
                $fav->user_id = Auth::user()->id;

                if ($fav->save()) {
                    return response(json_encode(array('error' => 0)));
                } else {
                    return response(json_encode(array('error' => 1, 'msg' => 'Error while adding to favourits')));
                }
            }
            return response(json_encode(array('msg' => 'Product already added')));
        }
        return response(json_encode(array('error' => 1, 'msg' => 'not_logged_in')));
    }

    public function stockReport(Request $request)
    {
        if (DashboardController::check()) {
            $stock = Products::sum('qty');
            $items = Products::count();
            $costs = Products::sum('cost');
            return view('pos.stock-report', ['stock'=>$stock, 'items'=>$items, 'costs' => $costs]);
        }

        return redirect('/signin');
    }

    public function removeFavourits(Request $request)
    {
        if (PosDataController::check()) {
            $sku = sanitize($request->input('pro_sku'));

            if (empty($sku)) {
                return response(json_encode(array('error' => 1, 'msg' => 'Error while deleting to favourits')));
            }

            $verify = favourits::where('user_id', Auth::user()->id)->where('pos_code', PosDataController::company()->pos_code)->where('sku', $sku);
            if ($verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array('error' => 0)));
                } else {
                    return response(json_encode(array('error' => 1, 'msg' => 'Error while deleting to favourits')));
                }
            }
            return response(json_encode(array('msg' => 'No product found')));
        }
        return response(json_encode(array('error' => 1, 'msg' => 'not_logged_in')));
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
            $name = sanitize($request->input('name'));
            $code = sanitize($request->input('code'));
            $code = str_replace(' ', '', $code);
            //$category = sanitize($request->input('category'));
            (float)$cost = sanitize($request->input('cost'));
            (float)$price = sanitize($request->input('price'));
            //(float)$price = sanitize($request->input('price'));
            (float)$stock = sanitize($request->input('stock'));
            $supplier = sanitize($request->input('supplier'));
            $imageName = "placeholder.svg";

            $code_verify = Products::where('sku', $code)->where('pos_code', company()->pos_code)->get();

            if ($code_verify && $code_verify->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Product Code Already Exists")));
            } elseif (!is_numeric($cost)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Cost")));
            } elseif (!is_numeric($stock)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Stock")));
            }elseif (!is_numeric($price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price")));
            } elseif (empty($name) || empty($code) || empty($cost) || empty($stock) || empty($supplier)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            // if ($category != "other" && getCategory($category)->pos_code != company()->pos_code) {
            //     return response(json_encode(array("error" => 1, "msg" => "Invalid Category")));
            // }

            if ($supplier != "other" && getSupplier($supplier)->pos_code != company()->pos_code) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Supplier")));
            }

            if ($request->hasFile('product_image')) {
                $extension = $request->file('product_image')->getClientOriginalExtension();
                if (in_array($extension, array('png', 'jpeg', 'jpg'))) {
                    $imageName = time() . str_replace(' ', '', $code) . '.' . $request->product_image->extension();
                    $request->product_image->move(public_path('assets/images/products'), $imageName);
                } else {
                    return response(json_encode(array("error" => 1, "msg" => "Please select 'png', 'jpeg', or 'jpg' type image")));
                }
            }

            $product = new Products();
            $product->pro_name = $name;
            $product->sku = $code;
            $product->cost = $cost;
            $product->price = $price;
            $product->qty = $stock;
            $product->pro_image = $imageName;
            $product->pos_code = company()->pos_code;
            $product->supplier = $supplier;

            if ($product->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Product Created Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong, please try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Products $products)
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

        $product = Products::where('sku', sanitize($id))->where('pos_code', company()->pos_code)->get();

        if ($product && $product->count() > 0) {
            return view('pos.add-product')->with('product', $product[0]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Products $products)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $name = sanitize($request->input('name'));
            $code = sanitize($request->input('code'));
            $code = str_replace(' ', '', $code);
            //$category = sanitize($request->input('category'));
            (float)$cost = sanitize($request->input('cost'));
            (float)$price = sanitize($request->input('price'));
            (float)$stock = sanitize($request->input('stock'));
            $supplier = sanitize($request->input('supplier'));
            $imageName = "placeholder.svg";

            $id_verify = Products::where('id', $id)->where('pos_code', company()->pos_code)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            $code_verify = Products::where('sku', $code)->where('pos_code', company()->pos_code)->where('id', '!=', $id)->get();

            if ($code_verify && $code_verify->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Product Code Already Exists")));
            } elseif (!is_numeric($cost)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Cost")));
            } elseif (!is_numeric($stock)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Stock")));
            } elseif (empty($name) || empty($code) || empty($cost) || empty($stock) || empty($supplier)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked As '*'")));
            }

            if ($request->hasFile('product_image')) {
                $extension = $request->file('product_image')->getClientOriginalExtension();
                if (in_array($extension, array('png', 'jpeg', 'jpg'))) {
                    $imageName = time() . str_replace(' ', '', $code) . '.' . $request->product_image->extension();
                    $request->product_image->move(env('APP_ENV')=='production'? '/var/www/image.nmsware.com/products/' : public_path('assets/images/products'), $imageName);
                } else {
                    return response(json_encode(array("error" => 1, "msg" => "Please select 'png', 'jpeg', or 'jpg' type image")));
                }
            }

            // if ($category != "other" && getCategory($category)->pos_code != company()->pos_code) {
            //     return response(json_encode(array("error" => 1, "msg" => "Invalid Category")));
            // }

            if ($supplier != "other" && getSupplier($supplier)->pos_code != company()->pos_code) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Supplier")));
            }

            $product = '';

            if ($request->hasFile('product_image')) {
                $product = Products::where('id', $id)->update([
                    "pro_name" => $name,
                    "sku" => $code,
                    "cost" => $cost,
                    "price" => $price,
                    "qty" => $stock,
                    "pro_image" => $imageName,
                    "supplier" => $supplier,
                ]);
            } else {
                $product = Products::where('id', $id)->update([
                    "pro_name" => $name,
                    "sku" => $code,
                    "cost" => $cost,
                    "price" => $price,
                    "qty" => $stock,
                    "supplier" => $supplier,
                ]);
            }

            if ($product) {
                return response(json_encode(array("error" => 0, "msg" => "Product Updated Successfully", 'sku' => $code)));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $sku = sanitize($request->input('sku'));
            $verify = Products::where('sku', $sku)->where('pos_code', company()->pos_code);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Product deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Product not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }
}
