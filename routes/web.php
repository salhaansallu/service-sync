<?php

use App\Http\Controllers\accountController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ChinaOrdersController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\PosDataController;
use App\Http\Controllers\PosInvitationController;
use App\Http\Controllers\POSSettingsController;
use App\Http\Controllers\pricePlan;
use App\Http\Controllers\ProductPurchasesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\QuotationsController;
use App\Http\Controllers\RepairsController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\SpareSaleHistoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserDataController;
use App\Models\customers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Route::get('/sri-lanka', function () {
//     login_redirect('/sri-lanka');
//     return view('index');
// });

// Route::get('/about', function () {
//     login_redirect('/about');
//     return view('about');
// });

//Route::post('/newsletter', [NewsletterController::class, 'store']);

//Route::get('/contact', [ContactController::class, 'index'])->name('contact_view');
Route::get('/invitation/accept/{id}', [PosInvitationController::class, 'index']);
Route::get('/customer-copy/{type}/{pos_id}/{id}', function ($type, $pos_id, $id){
    $order = getOrder($type, $id, $pos_id);
    if ($order->error == 0) {
        return redirect('/invoice/'.$order->URL);
    }
    return response()->view('errors.404')->setStatusCode(404);
});

//Route::post('/contact', [ContactController::class, 'create'])->name('contact');

//Route::get('/create-account', [UserDataController::class, 'index']);

Route::get('/account/overview', [accountController::class, 'index']);
Route::get('/account/details', [accountController::class, 'details']);
Route::get('/account/logout', [accountController::class, 'logout']);
Route::post('/update-details', [accountController::class, 'updateDetails']);
Route::post('/update-password', [accountController::class, 'updatePassword']);

// Route::get('/pricing', function () {
//     login_redirect('/pricing');
//     return view('pricing');
// });

// Route::get('/pricing/{plan}', [pricePlan::class, 'index']);

// Route::get('/privacy-policy', function() {
//     return view('privacy');
// });

//Route::post('checkout', [paymentController::class, 'checkout']);




//============= POS Routes ==========////

Route::get('/cloud-pos/{id}', [PosDataController::class, 'index']);
Route::get('/pos', [PosDataController::class, 'show']);
Route::post('/pos/get_repairs', [RepairsController::class, 'getRepairs']);
Route::post('/pos/update', [RepairsController::class, 'orderUpdate']);
Route::post('/pos/new_order', [RepairsController::class, 'store']);
Route::post('/pos/get_customers', [CustomersController::class, 'getCustomers']);
Route::post('/pos/get_partners', [PartnersController::class, 'getPartners']);
Route::post('/pos/get_cashiers', [PosDataController::class, 'getCashiers']);
Route::post('/pos/pos_data', [PosDataController::class, 'getPosData']);
Route::post('/pos/get_spares', [ProductsController::class, 'getSpares']);
Route::post('/pos/bulk-print', [DashboardController::class, 'generateInvoice']);
//Route::post('/pos/get_favourits', [ProductsController::class, 'getFavourits']);
//Route::post('/pos/add-favourits', [ProductsController::class, 'addFavourits']);
//Route::post('/pos/remove-favourits', [ProductsController::class, 'removeFavourits']);
Route::post('/pos/checkout', [PosDataController::class, 'checkout']);
Route::post('/pos/sales/checkout', [PosDataController::class, 'salesCheckout']);
Route::post('/pos/getInvoicePDF', [RepairsController::class, 'getInvoicePDF']);
//Route::post('/pos/save', [PosDataController::class, 'save']);
//Route::post('/pos/get_saved_orders', [PosDataController::class, 'getSavedOrders']);


//============= Other Repairs Routes ==========////
Route::get('/other-pos', [PosDataController::class, 'OtherPOSshow']);
Route::post('/other-pos/get_repairs', [RepairsController::class, 'OtherPOSgetRepairs']);
Route::post('/other-pos/update', [RepairsController::class, 'orderUpdate']);
Route::post('/other-pos/new_order', [RepairsController::class, 'store']);
Route::post('/other-pos/get_customers', [CustomersController::class, 'getCustomers']);
Route::post('/other-pos/pos_data', [PosDataController::class, 'getPosData']);
Route::post('/other-pos/get_spares', [ProductsController::class, 'getSpares']);
Route::post('/other-pos/checkout', [PosDataController::class, 'checkout']);


//============= Dashboard Routes ==========////


Route::get('/pos-dashboard/{id}', [DashboardController::class, 'index']);
Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
Route::get('/', [DashboardController::class, 'dashboard']);

Route::get('/format-number', function() {
    if (Auth::check() && DashboardController::check()) {
        $customers = customers::all();

        foreach ($customers as $key => $customer) {
            customers::where('id', $customer->id)->update([
                "phone" => formatOriginalPhoneNumber($customer->phone) != null ? formatOriginalPhoneNumber($customer->phone) : $customer->phone,
            ]);
        }

        echo 'done';
    }
});

Route::prefix('dashboard')->group(function () {
    Route::get('products', [DashboardController::class, 'listProducts']);
    Route::get('products/create', [DashboardController::class, 'createProduct']);
    Route::get('products/edit/{id}', [ProductsController::class, 'edit']);
    Route::post('products/create', [ProductsController::class, 'store']);
    Route::post('/products/edit', [ProductsController::class, 'update']);
    Route::delete('/products/delete', [ProductsController::class, 'destroy']);

    Route::get('repairs', [DashboardController::class, 'listrepairs']);
    Route::get('repairs/other-repairs', [DashboardController::class, 'listrepairs']);
    Route::get('/repairs/edit/{id}', [RepairsController::class, 'edit']);
    Route::post('/repairs/edit', [RepairsController::class, 'update']);
    Route::delete('/repairs/delete', [RepairsController::class, 'destroy']);

    Route::get('repair-commissions/list/{id}', [UserDataController::class, 'listRepairCommision']);
    Route::get('repair-commissions/list', [UserDataController::class, 'listRepairCommisions']);
    Route::post('repair-commissions/update', [UserDataController::class, 'updateRepairCommisions']);

    Route::get('quotations', [DashboardController::class, 'listQuotations']);
    Route::get('quotations/create', [QuotationsController::class, 'create']);
    Route::post('quotations/create', [QuotationsController::class, 'store']);
    Route::get('/quotations/edit/{id}', [QuotationsController::class, 'edit']);
    Route::post('/quotations/edit', [QuotationsController::class, 'update']);
    Route::delete('/quotations/delete', [QuotationsController::class, 'destroy']);

    Route::get('orders', [DashboardController::class, 'listOrders']);
    Route::get('/order/edit/{id}', [OrdersController::class, 'edit']);
    Route::post('/order/edit', [OrdersController::class, 'update']);
    Route::post('/order/return', [OrdersController::class, 'return']);
    Route::delete('/order/delete', [OrdersController::class, 'destroy']);

    Route::get('/china-order-list', [ChinaOrdersController::class, 'index']);
    Route::post('/china-order-get', [ChinaOrdersController::class, 'get']);
    Route::get('/china-order-add', [ChinaOrdersController::class, 'create']);
    Route::post('/china-order-add', [ChinaOrdersController::class, 'store']);
    Route::get('/china-order-update/{id}', [ChinaOrdersController::class, 'edit']);
    Route::post('/china-order-update', [ChinaOrdersController::class, 'update']);
    Route::post('/china-order-bulkedit', [ChinaOrdersController::class, 'bulkEdit']);

    Route::get('bills', [DashboardController::class, 'listBills']);
    Route::get('/bill/edit/{id}', [RepairsController::class, 'salesEdit']);
    Route::post('/bill/edit', [RepairsController::class, 'salesUpdate']);
    Route::delete('/bill/delete', [RepairsController::class, 'salesDestroy']);

    Route::get('sales-report', [DashboardController::class, 'salesReport']);
    Route::get('spare-report', [DashboardController::class, 'spareReport']);
    Route::post('/spares/get-report', [SpareSaleHistoryController::class, 'getReport']);
    Route::get('sales-report/customer', [DashboardController::class, 'customerSalesReport']);
    Route::post('/sales/get-products', [DashboardController::class, 'getSalesProducts']);
    Route::post('/sales/get-invoice', [DashboardController::class, 'getSalesInvoice']);
    Route::post('/sales/get-customer-invoice', [DashboardController::class, 'getCustomerInvoice']);
    Route::get('sales-report/re-service', [RepairsController::class, 'reServiceListView']);
    Route::get('repair-history/{id}', [RepairsController::class, 'viewHistory']);
    Route::get('/stock-report', [ProductsController::class, 'stockReport']);

    Route::get('purchases', [DashboardController::class, 'listPurchses']);
    Route::get('purchase/create', [DashboardController::class, 'createPurchse']);
    Route::post('purchase/create', [PurchasesController::class, 'store']);
    Route::get('purchase/edit/{id}', [PurchasesController::class, 'edit']);
    Route::post('/purchase/edit', [PurchasesController::class, 'update']);

    Route::get('product-purchases', [DashboardController::class, 'listProductPurchses']);
    Route::get('product-purchase/create', [DashboardController::class, 'createProductPurchse']);
    Route::post('product-purchase/create', [ProductPurchasesController::class, 'store']);
    Route::get('product-purchase/report/{id}', [ProductPurchasesController::class, 'report']);
    Route::get('product-purchase/edit/{id}', [ProductPurchasesController::class, 'edit']);
    Route::post('/product-purchase/edit', [ProductPurchasesController::class, 'update']);
    Route::post('/product-purchase/update-stock', [ProductPurchasesController::class, 'updateStock']);
    Route::post('/product-purchase/pay', [ProductPurchasesController::class, 'pay']);
    Route::delete('/product-purchase/delete', [ProductPurchasesController::class, 'destroy']);

    Route::get('/petty-cash/{id}', [PurchasesController::class, 'pettyCashes']);
    Route::post('/petty-cash/reload', [PurchasesController::class, 'addPattyCash']);
    Route::post('/petty-cash/transfer', [PurchasesController::class, 'transferPattyCash']);
    Route::get('/petty-cash/{id}/list', [PurchasesController::class, 'listPettyCash']);
    Route::post('/department-credit/pay', [PurchasesController::class, 'payDepartmentCredit']);

    // Route::get('returns', [DashboardController::class, 'listPurchses']);
    // Route::get('returns/create', [DashboardController::class, 'createPurchse']);
    // Route::post('returns/create', [PurchasesController::class, 'store']);
    // Route::get('returns/edit/{id}', [PurchasesController::class, 'edit']);
    // Route::post('/returns/edit', [PurchasesController::class, 'update']);

    Route::get('customers', [DashboardController::class, 'listCustomers']);
    Route::get('customer/create', [DashboardController::class, 'createCustomer']);
    Route::post('customer/create', [CustomersController::class, 'store']);
    Route::get('customer/edit/{id}', [CustomersController::class, 'edit']);
    Route::post('/customer/edit', [CustomersController::class, 'update']);
    Route::delete('/customers/delete', [CustomersController::class, 'destroy']);

    Route::get('partners', [DashboardController::class, 'listPartners']);
    Route::get('partner/create', [DashboardController::class, 'createPartner']);
    Route::post('partner/create', [PartnersController::class, 'store']);
    Route::get('partner/edit/{id}', [PartnersController::class, 'edit']);
    Route::post('/partner/edit', [PartnersController::class, 'update']);
    Route::delete('/partners/delete', [PartnersController::class, 'destroy']);

    Route::get('users', [DashboardController::class, 'listUsers']);
    Route::get('users/create', [DashboardController::class, 'createUsers']);
    Route::post('user/create', [UserDataController::class, 'save']);
    Route::post('user/invite', [UserDataController::class, 'invite']);
    Route::get('user/edit/{id}', [UserDataController::class, 'edit']);
    Route::post('/user/edit', [UserDataController::class, 'update']);
    Route::delete('/user/delete', [UserDataController::class, 'destroy']);

    Route::get('suppliers', [DashboardController::class, 'listSuppliers']);
    Route::get('suppliers/create', [DashboardController::class, 'createSuppliers']);
    Route::post('supplier/create', [SupplierController::class, 'store']);
    Route::get('supplier/edit/{id}', [SupplierController::class, 'edit']);
    Route::post('/supplier/edit', [SupplierController::class, 'update']);
    Route::delete('/supplier/delete', [SupplierController::class, 'destroy']);

    Route::get('user/update', [DashboardController::class, 'updateUser']);
    Route::post('user/update', [DashboardController::class, 'updateUserDetails']);
    Route::post('company/update', [DashboardController::class, 'updateCompanyDetails']);

    Route::get('credits', [CreditController::class, 'index']);
    Route::post('/credits/get-credits', [CreditController::class, 'getCredits']);
    Route::post('/credits/pay-credit', [CreditController::class, 'payCredit']);

    Route::get('invoice-settings', [DashboardController::class, 'InvoiceSettings']);
    Route::post('invoice-settings', [POSSettingsController::class, 'update']);

    Route::post('sms/get-balance', [POSSettingsController::class, 'getSMSBalance']);

    Route::get('sms', [SMSController::class, 'index']);
    Route::post('sms/send', [SMSController::class, 'send']);
});


Route::get('/partner-portal', [PartnersController::class, 'index'])->name('partnerDashboard');
Route::prefix('partner-portal')->group(function () {
    Route::get('login', [PartnersController::class, 'showLogin'])->name('partnerLogin');
    Route::post('login', [PartnersController::class, 'login'])->name('partnerSignin');
    Route::get('/repairs', [PartnersController::class, 'listRepairs']);
    Route::get('/repair/{id}', [PartnersController::class, 'displayRepair']);
});


Route::get('/customer-portal', function () {
    return view('customer-portal.customer-portal');
});
Route::prefix('customer-portal')->group(function () {
    Route::post('send-code', [CustomersController::class, 'sendCode']);
    Route::post('verify-code', [CustomersController::class, 'OTPVerify']);
});


Route::get('/signin', function() {
    //if (isset($_GET['ref']) && $_GET['ref'] == "get_started") {
    //    login_redirect('/create-account');
    //}
    return view('auth.login');
});

//Route::get('/signup', function() {
//   return view('auth.register');
//});

//Route::post('/signup', [RegisterController::class, 'register'])->name('signup');

//Route::post('/create-account', [UserDataController::class, 'store']);

Route::post('/signin', [LoginController::class, 'userLogin'])->name('userLogin');
