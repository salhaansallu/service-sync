<?php

namespace App\Http\Controllers;

use App\Models\chinaOrders;
use App\Models\customers;
use App\Models\Repairs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChinaOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && DashboardController::check(true)) {
            $customers = customers::where('pos_code', company()->pos_code)->get();
            return view('pos.list-china-orders')->with(['customers' => json_encode($customers)]);
        }
        return redirect('/signin');
    }

    public function get(Request $request)
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && DashboardController::check(true)) {
            $fromdate = sanitize($request->input('params')['fromdate']);
            $todate = sanitize($request->input('params')['todate']);
            $status = sanitize($request->input('params')['status']);
            $bill_no = sanitize($request->input('params')['bill_no']);

            if ($status == "") {
                $status = ' WHERE ';
            } 
            else {
                $status = ' WHERE status = "' . $status . '" AND ';
            }

            if ($bill_no == "") {
                $bill_no = ' ';
            } 
            else {
                $bill_no = ' bill_no = "' . $bill_no . '" AND ';
            }

            $order = DB::select('select * from china_orders ' . $status . $bill_no . ' pos_code = "' . company()->pos_code . '" AND created_at BETWEEN "' . date('Y-m-d', strtotime($fromdate)) . ' 00:00:00" AND "' . date('Y-m-d', strtotime($todate)) . ' 23:59:59"');
            return response(json_encode($order));
        }
    }

    public function bulkEdit(Request $request)
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && DashboardController::check(true)) {
            $action = sanitize($request->input('action'));
            $ids = $request->input('id');

            foreach ($ids as $key => $id) {
                if (in_array($action, ['Pending', 'Purchased', 'Canceled'])) {
                    chinaOrders::where('pos_code', company()->pos_code)->where('id', $id)->update([
                        'status' => $action
                    ]);
                }
                elseif ($action == "Delete") {
                    chinaOrders::where('pos_code', company()->pos_code)->where('id', $id)->delete();
                }
            }

            return response(json_encode(['error' => 0, "msg"=>"Order action success"]));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::check() && DashboardController::check(true)) {
            return view('pos.add-chinaOrders');
        }

        return redirect('/signin');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $purchase_number = sanitize($request->input('purchase_no'));
            $purchase_number = str_replace(' ', '', $purchase_number);
            $bill_no = sanitize($request->input('bill_no'));
            $panel_no = sanitize($request->input('panel_no'));
            $pcb_no = sanitize($request->input('pcb_no'));
            (float)$price = sanitize($request->input('price'));
            (float)$qty = sanitize($request->input('qty'));
            $order_date = sanitize($request->input('order_date'));
            $delivery_date = sanitize($request->input('delivery_date'));
            $status = sanitize($request->input('status'));

            $code_verify = chinaOrders::where('purchase_no', $purchase_number)->where('pos_code', company()->pos_code)->get();

            if ($code_verify && $code_verify->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Order Number Already Exists")));
            } elseif (!is_numeric($price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price")));
            } elseif (!is_numeric($qty)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For QTY")));
            } elseif (empty($purchase_number) || empty($bill_no)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            if (empty($order_date)) {
                $order_date = Carbon::now();
            }

            if (empty($delivery_date)) {
                $delivery_date = null;
            }

            $purchase = new chinaOrders();
            $purchase->purchase_no = $purchase_number;
            $purchase->bill_no = $bill_no;
            $purchase->panel_no = $panel_no;
            $purchase->pcb_no = $pcb_no;
            $purchase->price = $price;
            $purchase->qty = $qty;
            $purchase->delivery_date = $delivery_date;
            $purchase->created_at = $order_date;
            $purchase->pos_code = company()->pos_code;
            $purchase->status = $status;

            if ($purchase->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Order Added Successfully")));
            }
        }
        return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
    }

    /**
     * Display the specified resource.
     */
    public function show(chinaOrders $chinaOrders)
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

        $purchase = chinaOrders::where('purchase_no', sanitize($id))->where('pos_code', company()->pos_code)->get();

        if ($purchase && $purchase->count() > 0) {
            return view('pos.add-chinaOrders')->with('purchase', $purchase[0]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, chinaOrders $chinaOrders)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $purchase_number = sanitize($request->input('purchase_no'));
            $purchase_number = str_replace(' ', '', $purchase_number);
            $bill_no = sanitize($request->input('bill_no'));
            $panel_no = sanitize($request->input('panel_no'));
            $pcb_no = sanitize($request->input('pcb_no'));
            (float)$price = sanitize($request->input('price'));
            (float)$qty = sanitize($request->input('qty'));
            $order_date = sanitize($request->input('order_date'));
            $delivery_date = sanitize($request->input('delivery_date'));
            $status = sanitize($request->input('status'));

            $id_verify = chinaOrders::where('id', $id)->where('pos_code', company()->pos_code)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            $code_verify = chinaOrders::where('id', '!=', $id)->where('purchase_no', $purchase_number)->where('pos_code', company()->pos_code)->get();

            if ($code_verify && $code_verify->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Order Number Already Exists")));
            } elseif (!is_numeric($price)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price")));
            } elseif (!is_numeric($qty)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For QTY")));
            } elseif (empty($purchase_number) || empty($bill_no)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            $purchase = chinaOrders::where('id', $id)->update([
                "purchase_no" => $purchase_number,
                "bill_no" => $bill_no,
                "panel_no" => $panel_no,
                "pcb_no" => $pcb_no,
                "price" => $price,
                "qty" => $qty,
                "delivery_date" => $delivery_date,
                "created_at" => $order_date,
                "status" => $status,
            ]);

            if ($purchase) {
                return response(json_encode(array("error" => 0, "msg" => "Order Updated Successfully", 'code' => $purchase_number)));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(chinaOrders $chinaOrders)
    {
        //
    }
}
