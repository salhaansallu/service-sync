<?php

namespace App\Http\Controllers;

use App\Models\partners;
use App\Models\Products;
use App\Models\Repairs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class PartnersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->is_partner()) {
            $repairs["all"] = Repairs::where('partner', partner()->id)->count();
            $repairs["pending"] = Repairs::where('partner', partner()->id)->where('status', 'Pending')->count();
            $repairs["repaired"] = Repairs::where('partner', partner()->id)->where('status', 'Repaired')->count();
            $repairs["delivered"] = Repairs::where('partner', partner()->id)->where('status', 'Delivered')->count();

            $billNumbers = [];

            foreach (Repairs::where('partner', partner()->id)->where('status', 'Delivered')->get() as $key => $value) {
                $billNumbers[] = $value->bill_no;
            }

            $payments["pending"] =  DB::table('credits')
                ->whereExists(function ($query) use ($billNumbers) {
                    $query->select(DB::raw(1))
                        ->from('repairs')
                        ->whereRaw('FIND_IN_SET(repairs.bill_no, credits.order_id) > 0')
                        ->whereIn('repairs.bill_no', $billNumbers);
                })
                ->sum('credits.ammount');

            $finishedRepairs = Repairs::where('partner', partner()->id)->where('status', 'Repaired')->limit(5)->get();

            return view('partner-portal.dashboard')->with(['repairs' => (object)$repairs, 'payments' => (object)$payments, 'finishedRepairs' => $finishedRepairs]);
        }
        return redirect()->route('partnerLogin');
    }

    public function getPartners()
    {
        $response = [];
        if (PosDataController::check()) {
            return response(json_encode(partners::where('pos_code', PosDataController::company()->pos_code)->get()));
        } else {
            $response['error'] = 1;
            $response['msg'] = "not_logged_in";
            return response(json_encode($response));
        }
    }

    public static function is_partner($get_details = false)
    {
        if (Session::has('WeFixPartner')) {
            $token = Session::get('WeFixPartner');
            if (!empty($token)) {
                $token = json_decode(Crypt::decrypt($token));
                if (is_object($token)) {
                    if (isset($token->id) && isset($token->user)) {
                        $verify = partners::where('username', '=', sanitize($token->user))->where('id', '=', sanitize($token->id))->limit(1)->get();
                        if ($verify->count() == 1) {
                            if ($get_details) {
                                return (object)array(
                                    "id" => $verify[0]->id,
                                    "name" => $verify[0]->name,
                                    "company" => $verify[0]->company,
                                    "phone" => $verify[0]->phone,
                                    "address" => $verify[0]->address,
                                    "email" => $verify[0]->email,
                                    "username" => $verify[0]->username,
                                    "pos_code" => $verify[0]->pos_code,
                                    "created_at" => $verify[0]->created_at,
                                    "updated_at" => $verify[0]->updated_at,
                                );
                            }
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public static function getPartnerDetails()
    {
        return self::is_partner(true);
    }

    public static function login(Request $request)
    {
        $verify = partners::where('username', '=', sanitize($request->input('email')))->orWhere('email', '=', sanitize($request->input('email')))->limit(1)->get();
        if ($verify->count() == 1) {
            if (Hash::check(sanitize($request->input('password')), $verify[0]->password)) {
                Session::put('WeFixPartner', Crypt::encrypt(json_encode(array('user' => $verify[0]->username, 'id' => $verify[0]->id))));
                return redirect()->route('partnerDashboard');
            }
        }

        $error = array(
            "email" => sanitize($request->input('email')),
            "message" => "Invalid login credincials"
        );

        return view('partner-portal.login')->with('error', $error);;
    }

    public static function showLogin()
    {
        if (!self::is_partner()) {
            return view('partner-portal.login');
        }

        return redirect()->route('partnerDashboard');
    }

    public function listRepairs()
    {
        if (self::is_partner()) {
            $status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
            $repairs = [];
            if (!empty($status) && in_array(ucfirst(str_replace('-', ' ', $status)), ['Repaired', 'Pending', 'Delivered', 'Customer Pending', 'Awaiting Parts', 'Return'])) {
                $repairs = Repairs::where('partner', partner()->id)->where('status', ucfirst(str_replace('-', ' ', $status)))->get();
            } else {
                $repairs = Repairs::where('partner', partner()->id)->where('status', 'Repaired')->get();
            }

            return view('partner-portal.list-repairs')->with(['repairs' => $repairs]);
        }
        return redirect()->route('partnerLogin');
    }

    public function displayRepair($id)
    {
        if (self::is_partner()) {
            $id = sanitize($id);

            if (!empty($id)) {
                $repairs = Repairs::where('partner', partner()->id)->where('id', $id)->get();
                if ($repairs->count() > 0) {
                    $spares = Products::where('pos_code', partner()->pos_code)->get();
                    return view('partner-portal.add-repairs')->with(['repairs' => $repairs[0], 'spares' => $spares]);
                }
            }
            return display404();
        }
        return redirect()->route('partnerLogin');
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
