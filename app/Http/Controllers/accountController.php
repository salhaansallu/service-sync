<?php

namespace App\Http\Controllers;

use App\Models\posData;
use App\Models\PosInvitation;
use App\Models\posUsers;
use App\Models\User;
use App\Models\userData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class accountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        login_redirect('/account/overview');
        if (Auth::check()) {

            $has_dashboard = false;
            $has_pos = false;
            $has_message = false;
            $has_expired = false;
            $admin_code = "";
            $invitation[0] = array();
            $expiry[0] = array();

            $dash = posData::where('admin_id', Auth::user()->id)->where("status", 'active')->get();
            if ($dash && $dash->count() > 0) {
                $has_dashboard = true;
                $admin_code = Crypt::encrypt($dash[0]->pos_code);
                set_Cookie("admin_session", $admin_code);
            }
            
            $pos = posUsers::where("user_id", Auth::user()->id)->get();
            if ($pos && $pos->count() > 0) {
                $has_pos = true;
            }

            $getinvitation = PosInvitation::where("user_id", Auth::user()->id)->where('status', 'pending')->get();
            if ($getinvitation && $getinvitation->count() > 0) {
                $invitation = $getinvitation;
                $has_message = true;
            }

            $check_expired = posData::where("admin_id", Auth::user()->id)->where('status', 'active')->get();
            if ($check_expired && $check_expired->count() > 0 && $check_expired[0]->expiry_date != 'false' && date('Y-m-d h:i:s', strtotime($check_expired[0]->expiry_date)) < date('Y-m-d h:i:s')) {
                $expiry = $check_expired;
                $has_expired = true;
            }

            return view('account.overview')->with(['has_pos'=>$has_pos, 'has_message'=>$has_message, 'has_dashboard'=>$has_dashboard, 'has_expired'=>$has_expired, 'pos'=>$pos, 'admin_code'=>$admin_code, 'invitation'=>$invitation[0], 'expiry'=>$expiry[0]]);

        } else {
            return redirect('/signin');
        }
    }

    public function details()
    {
        login_redirect('/account/details');
        if (Auth::check()) {
            $company_info = posData::where('admin_id', Auth::user()->id)->get();
            $user_info = userData::where('user_id', Auth::user()->id)->get();
            return view('account.details')->with(['company' => $company_info, 'user_info' => $user_info]);
        } else {
            return redirect('/signin');
        }
    }

    public function logout(Request $request)
    {
        set_Cookie('pos_session', '');
        set_Cookie("admin_session", '');
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function updateDetails(Request $request)
    {

        $fname = sanitize($request->input('fname'));
        $lname = sanitize($request->input('lname'));
        $email = sanitize($request->input('email'));

        if (ctype_alpha(str_replace(' ', '', $fname)) === false) {
            return response(json_encode(array("error" => 1, 'msg' => "Invalid First Name format")));
        }
        if (ctype_alpha(str_replace(' ', '', $lname)) === false) {
            return response(json_encode(array("error" => 1, 'msg' => "Invalid Last Name format")));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response(json_encode(array("error" => 1, 'msg' => "Invalid Email format")));
        }
        
        $verify_email = User::where('email', $email)->where('id', '!=', Auth::user()->id)->get();
        if ($verify_email && $verify_email->count() > 0) {
            return response(json_encode(array("error" => 1, 'msg' => "Email already available")));
        }

        User::where('id', Auth::user()->id)->update([
            "fname" => $fname,
            "lname" => $lname,
            "email" => $email,
        ]);

        if ($request->has("company_name")) {
            $cname = sanitize($request->input("company_name"));
            $industry = sanitize($request->input("industry"));
            $country = sanitize($request->input("country"));
            $city = sanitize($request->input("city"));

            if (empty($cname)) {
                return response(json_encode(array("error" => 1, 'msg' => "Company name  is required")));
            }
            if (empty($industry)) {
                return response(json_encode(array("error" => 1, 'msg' => "Industry  is required")));
            }
            if (empty($country)) {
                return response(json_encode(array("error" => 1, 'msg' => "Country  is required")));
            }
            if (empty($city)) {
                return response(json_encode(array("error" => 1, 'msg' => "City  is required")));
            }

            posData::where("admin_id", Auth::user()->id)->update([
                "company_name" => $cname,
                "industry" => $industry,
                "country" => $country,
                "city" => $city
            ]);
        }

        if ($request->has("phone")) {
            $phone = sanitize($request->input("phone"));

            if (empty($phone) || !is_numeric(str_replace(" ", "", str_replace("+", "", $phone))) || strlen(str_replace(" ", "", str_replace("+", "", $phone))) > 15) {
                return response(json_encode(array("error" => 1, 'msg' => "Invalid or empty phone number")));
            }

            userData::where("user_id", Auth::user()->id)->update([
                "phone" => $phone,
            ]);
        }
        

        return response(json_encode(array("error" => 0, 'msg' => "Successfully updated")));
    }

    public function updatePassword(Request $request) {
        $oldpass = sanitize($request->input('oldpass'));
        $newpass = sanitize($request->input('newpass'));
        $confirmpass = sanitize($request->input('confirmpass'));

        if ($newpass == $confirmpass) {
            if (Hash::check($oldpass, Auth::user()->password)) {
                User::where('id', Auth::user()->id)->update([
                    "password" => Hash::make($newpass),
                ]);

                return response(json_encode(array("error" => 0, 'msg' => "Successfully updated")));
            }
            else {
                return response(json_encode(array("error" => 1, 'msg' => "Invalid Old Password")));
            }
        }
        else {
            return response(json_encode(array("error" => 1, 'msg' => "Passwords doesn't match")));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
