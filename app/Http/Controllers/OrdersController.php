<?php

namespace App\Http\Controllers;

use App\Models\customers;
use App\Models\orders;
use App\Models\Products;
use App\Models\Repairs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SMS;

class OrdersController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(orders $orders)
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

        $orders = orders::where('id', sanitize($id))->where('pos_code', company()->pos_code)->get();
        $customers = customers::where('pos_code', company()->pos_code)->get();

        if ($orders && $orders->count() > 0) {
            $product = Repairs::where('bill_no', $orders[0]->bill_no)->where('pos_code', company()->pos_code)->get();
            if ($product && $product->count() > 0) {
                return view('pos.add-orders')->with(['orders' => $orders[0], 'products' => $product[0], 'customers' => $customers]);
            }
            return view('pos.add-orders')->with(['orders' => $orders[0], 'customers' => $customers]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, orders $orders)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $payment_method = sanitize($request->input('payment_method'));
            $delivery = sanitize($request->input('delivery'));
            $charge = empty(sanitize($request->input('charge'))) ? 0 : sanitize($request->input('charge'));
            $tracking_code = sanitize($request->input('code'));
            $note = sanitize($request->input('note'));
            $status = sanitize($request->input('status'));

            if (!is_numeric($charge)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for price")));
            }

            $id_verify = orders::where('id', $id)->where('pos_code', company()->pos_code)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            $invoice = '';

            $inName = str_replace(' ', '-', str_replace('.', '-', $id_verify[0]->bill_no)) . '-Delivery-' . date('d-m-Y-h-i-s') . '-' . rand(0, 9999999) . '.pdf';

            if ($status == "Delivered") {
                $invoice = generateDeliveryInvoice($id, $inName)->url;
            }

            $product = orders::where('id', $id)->update([
                "payment_method" => $payment_method,
                "delivery" => $delivery,
                "charge" => $charge,
                "tracking_code" => $tracking_code,
                "status" => $status,
                "note" => $note,
                "invoice" => $status == "Delivered" ? "delivery/" . $inName : "",
                "updated_at" => date('Y-m-d H:i:s'),
            ]);

            if ($product) {
                if ($status == "Delivered" && $id_verify[0]->status !== 'Delivered') {
                    $invoice = generateDeliveryInvoice($id, $inName)->url;

                    $repair = Repairs::where('bill_no', $id_verify[0]->bill_no)
                        ->where('pos_code', company()->pos_code)->first();
                    $customer = $repair ? getCustomer($repair->customer) : null;
                    if ($customer && $customer->phone) {
                        $sms = new SMS();
                        $sms->contact = [['number' => $customer->phone]];
                        $sms->message = "Dear customer, Thank you for placing your order at ".company()->company_name. ". Your order has been dispatched and is on the way. Courrier Service: ".$delivery.", Tracking No: ".$tracking_code;
                        $sms->Send();
                    }

                    // Sales checkout reserves the stock when the order is created.
                }

                return response(json_encode(array("error" => 0, "msg" => "Order Updated Successfully", 'id' => $id, "url" => $invoice)));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong, please try again later")));
        }
    }

    public function return(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('id'));

            $id_verify = orders::where('id', $id)->where('pos_code', company()->pos_code)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            $returned = DB::transaction(function () use ($id) {
                $order = orders::where('id', $id)->where('pos_code', company()->pos_code)
                    ->lockForUpdate()->first();
                if (!$order || $order->status === 'Return') {
                    return false;
                }

                $repair = Repairs::where('bill_no', $order->bill_no)
                    ->where('pos_code', company()->pos_code)->first();
                if (!$repair) {
                    return false;
                }

                foreach (json_decode(htmlspecialchars_decode($repair->products ?? '[]')) ?: [] as $item) {
                    Products::where('id', $item->id)
                        ->where('pos_code', company()->pos_code)
                        ->increment('qty', (float) $item->qty);
                }

                $order->status = 'Return';
                $order->save();
                $repair->total = 0;
                $repair->cost = $order->charge;
                $repair->status = 'Return';
                $repair->save();

                return $repair;
            });

            if ($returned) {

                $customer = getCustomer($returned->customer);
                if ($customer && $customer->phone) {
                    $sms = new SMS();
                    $sms->contact = [['number' => $customer->phone]];
                    $sms->message = "Dear customer, Your order has been returned to our store and has been cancelled. If you did not cancel, please contact us via ".formatPhoneNumber(getUserData(company()->admin_id)->phone);
                    $sms->Send();
                }

                return response(json_encode(array("error" => 0, "msg" => "Order Returned Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong, please try again later")));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('id'));
            $verify = orders::where('id', $id)->where('pos_code', company()->pos_code);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Order deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Order not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }
}
