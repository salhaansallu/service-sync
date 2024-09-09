<?php

namespace App\Http\Controllers;

use App\Models\customers;
use App\Models\Products;
use App\Models\Repairs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SMS;

class RepairsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getRepairs()
    {
        $response = [];
        if (PosDataController::check()) {
            return response(json_encode(Repairs::where('pos_code', PosDataController::company()->pos_code)->where('status', '!=', 'Delivered')->get()));
        } else {
            $response['error'] = 1;
            $response['msg'] = "not_logged_in";
            return response(json_encode($response));
        }
    }

    public function orderUpdate(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $bill_no = sanitize($request->input('bill_no'));
            $total = sanitize($request->input('total'));
            $note = sanitize($request->input('note'));
            $spares = $request->input('spares');
            $service_cost = $request->input('service_cost');
            $status = sanitize($request->input('status'));
            $parts = [];
            $cost = 0;

            if (!is_numeric($service_cost)) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid service cost format")));
            }

            if (!count($spares) > 0 && !$service_cost > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Please enter atleast 1 spare or enter service charge")));
            }

            foreach ($spares as $key => $value) {
                $stock = Products::where('id', $value['id'])->where('pos_code', company()->pos_code)->get();
                if ($stock->count() > 0) {
                    Products::where('id', $value['id'])->where('pos_code', company()->pos_code)->update([
                        "qty" => (float)$stock[0]['qty'] - $value['qty']
                    ]);
                    $cost += (float)$stock[0]['cost'] * (float)$value['qty'];
                    $parts[] = $value['id'];
                }
            }

            if (is_numeric($service_cost) && $service_cost > 0) {
                $cost += ($service_cost / 100) * $total;
            }

            $update =  Repairs::where('bill_no', $bill_no)->where('pos_code', company()->pos_code);

            if ($update->update([ "note" => $note, "status" => $status, "total" => $total, "cost" => $cost, "spares" => json_encode($parts) ])) {
                $customerData = customers::where('pos_code', company()->pos_code)->where('id', $update->get()[0]["customer"])->get();
                $sms = new SMS();
                $sms->contact = array(array(
                    "fname" => $customerData[0]["name"],
                    "lname" => "",
                    "group" => "",
                    "number" => $customerData[0]["phone"],
                    "email" => $customerData[0]["email"],
                ));
                $sms->message = "Dear Customer, your product repair is complete. The total cost is " . currency($update->get()[0]["total"]) . ", with " . currency($update->get()[0]["advance"]) . " paid as advance. The remaining balance is " . currency($update->get()[0]["total"] - $update->get()[0]["advance"]) . ". Thank you for choosing " . company()->company_name . ".";
                $sms->Send();

                return response(json_encode(array("error" => 0, "msg" => "Order Updated Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
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
            try {
                $bill_no = 1001;
                $total = sanitize($request->input('total'));
                $note = str_replace('\n', '{break;}', $request->input('note'));
                $note = sanitize($request->input('note'));
                $model_no = sanitize($request->input('model_no'));
                $serial_no = sanitize($request->input('serial_no'));
                $fault = sanitize($request->input('fault'));
                $advance = sanitize($request->input('advance'));
                $customer = sanitize($request->input('customer'));

                $customerData = customers::where('pos_code', company()->pos_code)->where('id', $customer)->get();

                if ($customerData->count() == 0) {
                    return response(json_encode(array("error" => 1, "msg" => "Invalid customer")));
                }

                $note = str_replace('{break;}', '<br>', $note);

                if (!is_numeric($total)) {
                    return response(json_encode(array("error" => 1, "msg" => "Invalid price format")));
                }
                if (!is_numeric($advance)) {
                    return response(json_encode(array("error" => 1, "msg" => "Invalid advance format")));
                }

                $getBillNo = Repairs::where('pos_code', company()->pos_code)->orderBy('id', 'DESC')->first();
                $bill_no = $getBillNo && $getBillNo->count() > 0 ? (int)$getBillNo->bill_no + 1 : 1001;

                $repair = new Repairs();
                $repair->bill_no = $bill_no;
                $repair->model_no = $model_no;
                $repair->serial_no = $serial_no;
                $repair->fault = $fault;
                $repair->note = $note;
                $repair->advance = $advance;
                $repair->total = $total;
                $repair->customer = $customer;
                $repair->cashier = Auth::user()->id;
                $repair->status = "Pending";
                $repair->pos_code = company()->pos_code;

                if ($repair->save()) {

                    $sms = new SMS();
                    $sms->contact = array(array(
                        "fname" => $customerData[0]["name"],
                        "lname" => "",
                        "group" => "",
                        "number" => $customerData[0]["phone"],
                        "email" => $customerData[0]["email"],
                    ));
                    $sms->message = "Dear Customer, your account with " . company()->company_name . " has been successfully created. We have received your product and will notify you via this number once the repair is complete. Thank you for choosing " . company()->company_name . ".";
                    $sms->Send();

                    $inName = str_replace(' ', '-', str_replace('.', '-', $bill_no)) . '-Invoice-' . date('d-m-Y-h-i-s') . '-' . rand(0, 9999999) . '.pdf';

                    $generate_invoice = generateInvoice($bill_no, $inName, 'newOrder');

                    if ($generate_invoice->generated == true) {

                        Repairs::where('bill_no', $bill_no)->where('pos_code', company()->pos_code)->update([
                            "invoice" => $inName,
                        ]);

                        return response(json_encode(array("error" => 0, "msg" => "Order Added Successfully", "invoiceURL" => $generate_invoice->url)));
                    } else {
                        return response(json_encode(array("error" => 0, "msg" => "Order Added Successfully, Couldn't print invoice: " . $generate_invoice->msg)));
                    }
                }

                return response(json_encode(array("error" => 1, "msg" => "Something went wrong, please try again later")));
            } catch (Exception $e) {
                return response(json_encode(array("error" => 1, "msg" => "Error: " . $e->getMessage())));
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Repairs $repairs)
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

        $product = Repairs::where('id', sanitize($id))->where('pos_code', company()->pos_code)->get();
        $customers = customers::where('pos_code', company()->pos_code)->get();
        $spares = Products::where('pos_code', company()->pos_code)->get();

        if ($product && $product->count() > 0) {
            return view('pos.add-category')->with(['repairs' => $product[0], 'customers' => $customers, 'spares' => $spares]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Repairs $repairs)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $model_no = sanitize($request->input('model_no'));
            $serial_no = sanitize($request->input('serial_no'));
            $fault = sanitize($request->input('fault'));
            $note = sanitize($request->input('note'));
            $advance = sanitize($request->input('advance'));
            $total = sanitize($request->input('total'));
            $customer = sanitize($request->input('customer'));
            foreach ($request->input('spares') as $key => $val) {
                $spares[] = sanitize($val);
            }
            $status = sanitize($request->input('status'));

            $id_verify = Repairs::where('id', $id)->where('pos_code', company()->pos_code)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            if (!is_numeric($total)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Price")));
            }

            if (!is_numeric($advance)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Use Only Numbers For Advance")));
            }

            if (empty($model_no) || empty($customer) || empty($status)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked As '*'")));
            }

            $product = Repairs::where('id', $id)->update([
                "model_no" => $model_no,
                "serial_no" => $serial_no,
                "fault" => $fault,
                "note" => $note,
                "advance" => $advance,
                "total" => $total,
                "customer" => $customer,
                "spares" => json_encode($spares),
                "status" => $status,
                "updated_at" => date('Y-m-d H:i:s'),
            ]);

            if ($product) {
                return response(json_encode(array("error" => 0, "msg" => "Order Updated Successfully", 'id' => $id)));
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
            $verify = Repairs::where('id', $id)->where('pos_code', company()->pos_code);
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
