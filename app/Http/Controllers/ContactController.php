<?php

namespace App\Http\Controllers;

use App\Models\contact;
use App\Models\posData;
use App\Models\userData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        login_redirect('/contact');
        $user = array();
        if (Auth::check()) {
            $companydata = posData::where('admin_id', Auth::user()->id)->get();
            if ($companydata->count() > 0) {
                $user["company_name"] = $companydata[0]->company_name;
                $user["industry"] = $companydata[0]->industry;
                $user["country"] = $companydata[0]->country;
                $user["city"] = $companydata[0]->city;
            } else {
                $user["company_name"] = "";
                $user["industry"] = "";
                $user["country"] = "";
                $user["city"] = "";
            }

            $userdata = userData::where('user_id', Auth::user()->id)->get();
            if ($userdata->count() > 0) {
                $user["phone"] = $userdata[0]->phone;
            } else {
                $user["phone"] = "";
            }

            $user["fname"] = Auth::user()->fname;
            $user["lname"] = Auth::user()->lname;
            $user["email"] = Auth::user()->email;

            return view('contact')->with('userData', $user);
        }
        return view('contact');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $error = array();
        $captcha = captchaVerify(sanitize($request->input('cf-turnstile-response')));
        if ($captcha->error) {
            $error = array(
                "error" => 1,
                "msg" => $captcha->msg
            );
        }
        else {
            $validator = Validator::make($request->all(), [
                'fname' => 'required',
                'lname' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'message' => 'required',
            ]);
    
            if ($validator->fails()) {
                $error = array(
                    "error" => 1,
                    "msg" => "Please fill all required fields"
                );
            } else {
                $message = new contact();
                $message->fname = sanitize($request->input('fname'));
                $message->lname = sanitize($request->input('lname'));
                $message->email = sanitize($request->input('email'));
                $message->phone = sanitize($request->input('phone'));
                $message->company_name = sanitize($request->input('company_name'));
                $message->industry = sanitize($request->input('industry'));
                $message->country = sanitize($request->input('country'));
                $message->city = sanitize($request->input('city'));
                $message->message = sanitize($request->input('message'));
                $message->calls = sanitize($request->input('calls'));
                $message->user_id = Auth::check() ? Auth::user()->id : "";
    
                if ($message->save()) {
                    $error = array(
                        "error" => 0,
                        "msg" => "Message sent sucessfully"
                    );
                } else {
                    $error = array(
                        "error" => 1,
                        "msg" => "Sorry! something went wrong"
                    );
                }
            }
        }

        return response(json_encode($error));
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
    public function show(contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(contact $contact)
    {
        //
    }
}
