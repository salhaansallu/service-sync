<?php

namespace App\Http\Controllers;

use App\Models\newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsletterController extends Controller
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
        $email = sanitize($request->input('email'));
        $verify = newsletter::where('email', $email)->get();
        if ($verify->count() > 0) {
            return response(json_encode(array("error"=>1, "msg"=>"Email already Subscribed")));
        }
        else {
            $news = new newsletter();
            $news->email = $email;
            $news->user_id = Auth::check() ? Auth::user()->id : "";
            if ($news->save()) {
                return response(json_encode(array("error"=>0, "msg"=>"Subscribed Successfully")));
            }
            else {
                return response(json_encode(array("error"=>0, "msg"=>"Sorry! something went wrong")));
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(newsletter $newsletter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(newsletter $newsletter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, newsletter $newsletter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(newsletter $newsletter)
    {
        //
    }
}
