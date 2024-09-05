<?php

namespace App\Http\Controllers;

use App\Models\posData;
use App\Models\pricingPlans;
use App\PaypalPro as AppPaypalPro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Omnipay\Common\Http\Exception;
use Omnipay\Omnipay;
use App\PaypalPro;
use Twocheckout;
use Twocheckout_Charge;
use Twocheckout_Error;

class paymentController extends Controller
{

    public function __construct()
    {
        Twocheckout::privateKey(env('2C_PRIVATE_KEY'));
        Twocheckout::sellerId(env('2C_SELLER_ID'));
    }

    public function checkout(Request $request)
    {
        if (Auth::check()) {
            if (Session::has('nmswarepos')) {
                $verify = pricingPlans::where('code', '=', sanitize(Session::get('nmswarepos')));
                $company = company();
                $userData = userData();

                if ($verify->get() && $verify->count() > 0) {

                    $verify = $verify->first();
                    $token = sanitize($request->input('token'));
                    // $creditCardNumber = trim(str_replace(" ", "", sanitize($request->input('card_number'))));
                    // $creditCardType = sanitize($request->input('card_type'));
                    // $expYear = sanitize($request->input('expiry_year'));
                    // $expMonth = sanitize($request->input('expiry_month'));
                    // $cvv = sanitize($request->input('cvv'));

                    try {
                        // Charge a credit card
                        $charge = Twocheckout_Charge::auth($arr = array(
                            "merchantOrderId" => $company->pos_code,
                            "token" => $token,
                            "currency" => env('2C_CURRENCY'),
                            "total" => $verify->price,
                            "billingAddr" => array(
                                "name" => 'John Doe',
                                "addrLine1" => $userData->address,
                                "addrLine2" => $userData->address,
                                "city" => $userData->city,
                                "state" => $userData->city,
                                "zipCode" => $userData->zip,
                                "country" => $userData->country,
                                "email" => Auth::user()->email,
                                "phoneNumber" => $userData->phone,
                            ),
                        ));
                        
                        // Check whether the charge is successful
                        if ($charge['response']['responseCode'] == 'APPROVED') {
                            
                            dd($charge);
                        }
                    } catch (Twocheckout_Error $e) {
                        dd($e->getMessage());
                    }

                    // if ($paymentStatus == "SUCCESS") {
                    //     // Transaction info 
                    //     $transactionID = $response['TRANSACTIONID'];
                    //     $new_date =  date('Y-m-d h:i:s',strtotime('+30 days',strtotime(date('Y-m-d h:i:s'))));
                    //     posData::where('pos_code', $company->pos_code)->update(['plan' => $verify->id, 'expiry_date'=>$new_date]);
                    //     return redirect()->route('dashboard');
                    // } else {
                    //     return view('checkout')->with(['name'=> $verify->name, 'amount'=>$verify->price, 'error'=> 'Payment Faild']);
                    // }
                }
                else {
                    return display404();
                }
            }
            else {
                return display404();
            }
        }
        else {
            login_redirect('/'. request()->path());
            return redirect('/signup');
        }
    }
}
