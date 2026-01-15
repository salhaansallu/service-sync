<?php

namespace App\Http\Controllers;

use App\Models\customers;
use App\Models\employee_expenses;
use App\Models\orders;
use App\Models\partners;
use App\Models\posUsers;
use App\Models\Products;
use App\Models\repairCommissions;
use App\Models\Repairs;
use App\Models\spareSaleHistory;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function reServiceListView()
    {
        if (Auth::check() && DashboardController::check(true)) {
            return view('pos.list-re_services')->with(['repairs' => $this->reServiceList()]);
        }
    }

    public function viewHistory()
    {
        if (Auth::check() && DashboardController::check(true)) {
            $result = $this->reServiceList();
            if (count($result) > 0) {
                return view('pos.display-repair-history')->with(['repair' => $this->reServiceList()]);
            }
            return display404();
        }
    }

    public function reServiceList($bill_no = null)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $company = company();
            $results = [];
            $parent_repairs = [];

            if ($bill_no != null) {
                $parent_repairs = Repairs::where('pos_code', $company->pos_code)->where('status', 'Delivered')->whereNull('parent')->where('bill_no', $bill_no)->get();
            } else {
                $parent_repairs = Repairs::where('pos_code', $company->pos_code)->where('status', 'Delivered')->whereNull('parent')->get();
            }

            foreach ($parent_repairs as $key => $parent) {
                $child_repairs = Repairs::where('pos_code', $company->pos_code)->where('parent', $parent->bill_no)->get();

                if ($child_repairs->count() > 0) {
                    $results[$key] = $parent;
                    $results[$key]["child"] = $child_repairs;
                }
            }

            return $results;
        }
    }

    public function getRepairs(Request $request)
    {
        $response = [];
        if (isCashier()) {
            $fromDate = date("Y-m-d") . " 00:00:00";
            $toDate = date("Y-m-d") . " 23:59:59";
            $status = $request->has('status') ? sanitize($request->input('status')) : '';

            if ($request->has("fromDate") && $request->has("toDate")) {
                $fromDate = date("Y-m-d", strtotime(sanitize($request->input("fromDate")))) . " 00:00:00";
                $toDate = date("Y-m-d", strtotime(sanitize($request->input("toDate")))) . " 23:59:59";
            }

            if (!empty($status)) {
                return response(json_encode(Repairs::where('pos_code', PosDataController::company()->pos_code)->where('type', '=', 'repair')->where('status', '=', $status)->whereNull('parent')->whereBetween('created_at', [$fromDate, $toDate])->orderBy('customer', 'DESC')->get()));
            }

            return response(json_encode(Repairs::where('pos_code', PosDataController::company()->pos_code)->where('type', '=', 'repair')->where('status', '!=', 'Delivered')->whereBetween('created_at', [$fromDate, $toDate])->orderBy('customer', 'DESC')->get()));
        } else {
            $response['error'] = 1;
            $response['msg'] = "not_logged_in";
            return response(json_encode($response));
        }
    }

    public function OtherPOSgetRepairs(Request $request)
    {
        $response = [];
        if (PosDataController::check()) {
            $fromDate = date("Y-m-d") . " 00:00:00";
            $toDate = date("Y-m-d") . " 23:59:59";
            $status = $request->has('status') ? sanitize($request->input('status')) : '';

            if ($request->has("fromDate") && $request->has("toDate")) {
                $fromDate = date("Y-m-d", strtotime(sanitize($request->input("fromDate")))) . " 00:00:00";
                $toDate = date("Y-m-d", strtotime(sanitize($request->input("toDate")))) . " 23:59:59";
            }

            if (!empty($status)) {
                return response(json_encode(Repairs::where('pos_code', PosDataController::company()->pos_code)->where('type', '=', 'other')->where('status', '=', $status)->whereNull('parent')->whereBetween('created_at', [$fromDate, $toDate])->orderBy('customer', 'DESC')->get()));
            }

            return response(json_encode(Repairs::where('pos_code', PosDataController::company()->pos_code)->where('type', '=', 'other')->where('status', '!=', 'Delivered')->whereBetween('created_at', [$fromDate, $toDate])->orderBy('id', 'DESC')->get()));
        } else {
            $response['error'] = 1;
            $response['msg'] = "not_logged_in";
            return response(json_encode($response));
        }
    }

    public function orderUpdate(Request $request)
    {
        if (Auth::check() && isCashier()) {
            $bills = $request->input('bill_no');
            $total = sanitize($request->input('total'));
            $note = sanitize($request->input('note'));
            $spares = $request->input('spares');
            $service_cost = $request->input('service_cost');
            $status = sanitize($request->input('status'));
            $techie = sanitize($request->input('techie'));
            $commission = sanitize($request->input('commission'));
            $parts = [];
            $customerData = [];
            $cost = 0;

            if ($status == "Awaiting Parts") {
                foreach ($bills as $key => $bill_no) {
                    $update =  Repairs::where('bill_no', $bill_no);

                    if ($update->update(["note" => $note, "status" => $status, "total" => $total, "cost" => $cost, "spares" => json_encode($parts)])) {
                        $customerData = customers::where('id', $update->get()[0]["customer"])->get();

                        if ($request->has('send_sms') && sanitize($request->input('send_sms')) == true) {
                            $sms = new SMS();
                            $sms->contact = array(array(
                                "fname" => $customerData[0]["name"],
                                "lname" => "",
                                "group" => "",
                                "number" => $customerData[0]["phone"],
                                "email" => $customerData[0]["email"],
                            ));

                            $sms->message = "Dear Customer, \nWe are awaiting parts for your repair. Delivery by air cargo takes 7 to 10 days, and by sea cargo, 45 to 60 days. We’ll update you once they arrive. The total cost is " . currency($update->get()[0]["total"]) . ", with " . currency($update->get()[0]["advance"]) . " paid as advance. For more info, please contact us at " . formatPhoneNumber(getUserData(company()->admin_id)->phone) . ". Thank you for your patience.";
                            $sms->Send();
                        }

                        if (!empty($customerData[0]["email"])) {
                            $mail = new Mail();
                            $mail->to = $customerData[0]["email"];
                            $mail->toName = $customerData[0]["name"];
                            $mail->subject = "Order Awaiting Parts - " . company()->company_name;
                            $mail->body = "Dear Customer, <br><br>We are awaiting parts for your repair. Delivery by air cargo takes 7 to 10 days, and by sea cargo, 45 to 60 days. We’ll update you once they arrive. <br>The total cost is " . currency($update->get()[0]["total"]) . ", with " . currency($update->get()[0]["advance"]) . " paid as advance. <br>For more info, please contact us at " . formatPhoneNumber(getUserData(company()->admin_id)->phone) . ". <br><br>Thank you for your patience!<br>" . company()->company_name;
                            $mail->sendMail();
                        }
                    }
                }

                return response(json_encode(array("error" => 0, "msg" => "Order Updated Successfully")));
            }

            if (!is_numeric($service_cost)) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid service cost format")));
            }

            if (!count($spares) > 0 && !$service_cost > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Please enter atleast 1 spare or enter service charge")));
            }

            foreach ($bills as $key => $bill_no) {

                foreach ($spares as $key => $value) {
                    if (!str_contains($value['id'], 'temp')) {
                        $stock = Products::where('id', $value['id'])->get();
                        if ($stock->count() > 0) {
                            Products::where('id', $value['id'])->update([
                                "qty" => (float)$stock[0]['qty'] - $value['qty']
                            ]);

                            $sale = new spareSaleHistory();
                            $sale->spare_name = $stock[0]['pro_name'];
                            $sale->spare_id = $stock[0]['id'];
                            $sale->cost = $stock[0]['cost'];
                            $sale->qty = $value['qty'];
                            $sale->bill_no = $bill_no;
                            $sale->pos_code = company()->pos_code;
                            $sale->save();

                            $cost += (float)$stock[0]['cost'] * (float)$value['qty'];
                            $parts[] = $value['id'];
                        }
                    } else {
                        $sale = new spareSaleHistory();
                        $sale->spare_name = $value['pro_name'];
                        $sale->spare_id = $value['id'];
                        $sale->cost = $value['cost'];
                        $sale->qty = $value['qty'];
                        $sale->bill_no = $bill_no;
                        $sale->pos_code = company()->pos_code;
                        $sale->save();

                        $cost += (float)$value['cost'] * (float)$value['qty'];
                        $parts[] = $value['id'];
                    }
                }

                if (is_numeric($service_cost) && $service_cost > 0) {
                    $cost += ($service_cost / 100) * $total;
                }

                $update =  Repairs::where('bill_no', $bill_no);

                // if ($update->first() && $update->first()->type == 'other') {
                //     $commissionPer = is_numeric($commission)? $commission : 0;
                //     if ($commissionPer > 0) {
                //         $commission = ($commissionPer / 100) * ($total - $cost);
                //     }
                //     else {
                //         $commission = 0;
                //     }
                // }

                if ($update->update(["note" => $note, "techie" => $techie, "commission" => (is_numeric($commission) ? $commission : 0), "status" => $status, "total" => $total, "cost" => $cost, "spares" => json_encode($parts), "repaired_date" => date('Y-m-d H:i:s')])) {

                    $customerData = customers::where('id', $update->get()[0]["customer"])->get();

                    if ($request->has('send_sms') && sanitize($request->input('send_sms')) == true) {

                        $sms = new SMS();
                        $sms->contact = array(array(
                            "fname" => $customerData[0]["name"],
                            "lname" => "",
                            "group" => "",
                            "number" => $customerData[0]["phone"],
                            "email" => $customerData[0]["email"],
                        ));

                        if ($status == "Repaired") {
                            $sms->message = "Dear Customer, \nyour product repair is complete. The total cost is " . currency($update->get()[0]["total"]) . ", with " . currency($update->get()[0]["advance"]) . " paid as advance. The remaining balance is " . currency($update->get()[0]["total"] - $update->get()[0]["advance"]) . ". \nThank you for choosing " . company()->company_name . ".";
                        } elseif ($status == "Customer Pending") {
                            $sms->message = "Dear Customer,\nThis is the WeFix.lk team. We tried contacting you regarding your service request but couldn't reach you. The total cost is " . currency($update->get()[0]["total"]) . ", with " . currency($update->get()[0]["advance"]) . " paid as advance. Please let us know a convenient time for us to call, or feel free to contact us at " . formatPhoneNumber(getUserData(company()->admin_id)->phone) . ". \nThank you for choosing " . company()->company_name . ".";
                        } else {
                            $sms->message = "Dear Customer, \nWe couldn't repair some of your items. The total cost is " . currency($update->get()[0]["total"]) . ", with " . currency($update->get()[0]["advance"]) . " paid as advance. Please collect them within 14 days, as we are not responsible after this period. For any questions, call us at " . formatPhoneNumber(getUserData(company()->admin_id)->phone) . ". \nThank you for choosing " . company()->company_name . ".";
                        }

                        $sms->Send();
                    }

                    if (!empty($customerData[0]["email"])) {
                        $mail = new Mail();
                        $mail->to = $customerData[0]["email"];
                        $mail->toName = $customerData[0]["name"];

                        if ($status == "Repaired") {
                            $mail->subject = "Repair Complete - " . company()->company_name;
                            $mail->body = "Dear Customer, <br><br>your product repair is complete. <br>The total cost is " . currency($update->get()[0]["total"]) . ", with " . currency($update->get()[0]["advance"]) . " paid as advance. <br>The remaining balance is " . currency($update->get()[0]["total"] - $update->get()[0]["advance"]) . ". <br>For more info, please contact us at " . formatPhoneNumber(getUserData(company()->admin_id)->phone) . ". <br><br>Thank you for your patience!<br>" . company()->company_name;
                        } elseif ($status == "Customer Pending") {
                            $mail->subject = "No Response - Repair Complete - " . company()->company_name;
                            $mail->body = "Dear Customer, <br><br>This is the WeFix.lk team. We tried contacting you regarding your service request but couldn't reach you. <br>The total cost is " . currency($update->get()[0]["total"]) . ", with " . currency($update->get()[0]["advance"]) . " paid as advance. <br>The remaining balance is " . currency($update->get()[0]["total"] - $update->get()[0]["advance"]) . ". <br>Please let us know a convenient time for us to call, or feel free to contact us at " . formatPhoneNumber(getUserData(company()->admin_id)->phone) . ". <br><br>Thank you for choosing!<br>" . company()->company_name;
                        } else {
                            $mail->subject = "Repair incomplete - " . company()->company_name;
                            $mail->body = "Dear Customer, <br><br>We couldn't repair some of your items. <br>The total cost is " . currency($update->get()[0]["total"]) . ", with " . currency($update->get()[0]["advance"]) . " paid as advance. <br>The remaining balance is " . currency($update->get()[0]["total"] - $update->get()[0]["advance"]) . ". <br>Please collect them within 14 days, as we are not responsible after this period. For any questions, call us at " . formatPhoneNumber(getUserData(company()->admin_id)->phone) . ". <br><br>Thank you for choosing!<br>" . company()->company_name;
                        }

                        $mail->sendMail();
                    }

                    generateInvoice($bill_no, str_replace(['checkout/', 'newOrder/'], ['', ''], $update->get()[0]["invoice"]), 'newOrder');
                    generateThermalInvoice($bill_no, str_replace('Invoice', 'Thermal-invoice', str_replace(['checkout/', 'newOrder/'], ['', ''], $update->get()[0]["invoice"])), 'newOrder');
                }
            }

            return response(json_encode(array("error" => 0, "msg" => "Order Updated Successfully")));
        }
    }

    public function getInvoicePDF(Request $request)
    {
        if (Auth::check() && isCashier()) {
            $bill_no = sanitize($request->input('bill_no'));

            $bill =  Repairs::where('bill_no', $bill_no)->where('pos_code', company()->pos_code)->get();
            if ($bill->count() > 0) {
                $bill = $bill[0];
                $inv_type = "";

                if ($bill->status == "Delivered") {
                    $inv_type = "checkout";
                } elseif ($bill->status == "Pending") {
                    $inv_type = "newOrder";
                }
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
        if (Auth::check() && isCashier()) {

            try {
                $bill_no = 1001;
                $total = sanitize($request->input('total'));
                $note = sanitize($request->input('note'));
                $model_no = sanitize($request->input('model_no'));
                $serial_no = sanitize($request->input('serial_no'));
                $fault = sanitize($request->input('fault'));
                $advance = sanitize($request->input('advance'));
                $customer = sanitize($request->input('customer'));
                $customer_name = sanitize($request->input('customer_name'));
                $customer_phone = sanitize($request->input('customer_phone'));
                $partner = sanitize($request->input('partner'));
                $cashier_no = sanitize($request->input('cashier_no'));
                $bill_type = sanitize($request->input('bill_type'));
                $parent_bill_no = sanitize($request->input('parent_bill_no'));
                $new_order_qty = sanitize($request->input('new_order_qty'));
                $has_multiple_faults = sanitize($request->input('has_multiple_faults'));
                $signature = sanitize($request->input('signature'));
                $faults = json_decode($request->input('faults'));
                $customerData = [];
                $billData = [];

                if ($bill_type == 'new-order') {
                    $customerData = customers::where('name', $customer_name)->where('phone', $customer_phone)->first();
                    if (!$customerData) {

                        if (empty($customer_name) || $customer_phone == "") {
                            return response(json_encode(array("error" => 1, "msg" => "Invalid customer")));
                        }

                        $customerData = new customers();
                        $customerData->name = $customer_name;
                        $customerData->phone = $customer_phone;
                        $customerData->pos_code = company()->pos_code;
                        $customerData->save();
                    }
                } else {
                    $billData = Repairs::where('bill_no', $parent_bill_no)->where('status', 'Delivered')->get();

                    if ($billData->count() == 0) {
                        return response(json_encode(array("error" => 1, "msg" => "Invalid bill number")));
                    }

                    $model_no = $billData[0]->model_no;
                    $serial_no = $billData[0]->serial_no;
                    $customer = $billData[0]->customer;
                    $partner = $billData[0]->partner;
                    $model_no = $billData[0]->bill_no;

                    $customerData = customers::where('id', $customer)->first();
                }

                if (!is_numeric($total)) {
                    return response(json_encode(array("error" => 1, "msg" => "Invalid price format")));
                }
                if (!is_numeric($advance)) {
                    return response(json_encode(array("error" => 1, "msg" => "Invalid advance format")));
                }

                $getCashier = posUsers::where('cashier_code', $cashier_no)->get();

                if ($getCashier->count() > 0) {
                    $cashier_no = $getCashier[0]->user_id;
                }

                $success_count = 0;
                $bills = [];
                $totalAmount = 0;

                if ($new_order_qty > 0) {
                    for ($i = 0; $i < $new_order_qty; $i++) {
                        try {
                            $getBillNo = Repairs::orderBy('id', 'DESC')->first();
                            $bill_no = $getBillNo != null ? (int)$getBillNo->bill_no + 1 : 1001;

                            if ($has_multiple_faults) {
                                foreach ($faults as $key => $faultItem) {
                                    if (!is_numeric($faultItem->price)) {
                                        return response(json_encode(array("error" => 1, "msg" => "Some faults contain invalid price format")));
                                    }

                                    $totalAmount += $faultItem->price;
                                }
                            }

                            $repair = new Repairs();
                            $repair->bill_no = $bill_no;
                            $repair->model_no = $model_no;
                            $repair->serial_no = $serial_no;
                            $repair->fault = $fault;
                            $repair->has_multiple_fault = $has_multiple_faults ? 'Y' : 'N';
                            $repair->multiple_fault = json_encode($faults);
                            $repair->note = $note;
                            $repair->advance = $advance;
                            $repair->total = $has_multiple_faults ? $totalAmount : $total;
                            $repair->customer = $customerData->id;
                            $repair->partner = $partner == "" ? 0 : $partner;
                            $repair->cashier = $cashier_no;
                            $repair->techie = '';
                            $repair->status = "Pending";
                            $repair->parent = $bill_type == 'new-order' ? NULL : $parent_bill_no;
                            $repair->signature = $signature;

                            if (isset($_GET['source']) && sanitize($_GET['source']) == "other-pos") {
                                $repair->type = "other";
                            } else {
                                $repair->type = "repair";
                            }

                            $repair->pos_code = company()->pos_code;
                            $repair->warranty = 0;

                            if ($repair->save()) {

                                if (($partner == "" || $partner == 0) && $repair->type == 'repair' && $bill_type == 'new-order' && $request->has('send_sms') && sanitize($request->input('send_sms')) == true) {
                                    $sms = new SMS();
                                    $sms->contact = array(array(
                                        "fname" => $customerData->name,
                                        "lname" => "",
                                        "group" => "",
                                        "number" => $customerData->phone,
                                        "email" => $customerData->email,
                                    ));
                                    $sms->message = "Dear Customer, your  " . company()->company_name . " account is created. We've received your product and will notify you when the repair is done. Track it at https://wefixservers.xyz/customer-portal?phone=" . $customerData->phone . ". Thank you!";
                                }

                                $rand = date('d-m-Y-h-i-s') . '-' . rand(0, 9999999) . '.pdf';
                                $inName = str_replace(' ', '-', str_replace('.', '-', $bill_no)) . '-Invoice-' . $rand;
                                $thermalInName = str_replace(' ', '-', str_replace('.', '-', $bill_no)) . '-Thermal-invoice-' . $rand;
                                $success_count++;
                                $bills[] = $bill_no;



                                $generate_invoice = generateInvoice($bill_no, $inName, 'newOrder');
                                $generate_thermal_invoice = generateThermalInvoice($bill_no, $thermalInName, 'newOrder');

                                if ($generate_invoice->generated == true) {
                                    Repairs::where('bill_no', $bill_no)->update([
                                        "invoice" => "newOrder/" . $inName,
                                    ]);
                                }
                            }
                        } catch (Exception $e) {
                        }
                    }

                    $tempBill = 'temp-muilti-repairs-invoice';
                    $generate_temp_thermal_invoice = generateThermalInvoice($bills, $tempBill, 'newOrder');
                    $generate_temp_sticker = generateThermalSticker(is_array($bills) ? $bills[0] : $bills, 'temp-sticker');

                    if ($bill_type != 'new-order') {
                        $commis = new repairCommissions();
                        $commis->user = $billData[0]->techie;
                        $commis->amount = - (0.1 * ($billData[0]->total - $billData[0]->cost));
                        $commis->status = "pending";
                        $commis->note = $billData[0]->bill_no . " Return for re-service";
                        $commis->save();
                    }

                    if ($partner == "" || $partner == 0) {
                        if (!empty($customerData->email)) {
                            $mail = new Mail();
                            $mail->to = $customerData->email;
                            $mail->toName = $customerData->name;
                            $mail->subject = "Account Created - " . company()->company_name;
                            $mail->body = "Dear Customer,<br><br>We've received your product and will notify you when the repair is done.<br>Track it at <a href='https://wefixservers.xyz/customer-portal?phone=" . $customerData->phone . "'>Customer Portal</a>.<br><br>Thank you!<br>" . company()->company_name;

                            if ($new_order_qty > 1) {
                                $mail->attachments = [public_path(($generate_temp_thermal_invoice->url)) => 'Invoice-' . $bill_no . '.pdf'];
                            } else {
                                $mail->attachments = [public_path(($generate_invoice->url)) => 'Invoice-' . $bill_no . '.pdf'];
                            }

                            $mail->sendMail();
                        }
                    }

                    return response(json_encode(array("error" => $new_order_qty != $success_count, "msg" => $success_count . " out of " . $new_order_qty . " orders placed", "invoiceURL" => $generate_temp_thermal_invoice->url, 'sticker' => $generate_temp_sticker)));
                }

                return response(json_encode(array("error" => 1, "msg" => "QTY must be atleast 1")));
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
        if (!Auth::check() || !isCashier()) {
            return redirect('/signin');
        }

        $product = Repairs::where('id', sanitize($id))->where('pos_code', company()->pos_code)->get();
        $customers = customers::where('pos_code', company()->pos_code)->get();
        $users = DB::table('users')->select('pos_users.*', 'users.fname', 'users.lname', 'users.id', 'users.email')->leftJoin('pos_users', 'users.id', '=', 'pos_users.user_id')->where('pos_code', company()->pos_code)->get();
        $partners = partners::where('pos_code', company()->pos_code)->get();
        $spares = Products::where('pos_code', company()->pos_code)->get();

        if ($product && $product->count() > 0) {
            return view('pos.add-category')->with(['repairs' => $product[0], 'customers' => $customers, 'partners' => $partners, 'spares' => $spares, 'users' => $users]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Repairs $repairs)
    {
        if (Auth::check() && isCashier()) {
            $id = sanitize($request->input('modelid'));
            $model_no = sanitize($request->input('model_no'));
            $serial_no = sanitize($request->input('serial_no'));
            $fault = sanitize($request->input('fault'));
            $note = sanitize($request->input('note'));
            $delivery = sanitize($request->input('delivery'));
            $advance = sanitize($request->input('advance'));
            $total = sanitize($request->input('total'));
            $customer = sanitize($request->input('customer'));
            $partner = sanitize($request->input('partner'));
            $techie = sanitize($request->input('techie'));
            $commission = sanitize($request->input('commission'));
            $cashier = sanitize($request->input('cashier'));
            $warranty = sanitize($request->input('warranty'));
            $spares = [];
            if ($request->has('spares')) {
                foreach ($request->input('spares') as $key => $val) {
                    $spares[] = sanitize($val);
                }
            }
            $status = sanitize($request->input('status'));

            $id_verify = Repairs::where('id', $id)->get();

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
                "delivery" => $delivery,
                "customer" => $customer,
                "techie" => $techie,
                "commission" => $commission,
                "cashier" => $cashier,
                "partner" => $partner,
                "spares" => json_encode($spares),
                "status" => $status,
                "warranty" => $warranty,
                "updated_at" => date('Y-m-d H:i:s'),
            ]);

            if ($product) {

                employee_expenses::where('reference', $id_verify[0]->bill_no)->where('type', 'Commission')->update([
                    "amount" => $commission,
                ]);

                if ($status == "Pending" || $status == "Awaiting Parts" || $status == "Repaired") {
                    generateInvoice($id_verify[0]->bill_no, str_replace(['newOrder/', 'checkout/'], "", $id_verify[0]->invoice), 'newOrder');
                    generateThermalInvoice($id_verify[0]->bill_no, str_replace(['newOrder/', 'checkout/'], "", str_replace('Invoice', 'Thermal-invoice', $id_verify[0]->invoice)), 'newOrder');
                } else {
                    generateInvoice($id_verify[0]->bill_no, str_replace(['newOrder/', 'checkout/'], "", $id_verify[0]->invoice), 'checkout');
                    generateThermalInvoice($id_verify[0]->bill_no, str_replace(['newOrder/', 'checkout/'], "", str_replace('Delivery', 'Thermal-delivery', $id_verify[0]->invoice)), 'checkout');
                }

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
        if (Auth::check() && isCashier()) {
            $id = sanitize($request->input('id'));
            $verify = Repairs::where('id', $id);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Order deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Order not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }

    // =============== Sales ====================== ///

    public function salesUpdate(Request $request, Repairs $repairs)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $note = sanitize($request->input('note'));
            $customer = sanitize($request->input('customer'));
            $status = sanitize($request->input('status'));

            $id_verify = Repairs::where('id', $id)->where('pos_code', company()->pos_code)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            $product = Repairs::where('id', $id)->update([
                "note" => $note,
                "customer" => $customer,
                "status" => $status,
                "updated_at" => date('Y-m-d H:i:s'),
            ]);

            if ($product) {
                return response(json_encode(array("error" => 0, "msg" => "Order Updated Successfully", 'id' => $id)));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    public function salesEdit($id)
    {
        if (!Auth::check() && DashboardController::check(true)) {
            return redirect('/signin');
        }

        $product = Repairs::where('id', sanitize($id))->where('pos_code', company()->pos_code)->get();
        $customers = customers::where('pos_code', company()->pos_code)->get();

        if ($product && $product->count() > 0) {
            return view('pos.add-bills')->with(['repairs' => $product[0], 'customers' => $customers]);
        } else {
            return display404();
        }
    }

    public function salesDestroy(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('id'));
            $verify = Repairs::where('id', $id)->where('pos_code', company()->pos_code);
            if ($verify && $verify->get()->count() > 0) {
                $order = orders::where('bill_no', $verify->get()[0]->bill_no)->where('pos_code', company()->pos_code);
                if ($verify->delete()) {
                    $order->delete();
                    return response(json_encode(array("error" => 0, "msg" => "Order deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Order not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }

    public function getAllPendingRepairs(Request $request)
    {
        if (Auth::check() && PosDataController::check()) {
            $result = [];
            $techs = User::all();
            foreach ($techs as $key => $tech) {
                $data = DB::select('SELECT * FROM repairs WHERE (status = "Pending" AND cashier= "' . $tech->id . '") OR ((status = "Return" OR status = "Awaiting Parts" OR status = "Customer Pending") AND techie = "' . $tech->id . '")');

                if (count($data) > 0) {
                    $result[] = [
                        "name" => $tech->fname . ' ' . $tech->lname,
                        "id" => $tech->id,
                        "repairs" => $data
                    ];
                }
            }
            return response(json_encode($result));
        }
    }

    public function getAllPendingReport(Request $request)
    {
        if (Auth::check() && PosDataController::check()) {
            $id = $request->input('id');
            $name = $request->input('name');
            $data = is_array($id) ? json_decode(json_encode($id)) : $id;

            if (!is_array($id)) {
                $data = DB::select('SELECT * FROM repairs WHERE (status = "Pending" AND cashier = ?) OR (status IN ("Return", "Awaiting Parts", "Customer Pending") AND techie = ?) LIMIT 10', [$id, $id]);
            }

            $generate = generatePendingInvoice($data, 'panding-repair-report.pdf', $id, $name);

            return response(json_encode(['error' => 0, 'report' => asset($generate->url)]));
        }
    }

    public function n8n_get(Request $request)
    {
        $term = sanitize($request->input('term'));
        $type = sanitize($request->input('type'));
        $status = sanitize($request->input('status'));

        $qry = DB::table('repairs')->whereIn('type', ['repair', 'other']);

        if (!empty($term)) {
            $qry->where(function ($query) use ($term) {
                $query->where('bill_no', '=', $term)
                    ->orWhere('serial_no', '=', $term);
            });
        }

        if (!empty($status)) {
            $statusArr = [
                'pending' => 'Pending',
                'delivered' => 'Delivered',
                'repaired' => 'Repaired',
                'return' => 'Return',
                'awaiting_parts' => 'Awaiting Parts',
                'customer_pending' => 'Customer Pending',
            ];

            if (isset($statusArr[$status])) {
                $qry->where('status', '=', $statusArr[$status]);
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status parameter',
                ]);
            }
        }

        if (!empty($type)) {
            $typeArr = [
                'tv-repair' => 'repair',
                'other-repair' => 'other',
            ];

            if (isset($typeArr[$type])) {
                $qry->where('type', '=', $typeArr[$type]);
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid type parameter',
                ]);
            }
        }

        $results = $qry->get(['bill_no', 'model_no', 'serial_no', 'fault', 'has_multiple_fault', 'multiple_fault', 'advance', 'total', 'status', 'invoice', 'type', 'created_at']);

        $results->map(function ($item) {
            if (!empty($item->invoice)) {
                $item->invoice = env('APP_URL'). 'invoice/' . $item->invoice;
                $item->type = $item->type == 'repair'? 'TV Repair' : 'Other Repair';
            }
            return $item;
        });

        if ($qry->count() > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Repair fetched successfully',
                'data' => $results,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No repair record found',
            ]);
        }
    }
}
