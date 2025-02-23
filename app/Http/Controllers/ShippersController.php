<?php

namespace App\Http\Controllers;

use App\Models\shippers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippersController extends Controller
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
            $name = sanitize($request->input('name'));
            $phone = sanitize($request->input('phone'));

            if (empty($name)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }
            if (!empty($phone) && !is_numeric($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for phone number")));
            }

            if (shippers::where('phone', $phone)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Phone number already in use")));
            }

            $customer = new shippers();
            $customer->company_name = $name;
            $customer->phone = $phone;

            if ($customer->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Shipper Created Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(shippers $shippers)
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

        $shipper = shippers::where('id', sanitize($id))->get();

        if ($shipper && $shipper->count() > 0) {
            return view('pos.add-shipper')->with('shipper', $shipper[0]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $name = sanitize($request->input('name'));
            $phone = sanitize($request->input('phone'));

            if (empty($name)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            if (!empty($phone) && !is_numeric($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for phone number")));
            }

            $id_verify = shippers::where('id', $id)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            $customer = shippers::where('id', $id)->update([
                "company_name" => $name,
                "phone" => $phone,
            ]);

            if ($customer) {
                return response(json_encode(array("error" => 0, "msg" => "Shipper Updated Successfully")));
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
            $verify = shippers::where('id', $id);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Shipper deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Shipper not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }
}
