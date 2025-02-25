<?php

namespace App\Http\Controllers;

session_start();

use App\Models\customers;
use App\Models\Repairs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use SMS;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public static $otpSessionName = "asdaT57RYTdsrt466sQ6ipkojlnE57iop54qw";
    public function index()
    {
        //
    }

    public function getCustomers() {
        $response = [];
        if (PosDataController::check()) {
            return response(json_encode(customers::where('pos_code', PosDataController::company()->pos_code)->get()));
        }
        else {
            $response ['error'] = 1;
            $response ['msg'] = "not_logged_in";
            return response(json_encode($response));
        }
    }

    public function sendCode(Request $request) {
        if ($request->has('phone') && preg_match('/^(0|94|\+94)?7\d{8}$/', sanitize($request->input('phone')))) {
            $phone = sanitize($request->input('phone'));
            $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            if (customers::where("phone", $phone)->count() <= 0) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Phone Number")));
            }

            $sessionData = json_encode(["time" => Carbon::now(), "otp" => Hash::make($otp), 'phone' => formatOriginalPhoneNumber($phone)]);

            if ($_SESSION[self::$otpSessionName] = Crypt::encrypt($sessionData)) {
                $sms = new SMS();
                $sms->contact[] = array(
                    "fname" => "",
                    "lname" => "",
                    "number" => formatOriginalPhoneNumber($phone),
                    "group" => "",
                    "email" => "",
                );
                $sms->message = $otp." is your OTP to check your repair list at WeFix.LK. OTP only valid for 5 minutes. \n Do not share with anyone";
                if ($sms->Send()->error == 0) {
                    return response(json_encode(array("error" => 0, "msg" => "OTP sent to phone number ".$phone)));
                }
            }

            return response(json_encode(array("error" => 1, "msg" => "Error sending OTP")));
        }
        return response(json_encode(array("error" => 1, "msg" => "Invalid Phone Number")));
    }

    public function OTPVerify(Request $request) {
        if ($request->has('code') && preg_match('/^\d{4}$/', sanitize($request->input('code')))) {
            $code = sanitize($request->input('code'));

            if (!isset($_SESSION[self::$otpSessionName])) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid OTP")));
            }

            $sessionData = json_decode(Crypt::decrypt($_SESSION[self::$otpSessionName]));

            if ($sessionData) {
                if (Carbon::parse($sessionData->time)->diffInMinutes(Carbon::now()) >= 5) {
                    return response(json_encode(array("error" => 1, "msg" => "OTP code expired. Please Resend")));
                }

                if (Hash::check($code, $sessionData->otp)) {
                    unset($_SESSION[self::$otpSessionName]);
                    $data = customers::where("phone", $sessionData->phone)->get(['phone', 'id']);

                    if ($data->count() > 0) {
                        $repairs = Repairs::where('customer', $data[0]->id)->orderBy('id', 'DESC')->get(['bill_no', 'model_no', 'serial_no', 'fault', 'advance', 'total', 'delivery', 'status', 'invoice', 'created_at']);
                        return response(json_encode(array("error" => 0, "msg" => "OTP verification successful", "repairs"=>$repairs)));
                    }

                    return response(json_encode(array("error" => 1, "msg" => "Customer not found")));
                }
            }
        }

        return response(json_encode(array("error" => 1, "msg" => "Invalid OTP")));
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
            $address = sanitize($request->input('address'));
            $phone = sanitize($request->input('phone'));

            if (empty($name)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }
            if ($phone !=0 && !empty($phone) && !is_numeric($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for phone number")));
            }

            if ($phone !=0 && customers::where('phone', $phone)->where('pos_code', company()->pos_code)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Phone number already in use")));
            }

            $customer = new customers();
            $customer->name = $name;
            $customer->pos_code = company()->pos_code;
            $customer->address = $address;
            $customer->phone = $phone;

            if ($customer->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Customer Created Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(customers $customers)
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

        $customer = customers::where('id', sanitize($id))->where('pos_code', company()->pos_code)->get();

        if ($customer && $customer->count() > 0) {
            return view('pos.add-customer')->with('customer', $customer[0]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, customers $customers)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $name = sanitize($request->input('name'));
            $address = sanitize($request->input('address'));
            $phone = sanitize($request->input('phone'));

            if (empty($name)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            if ($phone !=0 && !empty($phone) && !is_numeric($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for phone number")));
            }

            $id_verify = customers::where('id', $id)->where('pos_code', company()->pos_code)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            $customer = customers::where('id', $id)->update([
                "name" => $name,
                "address" => $address,
                "phone" => $phone,
            ]);

            if ($customer) {
                return response(json_encode(array("error" => 0, "msg" => "Customer Updated Successfully")));
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
            $verify = customers::where('id', $id)->where('pos_code', company()->pos_code);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Customer deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Customer not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }
}
