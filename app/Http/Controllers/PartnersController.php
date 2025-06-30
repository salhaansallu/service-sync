<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\partners;
use App\Models\Products;
use App\Models\Repairs;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class PartnersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->is_partner()) {
            $repairs["all"] = Repairs::where('partner', partner()->id)->count();
            $repairs["pending"] = Repairs::where('partner', partner()->id)->where('status', 'Pending')->count();
            $repairs["repaired"] = Repairs::where('partner', partner()->id)->where('status', 'Repaired')->count();
            $repairs["delivered"] = Repairs::where('partner', partner()->id)->where('status', 'Delivered')->count();

            $billNumbers = [];

            foreach (Repairs::where('partner', partner()->id)->where('status', 'Delivered')->get() as $key => $value) {
                $billNumbers[] = $value->bill_no;
            }

            $payments["pending"] = DB::table('credits')
                ->whereExists(function ($query) use ($billNumbers) {
                    $query->select(DB::raw(1))
                        ->from('repairs')
                        ->whereRaw('FIND_IN_SET(repairs.bill_no, credits.order_id) > 0')
                        ->whereIn('repairs.bill_no', $billNumbers);
                })
                ->sum('credits.ammount');

            $finishedRepairs = Repairs::where('partner', partner()->id)->where('status', 'Repaired')->limit(5)->get();

            return view('partner-portal.dashboard')->with(['repairs' => (object)$repairs, 'payments' => (object)$payments, 'finishedRepairs' => $finishedRepairs]);
        }
        return redirect()->route('partnerLogin');
    }

    public function getPartners()
    {
        $response = [];
        if (PosDataController::check()) {
            return response(json_encode(partners::where('pos_code', PosDataController::company()->pos_code)->get()));
        } else {
            $response['error'] = 1;
            $response['msg'] = "not_logged_in";
            return response(json_encode($response));
        }
    }

    public static function is_partner($get_details = false)
    {
        if (Session::has('WeFixPartner')) {
            $token = Session::get('WeFixPartner');
            if (!empty($token)) {
                $token = json_decode(Crypt::decrypt($token));
                if (is_object($token)) {
                    if (isset($token->id) && isset($token->user)) {
                        $verify = partners::where('username', '=', sanitize($token->user))->where('id', '=', sanitize($token->id))->limit(1)->get();
                        if ($verify->count() == 1) {
                            if ($get_details) {
                                return (object)array(
                                    "id" => $verify[0]->id,
                                    "name" => $verify[0]->name,
                                    "company" => $verify[0]->company,
                                    "phone" => $verify[0]->phone,
                                    "address" => $verify[0]->address,
                                    "email" => $verify[0]->email,
                                    "username" => $verify[0]->username,
                                    "pos_code" => $verify[0]->pos_code,
                                    "logo" => $verify[0]->logo,
                                    "created_at" => $verify[0]->created_at,
                                    "updated_at" => $verify[0]->updated_at,
                                );
                            }
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public static function getPartnerDetails()
    {
        return self::is_partner(true);
    }

    public static function login(Request $request)
    {
        $verify = partners::where('username', '=', sanitize($request->input('email')))->orWhere('email', '=', sanitize($request->input('email')))->limit(1)->get();
        if ($verify->count() == 1) {
            if (Hash::check(sanitize($request->input('password')), $verify[0]->password)) {
                Session::put('WeFixPartner', Crypt::encrypt(json_encode(array('user' => $verify[0]->username, 'id' => $verify[0]->id))));
                return redirect()->route('partnerDashboard');
            }
        }

        $error = array(
            "email" => sanitize($request->input('email')),
            "message" => "Invalid login credincials"
        );

        return view('partner-portal.login')->with('error', $error);;
    }

    public static function showLogin()
    {
        if (!self::is_partner()) {
            return view('partner-portal.login');
        }

        return redirect()->route('partnerDashboard');
    }

    public function listRepairs()
    {
        if (self::is_partner()) {
            $status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
            $repairs = [];
            if (!empty($status) && in_array(ucfirst(str_replace('-', ' ', $status)), ['Repaired', 'Pending', 'Delivered', 'Customer Pending', 'Awaiting Parts', 'Return'])) {
                $repairs = Repairs::where('partner', partner()->id)->where('status', ucfirst(str_replace('-', ' ', $status)))->get();
            } else {
                $repairs = Repairs::where('partner', partner()->id)->where('status', 'Repaired')->get();
            }

            return view('partner-portal.list-repairs')->with(['repairs' => $repairs]);
        }
        return redirect()->route('partnerLogin');
    }

    public function displayRepair($id)
    {
        if (self::is_partner()) {
            $id = sanitize($id);

            if (!empty($id)) {
                $repairs = Repairs::where('partner', partner()->id)->where('id', $id)->get();
                if ($repairs->count() > 0) {
                    $spares = Products::where('pos_code', partner()->pos_code)->get();
                    return view('partner-portal.add-repairs')->with(['repairs' => $repairs[0], 'spares' => $spares]);
                }
            }
            return display404();
        }
        return redirect()->route('partnerLogin');
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
            $logo = sanitize($request->input('logo'));
            $name = sanitize($request->input('name'));
            $company = sanitize($request->input('company'));
            $phone = sanitize($request->input('phone'));
            $address = sanitize($request->input('address'));
            $email = sanitize($request->input('email'));
            $username = sanitize($request->input('username'));
            $password = sanitize($request->input('password'));
            $imageName = null;

            if (empty($name)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }
            if (!empty($phone) && !is_numeric($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for phone number")));
            }

            if (!empty(sanitize($request->input('subscription_amount'))) && !is_numeric(sanitize($request->input('subscription_amount')))) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for subscription amount")));
            }

            if (!empty($email) && partners::where('email', $email)->where('pos_code', company()->pos_code)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Email already in use")));
            }

            if (!empty($username) && partners::where('username', $username)->where('pos_code', company()->pos_code)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Username already in use")));
            }

            if (!empty($phone) && partners::where('phone', $phone)->where('pos_code', company()->pos_code)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Phone number already in use")));
            }

            if ($request->hasFile('logo')) {
                $extension = $request->file('logo')->getClientOriginalExtension();
                if (in_array($extension, array('png', 'jpeg', 'jpg'))) {
                    $imageName = time() . rand(11111, 99999999) . '.' . $request->logo->extension();
                    $request->logo->move(public_path('user_profile'), $imageName);
                } else {
                    return response(json_encode(array("error" => 1, "msg" => "Please select 'png', 'jpeg', or 'jpg' type image")));
                }
            }

            $subscription_options = array(
                "No Subscription" => 0,
                "Daily"           => 1,
                "Weekly"          => 7,
                "2 Weeks"         => 14,
                "Monthly"         => 30,   // Approximate month
                "3 Months"        => 90,   // 3 × 30
                "6 Months"        => 180,  // 6 × 30
                "9 Months"        => 270,  // 9 × 30
                "12 Months"       => 365   // Full year
            );



            $partner = new partners();
            $partner->name = $name;
            $partner->company = $company;
            $partner->phone = $phone;
            $partner->address = $address;
            $partner->email = $email;
            $partner->username = $username;
            $partner->logo = $logo;
            if (!empty($password)) {
                $partner->password = Hash::make($password);
            }
            $partner->subscription_frequency = $subscription_options[sanitize($request->input('subscription_frequency'))];
            $partner->subscription_amount = sanitize($request->input('subscription_amount'));
            $partner->pos_code = company()->pos_code;

            if ($partner->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Partner Created Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(partners $partners)
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

        $customer = partners::where('id', sanitize($id))->where('pos_code', company()->pos_code)->get();

        if ($customer && $customer->count() > 0) {
            return view('pos.add-partner')->with('user', $customer[0]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('modelid'));
            $logo = sanitize($request->input('logo'));
            $name = sanitize($request->input('name'));
            $company = sanitize($request->input('company'));
            $phone = sanitize($request->input('phone'));
            $address = sanitize($request->input('address'));
            $email = sanitize($request->input('email'));
            $username = sanitize($request->input('username'));
            $password = sanitize($request->input('password'));
            $imageName = null;

            if (empty($name)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            if (!empty($phone) && !is_numeric($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for phone number")));
            }

            $id_verify = partners::where('id', $id)->where('pos_code', company()->pos_code)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            if (!empty($email) && partners::where('email', $email)->where('pos_code', company()->pos_code)->where('id', "!=", $id)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Email already in use")));
            }

            if (!empty($username) && partners::where('username', $username)->where('pos_code', company()->pos_code)->where('id', "!=", $id)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Username already in use")));
            }

            if (!empty($phone) && partners::where('phone', $phone)->where('pos_code', company()->pos_code)->where('id', "!=", $id)->count() > 0) {
                return response(json_encode(array("error" => 1, "msg" => "Phone number already in use")));
            }

            if (!empty(sanitize($request->input('subscription_amount'))) && !is_numeric(sanitize($request->input('subscription_amount')))) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for subscription amount")));
            }

            if ($request->hasFile('logo')) {
                $extension = $request->file('logo')->getClientOriginalExtension();
                if (in_array($extension, array('png', 'jpeg', 'jpg'))) {
                    $imageName = time() . rand(11111, 99999999) . '.' . $request->logo->extension();
                    $request->logo->move(public_path('user_profile'), $imageName);
                } else {
                    return response(json_encode(array("error" => 1, "msg" => "Please select 'png', 'jpeg', or 'jpg' type image")));
                }
            }

            $subscription_options = array(
                "No Subscription" => 0,
                "Daily"           => 1,
                "Weekly"          => 7,
                "2 Weeks"         => 14,
                "Monthly"         => 30,   // Approximate month
                "3 Months"        => 90,   // 3 × 30
                "6 Months"        => 180,  // 6 × 30
                "9 Months"        => 270,  // 9 × 30
                "12 Months"       => 365   // Full year
            );

            $update_arr = [
                "name" => $name,
                "company" => $company,
                "phone" => $phone,
                "address" => $address,
                "email" => $email,
                "username" => $username,
                "subscription_frequency" => $subscription_options[sanitize($request->input('subscription_frequency'))],
                "subscription_amount" => sanitize($request->input('subscription_amount')),
            ];

            if (!empty($password)) {
                $update_arr["password"] = Hash::make($password);
            }

            if ($request->hasFile('logo')) {
                $update_arr["logo"] = $imageName;
            }

            $customer = partners::where('id', $id)->update($update_arr);

            if ($customer) {
                return response(json_encode(array("error" => 0, "msg" => "Partner Updated Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $id = sanitize($request->input('id'));
            $verify = partners::where('id', $id)->where('pos_code', company()->pos_code);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Partner deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Partner not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }

    public function logoUpdate(Request $request)
    {
        if (self::is_partner()) {
            if ($request->hasFile('logo')) {
                $extension = $request->file('logo')->getClientOriginalExtension();
                if (in_array($extension, array('png', 'jpeg', 'jpg'))) {
                    $imageName = time() . rand(11111, 99999999) . '.' . $request->logo->extension();
                    $request->logo->move(public_path('user_profile'), $imageName);

                    $update_arr = [
                        "logo" => $imageName,
                    ];

                    $customer = partners::where('id', partner()->id)->update($update_arr);

                    if ($customer) {
                        return response(json_encode(array("error" => 0, "msg" => "Logo Updated Successfully")));
                    }
                } else {
                    return response(json_encode(array("error" => 1, "msg" => "Please select 'png', 'jpeg', or 'jpg' type image")));
                }
            }

            return response(json_encode(array("error" => 1, "msg" => "Please select an image")));
        }
    }

    public function getReport()
    {
        if (isAdmin()) {
            $from = sanitize(request()->input('from'));
            $to   = sanitize(request()->input('to'));
            $partnerId = sanitize(request()->input('partner_id'));

            $from = Carbon::parse($from)->startOfDay();
            $to   = Carbon::parse($to)->endOfDay();

            // Repairs filtered by partner and date
            $repairs = DB::table('repairs')
                ->leftJoin('partners', 'repairs.partner', '=', 'partners.id')
                ->where('repairs.partner', $partnerId)
                ->whereBetween('repairs.created_at', [$from, $to])
                ->orderBy('repairs.created_at', 'desc')
                ->select('repairs.*', 'partners.name as partner_name', 'partners.company')
                ->get();

            // Credits related to the partner’s repairs
            $credits = DB::table('credits')
                ->join('repairs', 'credits.order_id', '=', 'repairs.bill_no')
                ->where('repairs.partner', $partnerId)
                ->whereBetween('credits.created_at', [$from, $to])
                ->select('credits.*', 'repairs.customer', 'repairs.model_no')
                ->orderBy('credits.created_at', 'desc')
                ->get();

            // Credit histories for customers in this date range (no partner_id directly, optional join logic)
            $credit_histories = DB::table('credit_histories')
                ->whereBetween('created_at', [$from, $to])
                ->orderBy('created_at', 'desc')
                ->get();

            $html = View::make('pos.repair_credit_report', compact('repairs', 'credits', 'credit_histories', 'from', 'to'))->render();

            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = 'partner_filtered_report.pdf';
            $path = public_path('invoice/' . $filename);
            file_put_contents($path, $dompdf->output());

            return response()->json([
                'error' => 0,
                'msg' => 'Report generated successfully',
                'url' => $filename,
            ]);
        }
    }

    public static function runSubscription()
    {
        $partners = partners::where('subscription_frequency', '!=', 0)->get();
        $date = Carbon::today();

        $parts[] = 'temp';

        foreach ($partners as $key => $partner) {

            $update = false;

            if (!$partner->next_due) {
                $partner->last_activation = $date;
                $partner->next_due = Carbon::today()->addDays($partner->subscription_frequency)->format('Y-m-d');
                $partner->save();

                $update = true;
            }

            if ($partner->next_due && Carbon::parse($partner->next_due)->format('Y-m-d') <= $date->format('Y-m-d')) {
                $partner->last_activation = $date;
                $partner->next_due = Carbon::parse($partner->next_due)->addDays($partner->subscription_frequency)->format('Y-m-d');
                $partner->save();

                $update = true;
            }

            if ($update) {
                $invoice_pro[] = array(
                    "name" => "Subscription of " . $date,
                    "unit" => $partner->subscription_amount,
                    "cost" => 0,
                    "qty" => "1",
                    "sku" => "temp",
                    "id" => "temp",
                );

                $bill_no = 1001;
                $getBillNo = Repairs::orderBy('id', 'DESC')->first();
                $bill_no = $getBillNo && $getBillNo->count() > 0 ? (int)$getBillNo->bill_no + 1 : 1001;

                $repair = new Repairs();
                $repair->bill_no = $bill_no;
                $repair->model_no = "";
                $repair->serial_no = "";
                $repair->fault = "";
                $repair->note = "";
                $repair->advance = 0;
                $repair->spares = json_encode($parts);
                $repair->total = $partner->subscription_amount;
                $repair->cost = 0;
                $repair->customer = 0;
                $repair->partner = $partner->id;
                $repair->cashier = 0;
                $repair->status = "Delivered";
                $repair->type = "sale";
                $repair->paid_at = date('Y-m-d H:i:s');
                $repair->repaired_date = date('Y-m-d H:i:s');
                $repair->products = htmlspecialchars(json_encode($invoice_pro));
                $repair->pos_code = company()->pos_code;

                if ($repair->save()) {

                    $credit = new Credit();
                    $credit->customer_id = 0;
                    $credit->ammount = $partner->subscription_amount;
                    $credit->pos_code = company()->pos_code;
                    $credit->order_id = $bill_no;
                    $credit->save();

                    $rand = date('d-m-Y-h-i-s') . '-' . rand(0, 9999999) . '.pdf';

                    $inName = str_replace(' ', '-', str_replace('.', '-', $bill_no)) . '-Invoice-' . $rand;
                    $ThermalInName = str_replace(' ', '-', str_replace('.', '-', $bill_no)) . '-Thermal-invoice-' . $rand;

                    $generate_invoice = generateSalesInvoice($bill_no, $inName, $invoice_pro, 0);
                    $generate_thermal_invoice = generateThermalSalesInvoice($bill_no, $ThermalInName, json_encode($invoice_pro), 0);

                    if ($generate_invoice->generated == true) {

                        Repairs::where('bill_no', $bill_no)->where('pos_code', company()->pos_code)->update([
                            "invoice" => 'checkout/' . $inName,
                        ]);
                    }
                }
            }
        }

        $noSubPartners = partners::where('subscription_frequency', 0)->get();
        foreach ($noSubPartners as $key => $partner) {
            $partner->next_due = null;
            $partner->save();
        }
    }
}
