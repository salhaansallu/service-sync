<?php

namespace App\Http\Controllers;

use App\Models\POSSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class POSSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public static function settings($pos_code) {
        $settings = POSSettings::where('pos_code', $pos_code);
        if ($settings->first() && $settings->count() > 0) {
            return $settings->first();
        }
        else {
            return defaultValues();
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
    public function show(POSSettings $pOSSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(POSSettings $pOSSettings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $updatearr = [];
        if (Auth::check() && DashboardController::check(true)) {
            if ($request->has('qr_code') && sanitize($request->input('qr_code')) == 'on') {

                $updatearr['qr_code'] = "active";
            }
            else {
                $updatearr['qr_code'] = "unactive";
            }

            if ($request->has('datetime') && sanitize($request->input('datetime')) == 'on') {
                $updatearr['datetime'] = "active";
            }
            else {
                $updatearr['datetime'] = "unactive";
            }

            if ($request->has('industry') && sanitize($request->input('industry')) == 'on') {
                $updatearr['industry'] = "active";
            }
            else {
                $updatearr['industry'] = "unactive";
            }

            if ($request->has('title') && sanitize($request->input('title')) == 'on') {
                $updatearr['title'] = "active";
            }
            else {
                $updatearr['title'] = "unactive";
            }
            
            if (count($updatearr) > 0) {
                $settings = POSSettings::where('pos_code', company()->pos_code);
                if ($settings->first() && $settings->count() > 0) {
                    $settings->update($updatearr);
                    return response(json_encode(array("error" => 0, "msg" => "Settings Updated")));
                }
                else {
                    $updatearr['pos_code'] = company()->pos_code;

                    POSSettings::insert($updatearr);
                    return response(json_encode(array("error" => 0, "msg" => "Settings Updated")));
                }
            }
            else {
                return response(json_encode(array("error" => 1, "msg" => "Error while updating settings")));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(POSSettings $pOSSettings)
    {
        //
    }
}
