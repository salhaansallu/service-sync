<?php

namespace App\Http\Controllers;

use App\Models\personalCredits;
use App\Models\Repairs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalCreditsController extends Controller
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
        if (Auth::check() && DashboardController::check(true)) {
            $bill_no = sanitize($request->input('bill_no'));
            (float)$amount = sanitize($request->input('amount'));
            $status = sanitize($request->input('status'));
            $note = sanitize($request->input('note'));

            $code_verify = personalCredits::where('bill_no', $bill_no)->first();

            if ($code_verify != null) {
                return response(json_encode(array("error" => 1, "msg" => "Credit already exists")));
            } elseif (!is_numeric($amount)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Amount")));
            } elseif (empty($bill_no) || empty($amount)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            $purchase = new personalCredits();
            $purchase->bill_no = $bill_no;
            $purchase->amount = $amount;
            $purchase->status = $status;
            $purchase->note = $note;

            if ($purchase->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Credit Added Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(personalCredits $personalCredits)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (!Auth::check() && DashboardController::check(true)) {
            return redirect('/signin');
        }

        $purchase = personalCredits::where('id', sanitize($id))->first();

        if ($purchase != null) {
            return view('pos.add-personalCredit')->with(['credit'=> $purchase, 'bills'=>Repairs::where('status', 'Delivered')->get()]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, personalCredits $personalCredits)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $bill_no = sanitize($request->input('bill_no'));
            (float)$amount = sanitize($request->input('amount'));
            $status = sanitize($request->input('status'));
            $note = sanitize($request->input('note'));

            $code_verify = personalCredits::where('bill_no', $bill_no)->where('id', '!=', $id)->get();

            if ($code_verify->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Credit already exists")));
            } elseif (!is_numeric($amount)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Amount")));
            } elseif (empty($bill_no) || empty($amount)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            $update = personalCredits::where('id', $id)->update([
                'bill_no' => $bill_no,
                'amount' => $amount,
                'status' => $status,
                'note' => $note,
            ]);

            if ($update) {
                return response(json_encode(array("error" => 0, "msg" => "Credit Updated Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(personalCredits $personalCredits)
    {
        //
    }
}
