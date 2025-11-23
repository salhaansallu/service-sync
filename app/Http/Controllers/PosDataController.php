<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\customers;
use App\Models\employee_expenses;
use App\Models\orderProducts;
use App\Models\orders;
use App\Models\partners;
use App\Models\personalCredits;
use App\Models\posData;
use App\Models\posUsers;
use App\Models\Products;
use App\Models\repairCommissions;
use App\Models\Repairs;
use App\Models\WarrantyRecord;
use App\Models\saveOrders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use SMS;

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
        $data = posData::first();
        if ($data) {
            return $data;
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

    public function getCashiers()
    {
        $response = [];
        if ($this->check()) {
            $data = DB::table('users')->select('pos_users.*', 'users.fname', 'users.lname', 'users.id', 'users.email')->leftJoin('pos_users', 'users.id', '=', 'pos_users.user_id')->where('pos_code', company()->pos_code)->get();
            if ($data && $data->count() > 0) {
                return (object)$data;
            }
            return [];
        } else {
            $response['error'] = 1;
            $response['msg'] = "not_logged_in";
            return response(json_encode($response));
        }
    }

    public function checkout(Request $request)
    {
        if (Auth::check() && isCashier()) {
            $request = filter_var_array($request->input('params'), FILTER_SANITIZE_STRING);
            $cashin = sanitize($request['cashin']);
            $bill_no = filter_var_array($request["bill_no"], FILTER_SANITIZE_STRING);
            $company = PosDataController::company();
            $total = 0;
            $advance = 0;
            $delivery = sanitize($request['delivery']);
            $warranty = sanitize($request['warranty']);
            $signature = sanitize($request['signature']);

            $rand = date('d-m-Y-h-i-s') . '-' . rand(0, 9999999) . '.pdf';
            $inName = str_replace(' ', '-', str_replace('.', '-', $bill_no[0])) . '-Delivery-' . $rand;
            $ThermalinName = str_replace(' ', '-', str_replace('.', '-', $bill_no[0])) . '-Thermal-delivery-' . $rand;

            foreach ($bill_no as $key => $id) {
                $total += Repairs::where('bill_no', $id)->sum('total');
                $advance += Repairs::where('bill_no', $id)->sum('advance');

                Repairs::where('bill_no', $id)->update([
                    "status" => "Delivered",
                    "updated_at" => date('d-m-Y H:i:s'),
                    "invoice" => "checkout/" . $inName,
                    "paid_at" => Carbon::now(),
                    "delivery" => $delivery,
                    "warranty" => $warranty,
                    "signature" => $signature,
                ]);

                // Create warranty record if warranty months > 0
                if ($warranty > 0) {
                    $repair = Repairs::where('bill_no', $id)->first();
                    if ($repair) {
                        $customer = customers::where('id', $repair->customer)->first();

                        if ($customer) {
                            // Get product name from model_no or use generic name
                            $productName = !empty($repair->model_no) ? $repair->model_no : 'TV Repair Service';

                            // Calculate expiry date based on warranty months
                            $purchaseDate = Carbon::now();
                            $expiryDate = Carbon::now()->addMonths((int)$warranty);

                            // Create warranty record
                            WarrantyRecord::create([
                                'serial_number' => !empty($repair->serial_no) ? $repair->serial_no : 'N/A-' . $id,
                                'bill_number' => $id,
                                'phone_number' => $customer->phone,
                                'product_name' => $productName,
                                'purchase_date' => $purchaseDate,
                                'expiry_date' => $expiryDate,
                                'coverage_type' => $warranty . ' Months Warranty',
                                'notes' => 'Auto-generated from POS checkout. Repair completed.',
                                'is_active' => true,
                            ]);
                        }
                    }
                }

                $rp = Repairs::where('bill_no', $id)->get(['techie', 'total', 'cost', 'commission', 'bill_no']);
                if ($rp->count() > 0) {
                    $com = new repairCommissions();
                    $com->user = $rp[0]->techie;
                    $com->amount = 0.1 * ($rp[0]->total - $rp[0]->cost);
                    $com->status = "pending";
                    $com->note = $id;
                    $com->save();

                    employee_expenses::insert([
                        "user" => $rp[0]->techie,
                        "note" => 'Commission of bill no '.$rp[0]->bill_no,
                        "amount" => $rp[0]->commission,
                        "type" => 'Commission',
                        "reference" => $rp[0]->bill_no,
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                    ]);
                }

                personalCredits::where('bill_no', $id)->update([
                    'status' => 'Delivered',
                ]);


            }

            $repairs = Repairs::where('bill_no', $bill_no[0])->get('customer')[0];

            if ($cashin < $total) {
                $minusAmt = count($bill_no) > 0? ($cashin / count($bill_no)) : 0;
                foreach ($bill_no as $key => $id) {
                    $repair = Repairs::where('bill_no', $id)->first(['customer', 'total', 'advance']);
                    if ($repair) {
                        $credit = new Credit();
                        $credit->customer_id = $repair->customer;
                        $credit->ammount = ($repair->total - $repair->advance) - $minusAmt;
                        $credit->pos_code = $company->pos_code;
                        $credit->order_id = $id;
                        $credit->save();
                    }
                }
            }

            $generate_invoice = generateInvoice($bill_no, $inName, 'checkout');
            $generate_thermal_invoice = generateThermalInvoice($bill_no, $ThermalinName, 'checkout');

            if ($generate_invoice->generated == true) {
                $customer = customers::where('id', $repairs->customer)->first();
                if ($customer && !empty($customer->email)) {
                    $mail = new Mail();
                    $mail->to = $customer->email;
                    $mail->toName = $customer->name;
                    $mail->subject = "Invoice - " . company()->company_name;
                    $mail->body = "Dear Customer,<br><br>Please find the invoice attached for your repair.<br><br>Thank you!<br>" . company()->company_name;
                    $mail->attachments = [public_path(($generate_invoice->url)) => 'Invoice.pdf'];
                    $mail->sendMail();
                }
                return response(json_encode(array("error" => 0, "msg" => "Checkout successful", "invoiceURL" => $generate_thermal_invoice->url)));
            } else {
                return response(json_encode(array("error" => 0, "msg" => "Checkout successful, Couldn't print invoice: " . $generate_thermal_invoice->msg)));
            }

            return response(json_encode(array("error" => 1, "msg" => "Error while proceeding, please try again later")));
        }
        return response(json_encode(array("error" => 1, "msg" => "Not logged in")));
    }

    public function salesCheckout(Request $request)
    {
        if (Auth::check() && isCashier()) {
            $request = filter_var_array($request->input('params'), FILTER_SANITIZE_STRING);
            $cashin = sanitize($request['cashin']);
            $sale_type = sanitize($request['sale_type']);
            $spares = $request['products'];
            $customer = sanitize($request['customer']);
            $bill_no = 1001;
            $getBillNo = Repairs::orderBy('id', 'DESC')->first();
            $bill_no = $getBillNo && $getBillNo->count() > 0 ? (int)$getBillNo->bill_no + 1 : 1001;
            $total = 0;
            $cost = 0;
            $parts = [];
            $invoice_pro = [];

            if ($sale_type != "online" && $sale_type != "instore") {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Sale Type")));
            }

            foreach ($spares as $key => $value) {
                $stock = Products::where('sku', $value['id'])->first();
                if ($stock != null) {
                    $stock->qty = (float)$stock->qty - (float)$value['qty'];
                    $stock->save();
                    $cost += (float)$stock->cost * (float)$value['qty'];
                    $total += (float)$value['price'] * (float)$value['qty'];
                    $parts[] = $stock->id;

                    $invoice_pro[] = array(
                        "name" => $stock->pro_name,
                        "unit" => $value['price'],
                        "cost" => $stock->cost,
                        "qty" => $value['qty'],
                        "sku" => $stock->sku,
                        "id" => $stock->id,
                    );
                }
                else {
                    $cost += (float)$value['cost'] * (float)$value['qty'];
                    $total += (float)$value['price'] * (float)$value['qty'];
                    $parts[] = $value['id'];
                    $invoice_pro[] = array(
                        "name" => $value['pro_name'],
                        "unit" => $value['price'],
                        "cost" => $value['cost'],
                        "qty" => $value['qty'],
                        "sku" => $value['sku'],
                        "id" => $value['id'],
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
            $repair->type = "sale";
            $repair->products = htmlspecialchars(json_encode($invoice_pro));
            $repair->pos_code = company()->pos_code;

            if ($repair->save()) {

                if ($sale_type == "online") {
                    $order = new orders();
                    $order->bill_no = $bill_no;
                    $order->pos_code = company()->pos_code;
                    $order->payment_method = "";
                    $order->status = "Processing";
                    $order->save();
                }

                $rand = date('d-m-Y-h-i-s') . '-' . rand(0, 9999999) . '.pdf';

                $inName = str_replace(' ', '-', str_replace('.', '-', $bill_no)) . '-Invoice-' . $rand;
                $ThermalInName = str_replace(' ', '-', str_replace('.', '-', $bill_no)) . '-Thermal-invoice-' . $rand;

                $generate_invoice = generateSalesInvoice($bill_no, $inName, $invoice_pro, $cashin);
                $generate_thermal_invoice = generateThermalSalesInvoice($bill_no, $ThermalInName, json_encode($invoice_pro), $cashin);

                if ($generate_invoice->generated == true) {

                    Repairs::where('bill_no', $bill_no)->where('pos_code', company()->pos_code)->update([
                        "invoice" => 'checkout/' . $inName,
                    ]);

                    return response(json_encode(array("error" => 0, "msg" => "Checkout Successful", "invoiceURL" => $generate_thermal_invoice->url)));
                } else {
                    return response(json_encode(array("error" => 0, "msg" => "Checkout Successful, Couldn't print invoice: " . $generate_thermal_invoice->msg)));
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

    public function OtherPOSshow()
    {
        login_redirect('/account/overview');
        if (Auth::check()) {
            if ($this->check() && !empty(company()->pos_code)) {
                return view('pos.other_repairs');
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

    public function runCron()
    {
        $this->reminder();
        //$this->termination();

        PartnersController::runSubscription();
    }

    public function reminder()
    {
        $repairedData = Repairs::where('status', 'Repaired')->whereDate('repaired_date', Carbon::now()->subDays(14))->get();
        $customerPendingData = Repairs::where('status', 'Customer Pending')->where('type', 'repair')->whereDate('repaired_date', Carbon::now()->subDays(14))->get();
        $returnData = Repairs::where('status', 'Return')->where('type', 'repair')->whereDate('repaired_date', Carbon::now()->subDays(14))->get();

        foreach ($repairedData as $key => $value) {
            $customerData = customers::where('id', $value->customer)->first();
            if ($customerData != null && $value->partner == 0) {
                $sms = new SMS();
                $sms->contact = array(array(
                    "fname" => $customerData->name,
                    "lname" => "",
                    "group" => "",
                    "number" => $customerData->phone,
                    "email" => $customerData->email,
                ));

                $sms->message = "Dear Customer,\nYour repaired product is ready for collection. Despite reminders, it remains uncollected. Please pick it up within 2 days to avoid legal action. If not collected within this period, we will not be responsible for its condition and will proceed with disassembly. CONTACT 077 330 0905 IMMEDIATELY. IF YOU DO NOT COLLECT THE ITEM WITHIN 2 DAYS, ALL YOUR INFORMATION WILL BE DELETED FROM OUR SYSTEM.";
                $sms->Send();

                $sms->type = 'unicode';

                // $sms->message = "அன்புள்ள வாடிக்கையாளர்,\nஉங்கள் TV WeFix.LK -ல் பழுது நீக்கப்பட்டு தயாராக உள்ளது. பல முறை நினைவூட்டல்களுக்குப் பிறகும், நீங்கள் அதை பெறவில்லை. தயவுசெய்து 2 நாட்கள் க்குள் வந்து பெற்றுக்கொள்ளவும்; இல்லையெனில் legal action எடுக்கப்படும். குறிப்பிட்ட நேரத்திற்குள் பெறப்படாவிட்டால், அதன் நிலைமைக்குப் பொறுப்பாக இருக்க முடியாது, மேலும் அதை disassemble செய்வோம். உடனடியாக தொடர்பு கொள்ளவும்: 077 330 0905. 2 நாட்களுக்குள் உங்கள் TV ஐ பெற்றுக்கொள்ளாவிட்டால், உங்கள் அனைத்து தகவல்களும் எங்கள் SYSTEM இலிருந்து நீக்கப்படும்.";
                // $sms->Send();

                $sms->message = "හිතවත් පාරිභෝගිකයා,\nඔබගේ TV අලුත්වැඩියා අවසන්, ලබාගන්න. දින 2ක් ඇතුළත ලබාගන්න, නැතිනම් නීතිමය ක්‍රියාවලිය ක්‍රියාත්මක විය හැක. විමසීම්: 077 330 0905.";
                $sms->Send();
            }
        }

        foreach ($customerPendingData as $key => $value) {
            $customerData = customers::where('id', $value->customer)->first();
            if ($customerData != null && $value->partner == 0) {
                $sms = new SMS();
                $sms->contact = array(array(
                    "fname" => $customerData->name,
                    "lname" => "",
                    "group" => "",
                    "number" => $customerData->phone,
                    "email" => $customerData->email,
                ));

                $sms->message = "Dear Customer,\nWe have checked your TV and identified the fault at WeFix.LK. However, you have not confirmed the repair yet. Please collect your TV within 2 days. If not collected within this period, we will not be responsible for its condition and will proceed with disassembly. A service charge will apply for the inspection. IF YOU DO NOT COLLECT THE TV WITHIN 2 DAYS, ALL YOUR INFORMATION WILL BE DELETED FROM OUR SYSTEM. Contact us immediately at 077 330 0905.";
                $sms->Send();

                $sms->type = 'unicode';

                // $sms->message = "அன்புள்ள வாடிக்கையாளர்,\nஉங்கள் TV-ஐ WeFix.LK -ல் check செய்து fault அடையாளம் கண்டுள்ளோம். ஆனால், நீங்கள் இதுவரை repair செய்ய ஒப்புதல் தரவில்லை. தயவுசெய்து 2 நாட்கள் க்குள் உங்கள் TV-ஐ பெற்று செல்லவும். இல்லையெனில், அதன் நிலைமைக்குப் பொறுப்பாக இருக்க முடியாது, மேலும் அதை disassemble செய்வோம். Inspectionக்காக service charge அறவிடப்படும். 2 நாட்களுக்குள் உங்கள் TV ஐ பெறவில்லை என்றால், உங்கள் அனைத்து தகவல்களும் எங்கள் SYSTEM இலிருந்து நீக்கப்படும். உடனடியாக தொடர்பு கொள்ளவும்: 077 330 0905.";
                // $sms->Send();

                $sms->message = "හිතවත් පාරිභෝගිකයා,\nඔබගේ TV fault හඳුනාගෙන ඇත, repair කිරීම සඳහා ඔබේ අනුමැතිය නොමැත. කරුණාකර දින 2ක් ඇතුළත ලබාගන්න, නැතිනම් අපි disassemble කිරීමට ක්‍රියාකරනවා. Inspection සඳහා service charge අයකරනු ලැබේ. විමසීම්: 077 330 0905.";
                $sms->Send();
            }
        }

        foreach ($returnData as $key => $value) {
            $customerData = customers::where('id', $value->customer)->first();
            if ($customerData != null && $value->partner == 0) {
                $sms = new SMS();
                $sms->contact = array(array(
                    "fname" => $customerData->name,
                    "lname" => "",
                    "group" => "",
                    "number" => $customerData->phone,
                    "email" => $customerData->email,
                ));

                $sms->message = "Dear Customer,\nWe have checked your TV at WeFix.LK, but unfortunately, we are unable to repair it. Please collect your TV within 2 days. If not collected within this period, we will not be responsible for its condition and will proceed with disassembly. A service charge will be applicable for the inspection. IF YOU DO NOT COLLECT THE TV WITHIN 2 DAYS, ALL YOUR INFORMATION WILL BE DELETED FROM OUR SYSTEM. Contact us immediately at 077 330 0905.";
                $sms->Send();

                $sms->type = 'unicode';

                // $sms->message = "அன்புள்ள வாடிக்கையாளர்,\nஉங்கள் TV WeFix.LK -ல் check செய்யப்பட்டு, ஆனால் அதனை repair செய்ய முடியாது. தயவுசெய்து உங்கள் TV-ஐ 2 நாட்கள் க்குள் வந்து பெற்றுக்கொள்ளவும். அதற்கு பிறகு, அதன் நிலைமைக்குப் பொறுப்பாக இருக்க முடியாது, மேலும் அதை disassemble செய்வோம். Inspectionக்கான service charge விதிக்கப்படும். 2 நாட்களுக்குள் உங்கள் TV ஐ பெறவில்லை என்றால், உங்கள் அனைத்து தகவல்களும் எங்கள் SYSTEM இலிருந்து நீக்கப்படும். உடனடியாக தொடர்பு கொள்ளவும்: 077 330 0905.";
                // $sms->Send();

                $sms->message = "හිතවත් පාරිභෝගිකයා,\nඔබගේ TV check කර ඇති නමුත් repair කළ නොහැක. කරුණාකර දින 2ක් ඇතුළත ලබාගන්න, නැතිනම් disassemble කිරීමට ක්‍රියාකරනවා. Inspection සඳහා service charge අයකරනු ලැබේ. විමසීම්: 077 330 0905.";
                $sms->Send();
            }
        }
    }

    public function termination()
    {
        $repairedData = Repairs::where('status', 'Repaired')->whereDate('repaired_date', Carbon::now()->subDays(16))->delete();
        $customerPendingData = Repairs::where('status', 'Customer Pending')->whereDate('repaired_date', Carbon::now()->subDays(16))->delete();
        $returnData = Repairs::where('status', 'Return')->whereDate('repaired_date', Carbon::now()->subDays(16))->delete();
    }
}
