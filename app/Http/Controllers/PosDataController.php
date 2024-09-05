<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\orderProducts;
use App\Models\orders;
use App\Models\posData;
use App\Models\posUsers;
use App\Models\Products;
use App\Models\Repairs;
use App\Models\saveOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class PosDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        if (Auth::check()) {
            $code = UrlToCode(sanitize($id));
            if (!empty($code)) {
                if ($this->login($code)) {
                    return redirect("/pos");
                }
                return response()->view('errors.404')->setStatusCode(404);
            }
            return response()->view('errors.404')->setStatusCode(404);
        }
        return response()->view('errors.404')->setStatusCode(404);
    }

    public static function company()
    {
        if (Auth::check() && get_Cookie('pos_session') != false) {
            $data = posData::where('pos_code', Crypt::decrypt(get_Cookie('pos_session')))->get();
            if ($data && $data->count() > 0) {
                return (object)$data[0];
            }
            return defaultValues();
        }
        return defaultValues();
    }

    public static function check($plan_verify = false)
    {
        if ($plan_verify) {
            if (get_Cookie('pos_session') != false) {
                $check = posUsers::where('user_id', Auth::user()->id)->where('pos_code', Crypt::decrypt(get_Cookie('pos_session')))->get();
                if ($check && $check->count() > 0) {
                    if (company()->plan > 1) {
                        return true;
                    }
                }
                return false;
            }
            return false;
        }
        if (get_Cookie('pos_session') != false) {
            $check = posUsers::where('user_id', Auth::user()->id)->where('pos_code', Crypt::decrypt(get_Cookie('pos_session')))->get();
            if ($check && $check->count() > 0) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function login($code)
    {
        $verify = posUsers::where('user_id', Auth::user()->id)->where('pos_code', Crypt::decrypt($code))->get();
        if ($verify && $verify->count() > 0) {
            set_Cookie('pos_session', $code);
            set_Cookie('user_code', Hash::make("This is just a duplicate"));
            return true;
        }
        return response()->view('errors.404')->setStatusCode(404);
    }

    public function getPosData()
    {
        $response = [];
        if ($this->check()) {
            $data = posData::where('pos_code', Crypt::decrypt(get_Cookie('pos_session')))->get(['pos_code', 'plan', 'currency']);
            if ($data && $data->count() > 0) {
                return (object)$data[0];
            }
            return defaultValues();
        } else {
            $response['error'] = 1;
            $response['msg'] = "not_logged_in";
            return response(json_encode($response));
        }
    }

    public function checkout(Request $request)
    {
        if (Auth::check() && $this->check()) {
            $request = filter_var_array($request->input('params'), FILTER_SANITIZE_STRING);
            $cashin = sanitize($request['cashin']);
            $bill_no = sanitize($request['bill_no']);
            $order = Repairs::where('bill_no', $bill_no)->where('pos_code', $this->company()->pos_code)->get();
            if ($order->count() > 0) {
                $order = $order[0];
            }
            $total = $order['total'] - $order['advance'];

            if ($cashin < $total) {
                $credit = new Credit();
                $credit->customer_id = $order["customer"];
                $credit->ammount = $total - $cashin;
                $credit->pos_code = $this->company()->pos_code;
                $credit->order_id = $bill_no;
                $credit->save();
            }

            Repairs::where('bill_no', $bill_no)->where('pos_code', $this->company()->pos_code)->update([
                "status" => "Delivered",
                "updated_at" => date('d-m-Y H:i:s'),
            ]);    

            $inName = str_replace(' ', '-', str_replace('.', '-', $bill_no)) . '-Delivery-' . date('d-m-Y-h-i-s') . '-' . rand(0, 9999999) . '.pdf';

            $generate_invoice = generateInvoice($bill_no, $inName, 'checkout');

            if ($generate_invoice->generated == true) {

                Repairs::where('bill_no', $bill_no)->where('pos_code', $this->company()->pos_code)->update([
                    "invoice" => $inName,
                ]);

                return response(json_encode(array("error" => 0, "msg" => "Checkout successful", "invoiceURL" => $generate_invoice->url)));
            } else {
                return response(json_encode(array("error" => 0, "msg" => "Checkout successful, Couldn't print invoice: " . $generate_invoice->msg)));
            }

            return response(json_encode(array("error" => 1, "msg" => "Error while proceeding, please try again later")));
        }
        return response(json_encode(array("error" => 1, "msg" => "Not logged in")));
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
    public function show()
    {
        login_redirect('/account/overview');
        if (Auth::check()) {
            if ($this->check() && !empty(company()->pos_code)) {
                return view('pos.pos');
            } else {
                return redirect('/account/overview');
            }
        } else {
            return redirect('/signin');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(posData $posData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, posData $posData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(posData $posData)
    {
        //
    }
}
