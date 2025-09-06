<?php

namespace App\Http\Controllers;

use App\Models\quotations;
use App\Models\Repairs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuotationsController extends Controller
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
        if (!Auth::check() && isCashier()) {
            return redirect('/signin');
        }

        $product = Repairs::where('pos_code', company()->pos_code)->get();
        return view('pos.add-quotation')->with(['bills' => $product]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::check() && isCashier()) {
            $q_no = 10001;
            $bill_no = sanitize($request->input('bill_no'));
            $total = sanitize($request->input('total'));
            $cargo_type = sanitize($request->input('cargo_type'));
            $expiry_date = sanitize($request->input('expiry_date'));
            $delivery_date = sanitize($request->input('delivery_date'));

            $getQNo = quotations::orderBy('id', 'DESC')->first();
            $q_no = $getQNo && $getQNo->count() > 0 ? (int)$getQNo->q_no + 1 : 10001;

            if (empty($q_no)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Fields")));
            }
            if (quotations::where('q_no', $q_no)->get()->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Quotation number already exists")));
            }
            if (empty($bill_no)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Fields")));
            }
            if (!is_numeric($total)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for total")));
            }

            $data = [];

            for ($i=1; $i < 11; $i++) {
                if (!is_numeric(sanitize($request->input('price_'.$i)))) {
                    return response(json_encode(array("error" => 3, "product" => $i, "msg" => "Please use only numbers for price of product ".$i)));
                    break;
                }

                array_push($data, [
                    "model_no" => sanitize($request->input('model_no_'.$i)),
                    "serial_no" => sanitize($request->input('serial_no_'.$i)),
                    "fault" => sanitize($request->input('fault_'.$i)),
                    "price" => sanitize($request->input('price_'.$i)),
                ]);
            }

            array_push($data, [
                "customer" => [
                    "customer_name" => sanitize($request->input('customer_name')),
                    "customer_phone" => sanitize($request->input('customer_phone')),
                    "customer_address" => sanitize($request->input('customer_address')),
                ]
            ]);

            $partner = new quotations();
            $partner->q_no = $q_no;
            $partner->bill_no = $bill_no;
            $partner->products = json_encode($data);
            $partner->cargo_type = $cargo_type;
            $partner->total = $total;
            $partner->expiry_date = $expiry_date;
            $partner->delivery_date = $delivery_date;
            $partner->pos_code = company()->pos_code;

            if ($partner->save()) {
                $path = '/quotations/' . str_replace([' ', '.', "'", '"'], ['', '', "", ''], $q_no) . '.pdf';
                generateQuotation($q_no, $path);

                if (!empty(sanitize($request->input('email'))) && sanitize($request->input('email')) != "null") {
                    $mail = new Mail();
                    $mail->to = sanitize($request->input('email'));
                    $mail->toName = $bill_no=='custom'? sanitize($request->input('customer_name')) : 'WeFix Customer';
                    $mail->subject = 'New Quotation Created';
                    $mail->body = 'Please find the attached quotation requested from us';
                    $mail->attachments = [public_path($path)=>'Quotation'];
                    $mail->sendMail();
                }

                return response(json_encode(array("error" => 0, "msg" => "Quotation Created Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(quotations $quotations)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (!Auth::check() || !isCashier()) {
            return redirect('/signin');
        }

        $quotations = quotations::where('q_no', sanitize($id))->where('pos_code', company()->pos_code)->first();

        if ($quotations != null) {
            $product = Repairs::where('pos_code', company()->pos_code)->get();
            return view('pos.add-quotation')->with(['quotation' => $quotations, 'bills' => $product]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, quotations $quotations)
    {
        if (Auth::check() && isCashier()) {
            $id = sanitize($request->input('modelid'));

            $bill_no = sanitize($request->input('bill_no'));
            $total = sanitize($request->input('total'));
            $cargo_type = sanitize($request->input('cargo_type'));
            $expiry_date = sanitize($request->input('expiry_date'));
            $delivery_date = sanitize($request->input('delivery_date'));

            if (empty($bill_no)) {
                return response(json_encode(array("error" => 1, "msg" => "Please fill all fields")));
            }
            if (!is_numeric($total)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for total")));
            }

            $data = [];

            for ($i=1; $i < 11; $i++) {
                if (!is_numeric(sanitize($request->input('price_'.$i)))) {
                    return response(json_encode(array("error" => 3, "product" => $i, "msg" => "Please use only numbers for price of product ".$i)));
                    break;
                }

                array_push($data, [
                    "model_no" => sanitize($request->input('model_no_'.$i)),
                    "serial_no" => sanitize($request->input('serial_no_'.$i)),
                    "fault" => sanitize($request->input('fault_'.$i)),
                    "price" => sanitize($request->input('price_'.$i)),
                ]);
            }

            array_push($data, [
                "customer" => [
                    "customer_name" => sanitize($request->input('customer_name')),
                    "customer_phone" => sanitize($request->input('customer_phone')),
                    "customer_address" => sanitize($request->input('customer_address')),
                ]
            ]);

            $update = quotations::where('id', $id)->update([
                'bill_no' => $bill_no,
                'products' => $data,
                'cargo_type' => $cargo_type,
                'total' => $total,
                'expiry_date' => $expiry_date,
                'delivery_date' => $delivery_date,
            ]);

            if ($update) {
                $q_no = quotations::where('id', $id)->get()[0]->q_no;
                $path = '/quotations/' . str_replace([' ', '.', "'", '"'], ['', '', "", ''], $q_no) . '.pdf';
                generateQuotation($q_no, $path);

                if (!empty(sanitize($request->input('email'))) && sanitize($request->input('email')) != "null") {
                    $mail = new Mail();
                    $mail->to = sanitize($request->input('email'));
                    $mail->toName = $bill_no=='custom'? sanitize($request->input('customer_name')) : 'WeFix Customer';
                    $mail->subject = 'New Quotation Created';
                    $mail->body = 'Please find the attached quotation requested from us';
                    $mail->attachments = [public_path($path)=>'Quotation'];
                    $mail->sendMail();
                }

                return response(json_encode(array("error" => 0, "msg" => "Quotation Updated Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Repairs $repairs)
    {
        if (Auth::check() && isCashier()) {
            $id = sanitize($request->input('id'));
            $verify = quotations::where('id', $id)->where('pos_code', company()->pos_code);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Quotation deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Quotation not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }
}
