<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function userLogin(Request $request)
    {

        // $captcha = captchaVerify($request->input('cf-turnstile-response'));

        // if ($captcha->error) {
        //     return view('auth.login')->with('error', array("email" => $request->input('email'),"message" => $captcha->msg));
        // }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, false)) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        $error = array(
            "email" => $request->input('email'),
            "message" => "Invalid login credincials"
        );

        return view('auth.login')->with('error', $error);
    }
}
