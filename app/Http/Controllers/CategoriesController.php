<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getCategories()
    {
        $response = [];
        if (PosDataController::check()) {
            return response(json_encode(Categories::where('pos_code', PosDataController::company()->pos_code)->get()));
        } else {
            $response['error'] = 1;
            $response['msg'] = "not_logged_in";
            return response(json_encode($response));
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
        if (Auth::check() && DashboardController::check(true)) {
            $name = sanitize($request->input('name'));
            $imageName = "placeholder.svg";

            if (empty($name)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            if ($request->hasFile('product_image')) {
                $extension = $request->file('product_image')->getClientOriginalExtension();
                if (in_array($extension, array('png', 'jpeg', 'jpg'))) {
                    $imageName = time() . rand(1111, 999999) . '.' . $request->product_image->extension();
                    $request->product_image->move(env('APP_ENV')=='production'? '/var/www/image.nmsware.com/categories/' : public_path('assets/images/categories'), $imageName);
                } else {
                    return response(json_encode(array("error" => 1, "msg" => "Please select 'png', 'jpeg', or 'jpg' type image")));
                }
            }

            $category = new Categories();
            $category->category_name = $name;
            $category->image = $imageName;
            $category->pos_code = company()->pos_code;

            if ($category->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Category Created Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Categories $categories)
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

        $category = Categories::where('id', sanitize($id))->where('pos_code', company()->pos_code)->get();

        if ($category && $category->count() > 0) {
            return view('pos.add-category')->with('category', $category[0]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categories $categories)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $name = sanitize($request->input('name'));
            $imageName = "placeholder.svg";

            if (empty($name)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            $id_verify = Categories::where('id', $id)->where('pos_code', company()->pos_code)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            if ($request->hasFile('product_image')) {
                $extension = $request->file('product_image')->getClientOriginalExtension();
                if (in_array($extension, array('png', 'jpeg', 'jpg'))) {
                    $imageName = time() . rand(1111, 999999) . '.' . $request->product_image->extension();
                    $request->product_image->move(env('APP_ENV')=='production'? '/var/www/image.nmsware.com/categories/' : public_path('assets/images/categories'), $imageName);
                } else {
                    return response(json_encode(array("error" => 1, "msg" => "Please select 'png', 'jpeg', or 'jpg' type image")));
                }
            }

            $category = '';

            if ($request->hasFile('product_image')) {
                $category = Categories::where('id', $id)->update([
                    "category_name" => $name,
                    "image" => $imageName,
                ]);
            } else {
                $category = Categories::where('id', $id)->update([
                    "category_name" => $name,
                ]);
            }

            if ($category) {
                return response(json_encode(array("error" => 0, "msg" => "Category Updated Successfully")));
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
            $id = sanitize($request->input('id'));
            $verify = Categories::where('id', $id)->where('pos_code', company()->pos_code);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Category deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Category not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }
}
