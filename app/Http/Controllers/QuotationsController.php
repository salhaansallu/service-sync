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
        if (!Auth::check() && DashboardController::check(true)) {
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
        if (Auth::check() && DashboardController::check(true)) {
            $q_no = sanitize($request->input('q_no'));
            $bill_no = sanitize($request->input('bill_no'));
            $total = sanitize($request->input('total'));
            $cargo_type = sanitize($request->input('cargo_type'));
            $expiry_date = sanitize($request->input('expiry_date'));
            $delivery_date = sanitize($request->input('delivery_date'));

            if (empty($q_no)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Fields")));
            }
            if (quotations::where('q_no', $q_no)->where('pos_code', company()->pos_code)->get()->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Quotation number already exists")));
            }
            if (empty($bill_no)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Fields")));
            }
            if (!is_numeric($total)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for total")));
            }

            $partner = new quotations();
            $partner->q_no = $q_no;
            $partner->bill_no = $bill_no;
            $partner->cargo_type = $cargo_type;
            $partner->total = $total;
            $partner->expiry_date = $expiry_date;
            $partner->delivery_date = $delivery_date;
            $partner->pos_code = company()->pos_code;

            if ($partner->save()) {
                generateQuotation($q_no);
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
        if (!Auth::check() || !DashboardController::check(true)) {
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
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $q_no = sanitize($request->input('q_no'));
            $bill_no = sanitize($request->input('bill_no'));
            $total = sanitize($request->input('total'));
            $cargo_type = sanitize($request->input('cargo_type'));
            $expiry_date = sanitize($request->input('expiry_date'));
            $delivery_date = sanitize($request->input('delivery_date'));

            if (empty($q_no)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Fields")));
            }
            if (quotations::where('q_no', $q_no)->where('pos_code', company()->pos_code)->where('id', '!=', $id)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Quotation number already exists")));
            }
            if (empty($bill_no)) {
                return response(json_encode(array("error" => 1, "msg" => "Please fill all fields")));
            }
            if (!is_numeric($total)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for total")));
            }

            $update = quotations::where('id', $id)->where('pos_code', company()->pos_code)->update([
                'q_no' => $q_no,
                'bill_no' => $bill_no,
                'cargo_type' => $cargo_type,
                'total' => $total,
                'expiry_date' => $expiry_date,
                'delivery_date' => $delivery_date,
            ]);

            if ($update) {
                generateQuotation($q_no);
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
        if (Auth::check() && DashboardController::check(true)) {
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
