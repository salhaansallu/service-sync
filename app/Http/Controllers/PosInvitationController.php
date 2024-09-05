<?php

namespace App\Http\Controllers;

use App\Models\PosInvitation;
use App\Models\posUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosInvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        login_redirect('/invitation/accept/'.$id);
        if (Auth::check()) {
            $verify = PosInvitation::where('invitation_id', $id)->where('user_id', Auth::user()->id);
            if ($verify->get() && $verify->count() > 0) {
                $pos_code = $verify->get()[0]->pos_code;

                $update = $verify->update([
                    "status" => "accepted"
                ]);

                if ($update) {
                    $user_verify = posUsers::where('user_id', Auth::user()->id)->where('pos_code', $pos_code)->get();
                    if ($user_verify && $user_verify->count() > 0) {
                        return redirect('/account/overview')->withError('You can Already Access This POS');
                    }
                    else {
                        $posUser = new posUsers();
                        $posUser->user_id = Auth::user()->id;
                        $posUser->pos_code = $pos_code;

                        if ($posUser->save()) {
                            return redirect('/account/overview');
                        }
                        else {
                            return redirect('/account/overview')->withError('Sorry! something went wrong');
                        }
                    }
                }
                else {
                    return redirect('/account/overview')->withError('Sorry! something went wrong');
                }
            }
            else {
                return redirect('/account/overview')->withError('Invalid Invitation');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PosInvitation $posInvitation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PosInvitation $posInvitation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosInvitation $posInvitation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosInvitation $posInvitation)
    {
        //
    }
}
