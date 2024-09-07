<?php

use App\Http\Controllers\accountController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\paymentController;
use App\Http\Controllers\paypalController;
use App\Http\Controllers\PosDataController;
use App\Http\Controllers\PosInvitationController;
use App\Http\Controllers\POSSettingsController;
use App\Http\Controllers\pricePlan;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\RepairsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserDataController;
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

Route::get('/', function () {
    //login_redirect('/');
    return view('auth.login');
});

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
Route::get('/customer-copy/{pos_id}/{id}', function ($pos_id, $id){
    $order = getOrder($id, $pos_id);
    if ($order[0] == true) {
        return view('customer-copy')->with(['order'=>$order[1], 'products'=>$order[2]]);
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

Route::post('checkout', [paymentController::class, 'checkout']);




//============= POS Routes ==========////

Route::get('/cloud-pos/{id}', [PosDataController::class, 'index']);
Route::get('/pos', [PosDataController::class, 'show']);
Route::post('/pos/get_repairs', [RepairsController::class, 'getRepairs']);
Route::post('/pos/update', [RepairsController::class, 'orderUpdate']);
Route::post('/pos/new_order', [RepairsController::class, 'store']);
Route::post('/pos/get_customers', [CustomersController::class, 'getCustomers']);
Route::post('/pos/pos_data', [PosDataController::class, 'getPosData']);
Route::post('/pos/get_spares', [ProductsController::class, 'getSpares']);
//Route::post('/pos/get_favourits', [ProductsController::class, 'getFavourits']);
//Route::post('/pos/add-favourits', [ProductsController::class, 'addFavourits']);
//Route::post('/pos/remove-favourits', [ProductsController::class, 'removeFavourits']);
Route::post('/pos/checkout', [PosDataController::class, 'checkout']);
//Route::post('/pos/save', [PosDataController::class, 'save']);
//Route::post('/pos/get_saved_orders', [PosDataController::class, 'getSavedOrders']);


//============= Dashboard Routes ==========////


Route::get('/pos-dashboard/{id}', [DashboardController::class, 'index']);
Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::prefix('dashboard')->group(function () {
    Route::get('products', [DashboardController::class, 'listProducts']);
    Route::get('products/create', [DashboardController::class, 'createProduct']);
    Route::get('products/edit/{id}', [ProductsController::class, 'edit']);
    Route::post('products/create', [ProductsController::class, 'store']);
    Route::post('/products/edit', [ProductsController::class, 'update']);
    Route::delete('/products/delete', [ProductsController::class, 'destroy']);

    Route::get('repairs', [DashboardController::class, 'listrepairs']);
    Route::get('/repairs/edit/{id}', [RepairsController::class, 'edit']);
    Route::post('/repairs/edit', [RepairsController::class, 'update']);
    Route::delete('/repairs/delete', [RepairsController::class, 'destroy']);

    Route::get('sales-report', [DashboardController::class, 'salesReport']);
    Route::get('sales-report/customer', [DashboardController::class, 'customerSalesReport']);
    Route::post('/sales/get-products', [DashboardController::class, 'getSalesProducts']);
    Route::post('/sales/get-invoice', [DashboardController::class, 'getSalesInvoice']);
    Route::post('/sales/get-customer-invoice', [DashboardController::class, 'getCustomerInvoice']);

    Route::get('purchases', [DashboardController::class, 'listPurchses']);
    Route::get('purchase/create', [DashboardController::class, 'createPurchse']);
    Route::post('purchase/create', [PurchasesController::class, 'store']);
    Route::get('purchase/edit/{id}', [PurchasesController::class, 'edit']);
    Route::post('/purchase/edit', [PurchasesController::class, 'update']);

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