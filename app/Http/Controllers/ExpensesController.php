<?php

namespace App\Http\Controllers;

use App\Models\expenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && isCashier()) {
            $purchses = expenses::all();
            return view('pos.list-expenses')->with(['purchses' => $purchses]);
        } else {
            return redirect('/signin');
        }
    }

    public function getItems(Request $request)
    {
        if (Auth::check() && isCashier()) {
            $items = expenses::where('item', 'like', '%' . sanitize($request->input('item')) . '%')->groupBy('item')->orderBy('created_at', 'desc')->get('item');
            if ($items && $items->count() > 0) {
                return response(json_encode(array("error" => 0, "data" => $items)));
            } else {
                return response(json_encode(array("error" => 1, "msg" => "No Items Found")));
            }
        } else {
            return redirect('/signin');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && isCashier()) {
            return view('pos.add-expense');
        } else {
            return redirect('/signin');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::check() && isCashier()) {

            (float)$amount = $request->has('amount') ? sanitize($request->input('amount')) : 0;
            $item = sanitize($request->input('item'));
            $qty = sanitize($request->input('qty'));
            $supplier = sanitize($request->input('supplier'));
            $user = sanitize($request->input('user'));
            $payment = sanitize($request->input('payment'));
            $note = sanitize($request->input('note'));


            if (!is_numeric($amount)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price")));
            } elseif (empty($user)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            $purchase = new expenses();
            $purchase->item = $item;
            $purchase->amount = $amount;
            $purchase->qty = $qty;
            $purchase->supplier_id = $supplier;
            $purchase->payment = $payment;
            $purchase->user = $user;
            $purchase->note = $note;

            if ($purchase->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Expense Added Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(expenses $expenses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (!Auth::check() || !isAdmin()) {
            return redirect('/signin');
        }

        $purchase = expenses::where('id', sanitize($id))->get();

        if ($purchase && $purchase->count() > 0) {
            return view('pos.add-expense')->with('purchase', $purchase[0]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, expenses $expenses)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            (float)$amount = $request->has('amount') ? sanitize($request->input('amount')) : 0;
            $item = sanitize($request->input('item'));
            $qty = sanitize($request->input('qty'));
            $supplier = sanitize($request->input('supplier'));
            $user = sanitize($request->input('user'));
            $payment = sanitize($request->input('payment'));
            $note = sanitize($request->input('note'));

            if (!is_numeric($amount)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price")));
            } elseif (empty($user)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            $purchase = expenses::where('id', $id)->update([
                "item" => $item,
                "amount" => $amount,
                "qty" => $qty,
                "supplier_id" => $supplier,
                "payment" => $payment,
                "user" => $user,
                "note" => $note,
            ]);

            if ($purchase) {
                return response(json_encode(array("error" => 0, "msg" => "Expense Updated Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (Auth::check() && DashboardController::check()) {
            $id = sanitize($request->input('code'));
            $verify = expenses::where('id', $id);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Expense deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Expense not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }

    public function report()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && isCashier()) {
            $data = DB::table('expenses')
                ->select('item', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(amount) as total_amount'))
                ->groupBy('item')
                ->get();
            return view('pos.list-expensesReport')->with(['purchses' => $data]);
        } else {
            return redirect('/signin');
        }
    }
}
