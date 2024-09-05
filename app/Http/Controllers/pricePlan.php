<?php

namespace App\Http\Controllers;

use App\Models\posData;
use App\Models\pricingPlans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class pricePlan extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($plan)
    {
        $verify = pricingPlans::where('name', '=', sanitize($plan));
        if ($verify->get() && $verify->get()->count() > 0) {
            login_redirect('/pricing/'.$plan.'/?ref=get_started');
            if (Auth::check()) {
                $activate = posData::where('admin_id', '=', Auth::user()->id);
                if ($activate->get() && $activate->get()->count() > 0 ) {
                    $verify = $verify->first();
                    $activate = $activate->first();
                    if ($verify->code == 'POSP1001' || $activate->plan == $verify->id) {
                        return redirect()->route('dashboard');
                    }
                    else {
                        Session::put('nmswarepos', $verify->code);
                        return redirect('/contact');
                        //return view('checkout')->with(['name'=> $verify->name, 'amount'=>$verify->price]);
                    }
                }
                else {
                    return redirect('create-account?ref=get_started&plan=free');
                }
            }
            else {
                return redirect('/signin');
            }
        }
        else {
            return display404();
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
