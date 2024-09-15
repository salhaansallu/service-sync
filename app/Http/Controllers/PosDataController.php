<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\customers;
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
                    "invoice" => "checkout/".$inName,
                ]);

                return response(json_encode(array("error" => 0, "msg" => "Checkout successful", "invoiceURL" => $generate_invoice->url)));
            } else {
                return response(json_encode(array("error" => 0, "msg" => "Checkout successful, Couldn't print invoice: " . $generate_invoice->msg)));
            }

            return response(json_encode(array("error" => 1, "msg" => "Error while proceeding, please try again later")));
        }
        return response(json_encode(array("error" => 1, "msg" => "Not logged in")));
    }

    public function salesCheckout(Request $request)
    {
        if (Auth::check() && $this->check()) {
            $request = filter_var_array($request->input('params'), FILTER_SANITIZE_STRING);
            $cashin = sanitize($request['cashin']);
            $spares = $request['products'];
            $customer = sanitize($request['customer']);
            $bill_no = 1001;
            $getBillNo = Repairs::where('pos_code', company()->pos_code)->orderBy('id', 'DESC')->first();
            $bill_no = $getBillNo && $getBillNo->count() > 0 ? (int)$getBillNo->bill_no + 1 : 1001;
            $total = 0;
            $cost = 0;
            $parts = [];
            $invoice_pro = [];

            if (customers::where('pos_code', company()->pos_code)->where('id', $customer)->get()->count() == 0) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Customer")));
            }

            foreach ($spares as $key => $value) {
                $stock = Products::where('sku', $value['id'])->where('pos_code', company()->pos_code)->get();
                if ($stock->count() > 0) {
                    Products::where('id', $value['id'])->where('pos_code', company()->pos_code)->update([
                        "qty" => (float)$stock[0]['qty'] - $value['qty']
                    ]);
                    $cost += (float)$stock[0]['cost'] * (float)$value['qty'];
                    $total += (float)$stock[0]['price'] * (float)$value['qty'];
                    $parts[] = $stock[0]['id'];

                    $invoice_pro[] = array(
                        "name" => $stock[0]["pro_name"],
                        "unit" => $stock[0]["price"],
                        "qty" => $value['qty'],
                    );
                }
            }

            if ($cashin < $total) {
                $credit = new Credit();
                $credit->customer_id = $customer;
                $credit->ammount = $total - $cashin;
                $credit->pos_code = $this->company()->pos_code;
                $credit->order_id = $bill_no;
                $credit->save();
            }

            $repair = new Repairs();
                $repair->bill_no = $bill_no;
                $repair->model_no = "";
                $repair->serial_no = "";
                $repair->fault = "";
                $repair->note = "";
                $repair->advance = 0;
                $repair->spares = json_encode($parts);
                $repair->total = $total;
                $repair->cost = $cost;
                $repair->customer = $customer;
                $repair->cashier = Auth::user()->id;
                $repair->status = "Delivered";
                $repair->pos_code = company()->pos_code;

                if ($repair->save()) {

                    $inName = str_replace(' ', '-', str_replace('.', '-', $bill_no)) . '-Invoice-' . date('d-m-Y-h-i-s') . '-' . rand(0, 9999999) . '.pdf';

                    $generate_invoice = generateSalesInvoice($bill_no, $inName, $invoice_pro, $cashin);

                    if ($generate_invoice->generated == true) {

                        Repairs::where('bill_no', $bill_no)->where('pos_code', company()->pos_code)->update([
                            "invoice" => $inName,
                        ]);

                        return response(json_encode(array("error" => 0, "msg" => "Checkout Successful", "invoiceURL" => $generate_invoice->url)));
                    } 
                    else {
                        return response(json_encode(array("error" => 0, "msg" => "Checkout Successful, Couldn't print invoice: " . $generate_invoice->msg)));
                    }
                }
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
