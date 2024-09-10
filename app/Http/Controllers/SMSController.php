<?php

namespace App\Http\Controllers;

use App\Models\customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SMS;

class SMSController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check() && DashboardController::check(true)) {
            $customers = customers::where('pos_code', company()->pos_code)->get();
            return view('pos.sms')->with(['customers'=>$customers]);
        }

        login_redirect('/dashboard/sms');
        return redirect('/signin');
    }

    public function send(Request $request)
    {
        $contacts = [];

        if (sanitize($request->input('send_to')) == 'all') {
            $customers = customers::where('pos_code', company()->pos_code)->get();
            foreach ($customers as $key => $customer) {
                $contacts[] = array(
                    "fname" => "",
                    "lname" => "",
                    "number" => $customer["phone"],
                    "group" => "",
                    "email" => "",
                );
            }
        }

        if (sanitize($request->input('send_to')) == 'induvidual') {
            foreach (json_decode($request->input('contacts')) as $key => $customerData) {
                $contacts[] = array(
                    "fname" => "",
                    "lname" => "",
                    "number" => $customerData,
                    "group" => "",
                    "email" => "",
                );
            }
        }

        $sms = new SMS();
        $sms->contact = $contacts;
        $sms->message = sanitize($request->input('camp_message'));
        $sms->camp_type = sanitize($request->input('camp_type'));
        $sms->send_at = sanitize($request->input('send_at'));

        return response(json_encode($sms->Send()));
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
