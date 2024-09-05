<?php

namespace App\Http\Controllers;

use App\Models\posData;
use App\Models\PosInvitation;
use App\Models\posUsers;
use App\Models\User;
use App\Models\userData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public static function user()
    {
        if (Auth::check()) {
            $details = userData::where('user_id', Auth::user()->id)->get();
            if ($details && $details->count() > 0) {
                return $details[0];
            }
            return defaultValues();
        }
        return defaultValues();
    }

    public function index()
    {
        login_redirect('/create-account');
        if (Auth::check()) {
            $validate_user = posData::where('admin_id', '=', Auth::user()->id)->get()->count();
            if ($validate_user == 0) {
                return view('auth.register_info');
            }
            else {
                return redirect('/pricing');
            }
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
        $response = array();

        if (Auth::check()) {
            $request = $request->input('params');
            
            foreach ($request as $val) {
                if (empty($val)) {
                    return array("error"=>1, "msg"=>"All fields are required");
                }
            }

            $user_update = User::where('id', "=", Auth::user()->id)->update([
                "fname" => sanitize($request['fname']),
                "lname" => sanitize($request['lname']),
            ]);

            $validate_user = userData::where('user_id', '=', Auth::user()->id);
            if ($validate_user->get()->count() > 0) {
                $validate_user->update([
                    "country" => sanitize($request['country']),
                    "address" => sanitize($request['address']),
                    "city" => sanitize($request['city']),
                    "zip" => sanitize($request['zip']),
                    "phone" => sanitize($request['phone']),
                ]);
            }
            else {
                userData::insert([
                    "country" => sanitize($request['country']),
                    "address" => sanitize($request['address']),
                    "city" => sanitize($request['city']),
                    "zip" => sanitize($request['zip']),
                    "phone" => sanitize($request['phone']),
                    "user_id" => Auth::user()->id,
                ]);
            }

            $validate_company = posData::where('admin_id', '=', Auth::user()->id);
            if ($validate_company->count() > 0) {
                $validate_company->update([
                    "company_name" => sanitize($request['cname']),
                    "industry" => sanitize($request['industry']),
                    "country" => sanitize($request['ccountry']),
                    "city" => sanitize($request['ccity']),
                ]);
            }
            else {
                $pos_code = rand(1000000, 9999999999).date('dmYhis').rand(1000, 9999);
                posData::insert([
                    "pos_code" => $pos_code,  
                    "admin_id" => Auth::user()->id,
                    "company_name" => sanitize($request['cname']),
                    "industry" => sanitize($request['industry']),
                    "country" => sanitize($request['ccountry']),
                    "city" => sanitize($request['ccity']),
                    "plan" => "1",
                    "status" => "active",
                    "expiry_date" => 'false'
                ]);
                posUsers::insert([
                    "user_id" => Auth::user()->id,
                    "pos_code" => $pos_code
                ]);
            }

            $response = array(
                "error" => 0,
            );
        }
        else {
            $response = array(
                "error" => 1,
                "msg" => "Sorry! something went wrong"
            );
        }

        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(userData $userData)
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

        $user = DB::table('users')->select('*')->leftJoin('pos_users', 'users.id', '=', 'pos_users.user_id')->where('pos_code', company()->pos_code)->where('pos_users.user_id', sanitize($id))->get();

        if ($user && $user->count() > 0) {
            return view('pos.add-users')->with('user', $user[0]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, userData $userData)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $fname = sanitize($request->input('fname'));
            $lname = sanitize($request->input('lname'));

            if (empty($fname)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            if (ctype_alpha(str_replace(' ', '', $fname)) === false) {
                return response(json_encode(array("error" => 1, "msg" => "Please Enter A Valid First Name")));
            }

            if (!empty($lname) && ctype_alpha(str_replace(' ', '', $lname)) === false) {
                return response(json_encode(array("error" => 1, "msg" => "Please Enter A Valid Last Name")));
            }

            $id_verify = DB::table('users')->select('*')->leftJoin('pos_users', 'users.id', '=', 'pos_users.user_id')->where('pos_code', company()->pos_code)->where('pos_users.user_id', sanitize($id))->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            $user = User::where('id', $id)->update([
                "fname" => $fname,
                "lname" => $lname,
            ]);

            if ($user) {
                return response(json_encode(array("error" => 0, "msg" => "User Updated Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    public function save(Request $request)
    {
        if (Auth::check() && DashboardController::check()) {
            $fname = sanitize($request->input('fname'));
            $lname = sanitize($request->input('lname'));
            $email = sanitize($request->input('email'));
            $password = sanitize($request->input('password'));

            $email_verify = User::where('email', $email)->get();

            if ($email_verify && $email_verify->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Email already in use. if you need to assign that user, send an invitation from the invitation section")));
            }

            if (empty($fname) || empty($email) || empty($password)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            if (ctype_alpha(str_replace(' ', '', $fname)) === false) {
                return response(json_encode(array("error" => 1, "msg" => "Please Enter A Valid First Name")));
            }

            if (!empty($lname) && ctype_alpha(str_replace(' ', '', $lname)) === false) {
                return response(json_encode(array("error" => 1, "msg" => "Please Enter A Valid Last Name")));
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response(json_encode(array("error" => 1, 'msg' => "Invalid Email format")));
            }

            $user = new User();
            $user->fname = $fname;
            $user->lname = $lname;
            $user->email = $email;
            $user->password = Hash::make($password);

            if ($user->save()) {
                sendInvitation($email);

                $user_id = User::where('email', $email)->get();

                $userData = new posUsers();
                $userData->user_id = $user_id[0]->id;
                $userData->pos_code = company()->pos_code;
                
                if ($userData->save()) {
                    return response(json_encode(array("error" => 0, "msg" => "User Created Successfully")));
                }
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    public function invite(Request $request) {
        if (Auth::check() && DashboardController::check(true)) {
            $email = sanitize($request->input('email'));

            $verify = User::where('email', $email);
            if ($verify && $verify->count() > 0) {
                $invitation = PosInvitation::where('user_id', $verify->get()[0]->id)->where('pos_code', company()->pos_code)->where('status', 'pending')->get();
                if ($invitation && $invitation->count() > 0) {
                    return response(json_encode(array("error" => 1, "msg" => "User Already Invited")));
                }
                if (sendInvitation($email)) {
                    return response(json_encode(array("error" => 0, "msg" => "User Invited")));
                }
            }
            return response(json_encode(array("error" => 1, "msg" => "User Not Found")));
        }
        return response(json_encode(array("error" => 1, "msg" => "Something went wrong")));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('id'));
            $id_verify = posUsers::where('user_id', sanitize($request->input('id')))->where('pos_code', company()->pos_code)->whereNot('user_id', Auth::user()->id);
            if ($id_verify && $id_verify->count() > 0 && $id_verify->delete()) {
                return response(json_encode(array("error" => 0, "msg" => "User terminated successfully")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }   
}
