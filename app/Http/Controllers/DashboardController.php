<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\customers;
use App\Models\orderProducts;
use App\Models\orders;
use App\Models\partners;
use App\Models\personalCredits;
use App\Models\posData;
use App\Models\posUsers;
use App\Models\ProductPurchases;
use App\Models\Products;
use App\Models\Purchases;
use App\Models\quotations;
use App\Models\Repairs;
use App\Models\shippers;
use App\Models\spareSaleHistory;
use App\Models\supplier;
use App\Models\User;
use App\Models\userData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index($id)
    {
        if (Auth::check()) {
            $code = UrlToCode(sanitize($id));
            if ($code == get_Cookie("admin_session")) {
                if ($this->login($code)) {
                    return redirect("/dashboard");
                }
                return response()->view('errors.404')->setStatusCode(404);
            }
            return response()->view('errors.404')->setStatusCode(404);
        }
        return response()->view('errors.404')->setStatusCode(404);
    }

    public static function check($plan_verify = false)
    {
        if ($plan_verify) {
            if (Auth::check() && Auth::user()->id == PosDataController::company()->admin_id) {
                if (get_Cookie('admin_session') != false) {
                    $check = posData::where('admin_id', Auth::user()->id)->where('pos_code', Crypt::decrypt(get_Cookie('admin_session')))->get();
                    if (($check && $check->count() > 0 && company()->plan > 1 && company()->expiry_date == 'false') || ($check && $check->count() > 0 && company()->expiry_date != 'false' && date('Y-m-d h:i:s', strtotime(company()->expiry_date)) > date('Y-m-d h:i:s'))) {
                        return true;
                    }
                    return false;
                }
                return false;
            }
            return false;
        }

        if (Auth::check() && Auth::user()->id == PosDataController::company()->admin_id) {
            if (get_Cookie('admin_session') != false) {
                $check = posData::where('admin_id', Auth::user()->id)->where('pos_code', Crypt::decrypt(get_Cookie('admin_session')))->get();
                if ($check && $check->count() > 0) {
                    return true;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    public static function login($code)
    {
        $verify = posData::where('admin_id', Auth::user()->id)->get();
        if (Crypt::decrypt($code) == $verify[0]->pos_code) {
            set_Cookie('pos_session', $code);
            return true;
        }
        return response()->view('errors.404')->setStatusCode(404);
    }

    public function dashboard()
    {
        login_redirect('/account/overview');
        if (Auth::check() && $this->check()) {

            if (company()->expiry_date != 'false' && date('Y-m-d h:i:s', strtotime(company()->expiry_date)) < date('Y-m-d h:i:s')) {
                return redirect('/account/overview');
            }

            (float)$todaysales = 0;
            (float)$cost = 0;
            $sales = array();
            $yearcost = array();
            $yearexpense = array();
            $company = posData::where('admin_id', Auth::user()->id)->get()[0];
            $todaysalesqry = Repairs::where('pos_code', $company->pos_code)->where('status', 'Delivered')->whereDate('paid_at', Carbon::today())->get();
            $low_stock = Products::where('pos_code', $company->pos_code)->where('qty', '<=', 3)->limit(4)->get();
            //$best_selling = DB::table('order_products')->select('*')->where('pos_id', $company->pos_code)->leftJoin('orders', 'order_products.order_id', '=', 'orders.order_number')->where('pos_code', $company->pos_code)->groupBy('sku')->orderByDesc('qty')->limit(3)->get();
            $best_selling = [];

            $todayRepairsIn = Repairs::whereDate('created_at', Carbon::today())->count();
            $todayRepaired = Repairs::whereDate('repaired_date', Carbon::today())->count();
            $todayRepairsOut = Repairs::whereDate('paid_at', Carbon::today())->count();

            if ($todaysalesqry && $todaysalesqry->count() > 0) {
                foreach ($todaysalesqry as $key => $qry) {
                    (float)$todaysales += (float)$qry['total'];
                    (float)$cost += $qry['cost'];
                }
            }

            $salesqry = Repairs::where('pos_code', $company->pos_code)->where('status', 'Delivered')->whereYear('paid_at', date('Y'))->orderBy('paid_at', 'ASC')->get();

            if ($salesqry && $salesqry->count() > 0) {
                foreach ($salesqry as $key => $sale) {
                    if (array_key_exists(date('M', strtotime($sale['paid_at'])), $sales)) {
                        (float)$sales[date('M', strtotime($sale['paid_at']))] += (float)$sale['total'];
                    } else {
                        (float)$sales[date('M', strtotime($sale['paid_at']))] = 0;
                        (float)$sales[date('M', strtotime($sale['paid_at']))] += (float)$sale['total'];
                    }

                    if (array_key_exists(date('M', strtotime($sale['paid_at'])), $yearcost)) {
                        (float)$yearcost[date('M', strtotime($sale['paid_at']))] += (float)$sale['cost'];
                    } else {
                        (float)$yearcost[date('M', strtotime($sale['paid_at']))] = 0;
                        (float)$yearcost[date('M', strtotime($sale['paid_at']))] += (float)$sale['cost'];
                    }
                }
            }


            $expenses = Purchases::where('pos_code', $company->pos_code)->whereYear('created_at', date('Y'))->orderBy('created_at', 'ASC')->get();

            if ($expenses && $expenses->count() > 0) {
                foreach ($expenses as $key => $expense) {
                    if (array_key_exists(date('M', strtotime($expense['created_at'])), $yearexpense)) {
                        (float)$yearexpense[date('M', strtotime($expense['created_at']))] += (float)(($expense['price'] - $expense['discount']) * $expense['qty']) + $expense['shipping_charge'];
                    } else {
                        (float)$yearexpense[date('M', strtotime($expense['created_at']))] = 0;
                        (float)$yearexpense[date('M', strtotime($expense['created_at']))] += (float)(($expense['price'] - $expense['discount']) * $expense['qty']) + $expense['shipping_charge'];
                    }
                }
            }


            return view('pos.dashboard')->with(['company' => $company, 'todaysales' => $todaysales, 'cost' => $cost, 'sales' => json_encode($sales), 'low_stock' => $low_stock, 'yearcost' => json_encode($yearcost), 'yearexpense' => json_encode($yearexpense), 'best_sellings' => $best_selling, 'todayRepairsIn'=>$todayRepairsIn, 'todayRepaired'=>$todayRepaired, 'todayRepairsOut'=>$todayRepairsOut]);
        } else {
            return redirect('/account/overview');
        }
    }

    public function listProducts()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {
            $products = Products::where('pos_code', company()->pos_code)->get();
            return view('pos.list-product')->with(['products' => $products]);
        } else {
            return redirect('/signin');
        }
    }

    public function createProduct()
    {
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-product');
        } else {
            return redirect('/signin');
        }
    }

    public function listLowStockProducts()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && isCashier()) {
            $low = isset($_GET['qty'])? sanitize($_GET['qty']) : 5;
            $products = Products::where('qty', '<=', (int)$low)->get();
            return view('pos.list-stock')->with(['stocks' => $products]);
        } else {
            return redirect('/signin');
        }
    }

    public function generateLowStockReport(Request $request)
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && isAdmin()) {

            $low = $request->has('qty')? sanitize($request->input('qty')) : 5;

            $report = generateLowStockReport($low);
            if($report->generated) {
                return response(json_encode(['error'=>'0', 'url' => $report->url]));
            }
            return response(json_encode(['error'=>'1', 'msg' => 'Error generating report']));
        } else {
            return redirect('/account/overview');
        }
    }

    public function listCategories()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {
            $categories = Categories::where('pos_code', company()->pos_code)->get();
            return view('pos.list-categories')->with(['categories' => $categories]);
        } else {
            return redirect('/signin');
        }
    }

    public function listrepairs(Request $request)
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {
            $categories = [];

            if ($request->path() == 'dashboard/repairs/other-repairs') {
                if ($request->has('s')) {
                    $s = sanitize($request->input('s'));
                    $categories = DB::table('repairs')
                    ->leftJoin('partners', 'repairs.partner', '=', 'partners.id') // Use LEFT JOIN
                    ->leftJoin('customers', 'repairs.customer', '=', 'customers.id') // Use LEFT JOIN
                    ->where(function ($query) use ($s) {
                        $query->where('partners.name', 'LIKE', "%$s%")
                            ->orWhere('partners.company', 'LIKE', "%$s%")
                            ->orWhere('customers.name', 'LIKE', "%$s%")
                            ->orWhere('customers.phone', 'LIKE', "%$s%")
                            ->orWhere('repairs.bill_no', 'LIKE', "%$s%")
                            ->orWhere('repairs.model_no', 'LIKE', "%$s%")
                            ->orWhere('repairs.serial_no', 'LIKE', "%$s%");
                    })
                    ->where('repairs.type', 'other') // Ensure `type="other"`
                    ->select('repairs.*', 'partners.name as partner_name', 'customers.name as customer_name')
                    ->paginate(10);

                } else {
                    $categories = Repairs::where('pos_code', company()->pos_code)->where('type', '=', 'other')->paginate(10);
                }

                return view('pos.list-categories')->with(['repairs' => $categories]);
            }

            $categories = [];
            if ($request->has('s')) {
                $s = sanitize($request->input('s'));
                $categories = DB::table('repairs')
                ->leftJoin('partners', 'repairs.partner', '=', 'partners.id') // Use LEFT JOIN
                ->leftJoin('customers', 'repairs.customer', '=', 'customers.id') // Use LEFT JOIN
                ->where(function ($query) use ($s) {
                    $query->where('partners.name', 'LIKE', "%$s%")
                        ->orWhere('partners.company', 'LIKE', "%$s%")
                        ->orWhere('customers.name', 'LIKE', "%$s%")
                        ->orWhere('customers.phone', 'LIKE', "%$s%")
                        ->orWhere('repairs.bill_no', 'LIKE', "%$s%")
                        ->orWhere('repairs.model_no', 'LIKE', "%$s%")
                        ->orWhere('repairs.serial_no', 'LIKE', "%$s%");
                })
                ->where('repairs.type', 'repair') // Ensure `type="repair"`
                ->select('repairs.*', 'partners.name as partner_name', 'customers.name as customer_name')
                ->paginate(10);

                // $categories = Repairs::where('pos_code', company()->pos_code)->where('type', '=', 'repair')->when($s, function($qry, $s) {
                //     return $qry->where('bill_no', 'like', "%{$s}%")
                //     ->orWhere('model_no', 'like', "%{$s}%")
                //     ->orWhere('serial_no', 'like', "%{$s}%");
                // })->paginate(100000);
            } else {
                $categories = Repairs::where('pos_code', company()->pos_code)->where('type', '=', 'repair')->paginate(10);
            }

            return view('pos.list-categories')->with(['repairs' => $categories]);
        } else {
            return redirect('/signin');
        }
    }

    public function listQuotations()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {
            $company = company();
            $results = DB::select('select quotations.total AS quote_total, quotations.bill_no AS quote_bill, quotations.id AS q_id, quotations.*, repairs.* from quotations, repairs WHERE (quotations.bill_no = repairs.bill_no OR quotations.bill_no = "custom") AND quotations.pos_code = "' . $company->pos_code . '" AND repairs.pos_code = "' . $company->pos_code . '" GROUP BY quotations.q_no ORDER BY quotations.id DESC');
            return view('pos.list-quotations')->with(['quotations' => $results]);
        } else {
            return redirect('/signin');
        }
    }

    public function listOrders()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {
            $categories = orders::where('pos_code', company()->pos_code)->get();
            return view('pos.list-orders')->with(['repairs' => $categories]);
        } else {
            return redirect('/signin');
        }
    }

    public function listBills()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {
            $categories = Repairs::where('pos_code', company()->pos_code)->where('type', '=', 'sale')->get();
            return view('pos.list-bills')->with(['repairs' => $categories]);
        } else {
            return redirect('/signin');
        }
    }

    public function createCategories()
    {
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-category');
        } else {
            return redirect('/signin');
        }
    }

    public function salesReport()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {

            //$sales = Repairs::where('pos_code', company()->pos_code)->where('status', 'Delivered')->whereDate('created_at', Carbon::today())->orderBy('created_at', 'DESC')->get();
            $customers = customers::where('pos_code', company()->pos_code)->get();
            $pos_user = DB::table('pos_users')->select('pos_users.*', 'users.fname', 'users.lname', 'users.id', 'users.email')->where('pos_code', company()->pos_code)->leftJoin('users', 'pos_users.user_id', '=', 'users.id')->get();

            $result = [];

            return view('pos.sales')->with(['sales' => json_encode($result), 'customers' => json_encode($customers), 'cahiers' => json_encode($pos_user)]);
        } else {
            return redirect('/account/overview');
        }
    }

    public function spareReport()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {

            $result = DB::table('spare_sale_histories')
                ->select('spare_id', 'spare_code', 'spare_name', DB::raw('SUM(qty) as total_sold'), DB::raw('SUM(qty*cost) as total_revenew'))
                ->where('pos_code', company()->pos_code) // filter by POS code if necessary
                ->whereDate('created_at', '=', Carbon::today()) // filter by date range
                ->groupBy('spare_id', 'spare_name') // group by unique item
                ->get();

            return view('pos.spare-report')->with(['reports' => json_encode($result)]);
        } else {
            return redirect('/account/overview');
        }
    }

    public function customerSalesReport()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {
            $customers = customers::where('pos_code', company()->pos_code)->get();
            $pos_user = DB::table('pos_users')->select('pos_users.*', 'users.fname', 'users.lname', 'users.id', 'users.email')->where('pos_code', company()->pos_code)->leftJoin('users', 'pos_users.user_id', '=', 'users.id')->get();

            return view('pos.customer-sales')->with(['customers' => json_encode($customers), 'cahiers' => json_encode($pos_user)]);
        } else {
            return redirect('/account/overview');
        }
    }

    public function getSalesProducts(Request $request)
    {

        if (Auth::check() && $this->check(true)) {

            $report = Repairs::where('bill_no', sanitize($request->input('params')['bill_no']))->where('pos_code', company()->pos_code)->get();
            $result = [];
            if ($report[0]->spares != NULL) {
                foreach (json_decode($report[0]->spares) as $key => $value) {
                    $data = Products::where('id', $value)->get();

                    if ($data->count() > 0) {
                        $result[] = $data[0];
                    }
                }

                return response(json_encode($result));
            }
            return response(json_encode([]));
        } else {
            return response(json_encode(array("Not Logged In")));
        }
    }

    public function getSalesInvoice(Request $request)
    {
        $fromdate = sanitize($request->input('params')['fromdate']);
        $todate = sanitize($request->input('params')['todate']);
        $customer = sanitize($request->input('params')['customer']);
        $cashier = sanitize($request->input('params')['cashier']);

        if ($customer == "0") {
            $customer = ' WHERE ';
        } elseif (customers::where('id', $customer)->where('pos_code', company()->pos_code)->get()->count() > 0) {
            $customer = ' WHERE customer = ' . $customer . ' AND ';
        } else {
            return 0;
        }

        if ($cashier == "0") {
            $cashier = ' ';
        } elseif (posUsers::where('user_id', $cashier)->where('pos_code', company()->pos_code)->get()->count() > 0) {
            $cashier = ' cashier = ' . $cashier . ' AND ';
        } else {
            return 0;
        }

        $reports = DB::select('select * from repairs ' . $customer . $cashier . ' pos_code = "' . company()->pos_code . '" AND status = "Delivered" AND paid_at BETWEEN "' . date('Y-m-d', strtotime($fromdate)) . ' 00:00:00" AND "' . date('Y-m-d', strtotime($todate)) . ' 23:59:59"');

        //dd($result);
        return response(json_encode($reports));
    }

    public function getCustomerInvoice(Request $request)
    {
        $cus_no = sanitize($request->input('params')['cus_no']);
        $customer = sanitize($request->input('params')['customer']);

        if (!empty($cus_no)) {
            $customer = customers::where('phone', $cus_no)->where('pos_code', company()->pos_code)->get();
            if ($customer->count() > 0) {
                return response(json_encode(DB::select('select * from repairs where customer = "' . $customer[0]['id'] . '" AND pos_code = "' . company()->pos_code . '"')));
            } else {
                return response(json_encode([]));
            }
        }

        if (!empty($customer)) {
            return response(json_encode(DB::select('select * from repairs where customer = "' . $customer . '" AND pos_code = "' . company()->pos_code . '"')));
        }

        return response(json_encode([]));
    }

    public function listPurchses()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {
            $purchses = Purchases::where('pos_code', company()->pos_code)->get();
            return view('pos.list-purchase')->with(['purchses' => $purchses]);
        } else {
            return redirect('/signin');
        }
    }

    public function createPurchse()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-purchase');
        } else {
            return redirect('/signin');
        }
    }

    public function listPersonalCredit()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {
            $purchses = [];
            if (isset($_GET['status']) && sanitize($_GET['status']) == 'all') {
                $purchses = personalCredits::all();
            }
            else {
                $purchses = personalCredits::where('status', 'Delivered')->get();
            }
            return view('pos.list-personalCredits')->with(['purchses' => $purchses]);
        } else {
            return redirect('/signin');
        }
    }

    public function createPersonalCredit()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-personalCredit')->with(['bills'=>Repairs::all()]);
        } else {
            return redirect('/signin');
        }
    }

    public function listProductPurchses()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && isAdmin()) {
            $purchses = [];
            if(isset($_GET['q'])){
                if (sanitize($_GET['q']) != 'all') {
                    $purchses = ProductPurchases::where('status', sanitize($_GET['q']))->get();
                    return view('pos.list-productPurchase')->with(['purchses' => $purchses]);
                }
                else {
                    $purchses = ProductPurchases::all();
                }
            }
            else {
                $purchses = ProductPurchases::where('status', 'pending')->get();
            }
            return view('pos.list-productPurchase')->with(['purchses' => $purchses]);
        } else {
            return redirect('/signin');
        }
    }

    public function createProductPurchse()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && isAdmin()) {
            return view('pos.add-productPurchase');
        } else {
            return redirect('/signin');
        }
    }

    public function listCustomers()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            $customers = customers::where('pos_code', company()->pos_code)->get();
            return view('pos.list-customers')->with(['customers' => $customers]);
        } else {
            return redirect('/signin');
        }
    }

    public function listPartners()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            $customers = partners::where('pos_code', company()->pos_code)->get();
            return view('pos.list-partners')->with(['partners' => $customers]);
        } else {
            return redirect('/signin');
        }
    }

    public function createCustomer()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-customer');
        } else {
            return redirect('/signin');
        }
    }

    public function createShipper()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-shipper');
        } else {
            return redirect('/signin');
        }
    }

    public function listShipper()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            $shippers = shippers::all();
            return view('pos.list-shippers')->with(['shippers' => $shippers]);
        } else {
            return redirect('/signin');
        }
    }

    public function createPartner()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-partner');
        } else {
            return redirect('/signin');
        }
    }

    public function listUsers()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            $users = DB::table('users')->select('pos_users.*', 'users.fname', 'users.lname', 'users.id', 'users.email')->leftJoin('pos_users', 'users.id', '=', 'pos_users.user_id')->whereNot('user_id', Auth::user()->id)->where('pos_code', company()->pos_code)->get();
            return view('pos.list-users')->with(['users' => $users]);
        } else {
            return redirect('/signin');
        }
    }

    public function createUsers()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-users');
        } else {
            return redirect('/signin');
        }
    }

    public function listSuppliers()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            $suppliers = supplier::where('pos_code', company()->pos_code)->get();
            return view('pos.list-suppliers')->with(['suppliers' => $suppliers]);
        } else {
            return redirect('/signin');
        }
    }

    public function createSuppliers()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-supplier');
        } else {
            return redirect('/signin');
        }
    }

    public function updateUser()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check()) {
            $posData = posData::where('admin_id', Auth::user()->id)->where('pos_code', company()->pos_code)->get();
            $userData = userData::where('user_id', Auth::user()->id)->get();
            return view('pos.user-update')->with(['posData' => $posData[0], 'userData' => $userData[0]]);
        }
    }

    public function updateUserDetails(Request $request)
    {
        if (Auth::check() && $this->check()) {
            $country = sanitize($request->input('country'));
            $address = sanitize($request->input('address'));
            $city = sanitize($request->input('city'));
            $zip = sanitize($request->input('zip'));
            $phone = sanitize($request->input('phone'));
            $imageName = "";

            if (empty($country) || empty($address) || empty($city) || empty($zip) || empty($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please fill all fields")));
            }

            if (country($country) == false) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Country")));
            }

            if (!is_numeric($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please only use numbers for phone number")));
            }

            if ($request->hasFile('profile_pic')) {
                $extension = $request->file('profile_pic')->getClientOriginalExtension();
                if (in_array($extension, array('png', 'jpeg', 'jpg'))) {
                    $imageName = time() . rand(11111, 99999999) . '.' . $request->profile_pic->extension();
                    $request->profile_pic->move(public_path('user_profile'), $imageName);
                } else {
                    return response(json_encode(array("error" => 1, "msg" => "Please select 'png', 'jpeg', or 'jpg' type image")));
                }
            }

            $userData = '';

            if ($request->hasFile('profile_pic')) {
                $userData = userData::where('user_id', Auth::user()->id)->update([
                    "country" => $country,
                    "address" => $address,
                    "city" => $city,
                    "zip" => $zip,
                    "phone" => $phone,
                    "profile" => $imageName,
                ]);
            } else {
                $userData = userData::where('user_id', Auth::user()->id)->update([
                    "country" => $country,
                    "address" => $address,
                    "city" => $city,
                    "zip" => $zip,
                    "phone" => $phone,
                ]);
            }


            if ($userData) {
                return response(json_encode(array("error" => 0, "msg" => "Details updated successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }

    public function updateCompanyDetails(Request $request)
    {
        if (Auth::check() && $this->check()) {
            $company = sanitize($request->input('name'));
            $industry = sanitize($request->input('industry'));
            $country = sanitize($request->input('country'));
            $city = sanitize($request->input('city'));
            $currency = sanitize($request->input('currency'));

            if (empty($company) || empty($industry) || empty($country) || empty($city) || empty($currency)) {
                return response(json_encode(array("error" => 1, "msg" => "Please fill all fields")));
            }

            if (!in_array($currency, array('LKR', 'USD', 'GBP', 'EUR', 'INR'))) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Currency")));
            }

            if (country($country) == false) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Country")));
            }

            $userData = posData::where('admin_id', Auth::user()->id)->where('pos_code', company()->pos_code)->update([
                "company_name" => $company,
                "industry" => $industry,
                "country" => $country,
                "city" => $city,
                "currency" => $currency,
            ]);

            if ($userData) {
                return response(json_encode(array("error" => 0, "msg" => "Details updated successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }

    public function InvoiceSettings(Request $request)
    {
        if (Auth::check() && $this->check()) {
            return view('pos.bill-settings')->with(['settings' => POSSettings()]);
        }
    }

    public function generateInvoice(Request $request)
    {
        if (Auth::check() && $this->check()) {
            $tempBill = 'temp-bulk-invoice';
            $invoice = generateThermalInvoice($request->input('invoice'), $tempBill, 'newOrder');

            if ($invoice->generated == true) {
                return response(json_encode(array("error" => 0, "msg" => "Invoice generated successfully", "url"=>$invoice->url)));
            }
        }

        return response(json_encode(array("error" => 1, "msg" => "Error generating invoice")));
    }
}
