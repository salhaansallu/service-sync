<?php

namespace App\Http\Controllers;

use App\Models\partners;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PartnersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getPartners()
    {
        $response = [];
        if (PosDataController::check()) {
            return response(json_encode(partners::where('pos_code', PosDataController::company()->pos_code)->get()));
        }
        else {
            $response ['error'] = 1;
            $response ['msg'] = "not_logged_in";
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
            $company = sanitize($request->input('company'));
            $phone = sanitize($request->input('phone'));
            $address = sanitize($request->input('address'));
            $email = sanitize($request->input('email'));
            $username = sanitize($request->input('username'));
            $password = sanitize($request->input('password'));

            if (empty($name)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }
            if (!empty($phone) && !is_numeric($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for phone number")));
            }

            if (!empty($email) && partners::where('email', $email)->where('pos_code', company()->pos_code)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Email already in use")));
            }

            if (!empty($username) && partners::where('username', $username)->where('pos_code', company()->pos_code)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Username already in use")));
            }

            if (!empty($phone) && partners::where('phone', $phone)->where('pos_code', company()->pos_code)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Phone number already in use")));
            }

            $partner = new partners();
            $partner->name = $name;
            $partner->company = $company;
            $partner->phone = $phone;
            $partner->address = $address;
            $partner->email = $email;
            $partner->username = $username;
            if (!empty($password)) {
                $partner->password = Hash::make($password);
            }
            $partner->pos_code = company()->pos_code;

            if ($partner->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Partner Created Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(partners $partners)
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

        $customer = partners::where('id', sanitize($id))->where('pos_code', company()->pos_code)->get();

        if ($customer && $customer->count() > 0) {
            return view('pos.add-partner')->with('user', $customer[0]);
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
            $company = sanitize($request->input('company'));
            $phone = sanitize($request->input('phone'));
            $address = sanitize($request->input('address'));
            $email = sanitize($request->input('email'));
            $username = sanitize($request->input('username'));
            $password = sanitize($request->input('password'));

            if (empty($name)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            if (!empty($phone) && !is_numeric($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for phone number")));
            }

            $id_verify = partners::where('id', $id)->where('pos_code', company()->pos_code)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            if (!empty($email) && partners::where('email', $email)->where('pos_code', company()->pos_code)->where('id', "!=", $id)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Email already in use")));
            }

            if (!empty($username) && partners::where('username', $username)->where('pos_code', company()->pos_code)->where('id', "!=", $id)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Username already in use")));
            }

            if (!empty($phone) && partners::where('phone', $phone)->where('pos_code', company()->pos_code)->where('id', "!=", $id)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Phone number already in use")));
            }

            $update_arr = [
                "name" => $name,
                "company" => $company,
                "phone" => $phone,
                "address" => $address,
                "email" => $email,
                "username" => $username,
            ];

            if (!empty($password)) {
                $update_arr["password"] = Hash::make($password);
            }

            $customer = partners::where('id', $id)->update($update_arr);

            if ($customer) {
                return response(json_encode(array("error" => 0, "msg" => "Partner Updated Successfully")));
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
            $verify = partners::where('id', $id)->where('pos_code', company()->pos_code);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Partner deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Partner not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }
}
