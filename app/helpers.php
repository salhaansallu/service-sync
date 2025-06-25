<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\PosDataController;
use App\Http\Controllers\POSSettingsController;
use App\Http\Controllers\UserDataController;
use App\Models\Categories;
use App\Models\Credit;
use App\Models\CreditHistory;
use App\Models\customers;
use App\Models\orderProducts;
use App\Models\orders;
use App\Models\partners;
use App\Models\posData;
use App\Models\PosInvitation;
use App\Models\posUsers;
use App\Models\ProductPurchases;
use App\Models\Products;
use App\Models\purchaseTransactions;
use App\Models\quotations;
use App\Models\Repairs;
use App\Models\shippers;
use App\Models\supplier;
use App\Models\User;
use App\Models\userData;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class SMS
{
    public $contact;
    public $message;
    public $type;
    public $camp_type;
    public $send_at;

    function Send()
    {
        // URL to send the POST request to
        $url = env('SMS_GATEWAY_URL');

        // Data to be sent in the POST request
        $postFields = [
            'user_id' => env('SMS_USER_ID'),
            'api_key' => env('SMS_API_KEY'),
            'sender_id' => env('SMS_SENDER_ID'),
            'contact' => json_encode($this->contact),
            'message' => $this->message,
            'type' => $this->type,
            'camp_type' => $this->camp_type,
            'send_at' => $this->send_at,
        ];

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_POST, true);           // Set the request method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields)); // Set the POST fields

        // Execute the cURL session and fetch the response
        $response = curl_exec($ch);

        // Close the cURL session
        curl_close($ch);

        // Check for errors
        if (curl_errno($ch)) {
            return (object)array(
                "error" => 1,
                "msg" => 'cURL error: ' . curl_error($ch)
            );
        } else {
            // Print the response
            $response = json_decode($response);
            return (object)array(
                "error" => $response->error,
                "msg" => $response->message
            );
        }
    }

    static function getBalance()
    {
        // URL to send the POST request to
        $url = env('SMS_GATEWAY_URL');

        // Data to be sent in the POST request
        $postFields = [
            'user_id' => env('SMS_USER_ID'),
            'api_key' => env('SMS_API_KEY'),
        ];

        // Initialize cURL session
        $ch = curl_init($url . 'get-balance');

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_POST, true);           // Set the request method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields)); // Set the POST fields

        // Execute the cURL session and fetch the response
        $response = curl_exec($ch);


        // Check for errors
        if (curl_errno($ch)) {
            $response = (object)array(
                "error" => 1,
                "msg" => 'cURL error: ' . curl_error($ch)
            );
        } else {
            // Print the response
            $response = json_decode($response);
            $response = (object)array(
                "error" => $response->error,
                "msg" => $response->message,
                "balance" => $response->error == 0 ? $response->balance : 0
            );
        }

        // Close the cURL session
        curl_close($ch);
        return $response;
    }
}

function isAdmin()
{
    return Auth::check() && DashboardController::check();
}

function isCashier()
{
    return Auth::check() && PosDataController::check();
}

function getAllProducts()
{
    return Products::all();
}

function country($country)
{
    $countryList = array(
        "Afghanistan",
        "Albania",
        "Algeria",
        "American Samoa",
        "Andorra",
        "Angola",
        "Anguilla",
        "Antarctica",
        "Antigua and Barbuda",
        "Argentina",
        "Armenia",
        "Aruba",
        "Australia",
        "Austria",
        "Azerbaijan",
        "Bahamas (the)",
        "Bahrain",
        "Bangladesh",
        "Barbados",
        "Belarus",
        "Belgium",
        "Belize",
        "Benin",
        "Bermuda",
        "Bhutan",
        "Bolivia (Plurinational State of)",
        "Bonaire, Sint Eustatius and Saba",
        "Bosnia and Herzegovina",
        "Botswana",
        "Bouvet Island",
        "Brazil",
        "British Indian Ocean Territory (the)",
        "Brunei Darussalam",
        "Bulgaria",
        "Burkina Faso",
        "Burundi",
        "Cabo Verde",
        "Cambodia",
        "Cameroon",
        "Canada",
        "Cayman Islands (the)",
        "Central African Republic (the)",
        "Chad",
        "Chile",
        "China",
        "Christmas Island",
        "Cocos (Keeling) Islands (the)",
        "Colombia",
        "Comoros (the)",
        "Congo (the Democratic Republic of the)",
        "Congo (the)",
        "Cook Islands (the)",
        "Costa Rica",
        "Croatia",
        "Cuba",
        "Curaçao",
        "Cyprus",
        "Czechia",
        "Côte d'Ivoire",
        "Denmark",
        "Djibouti",
        "Dominica",
        "Dominican Republic (the)",
        "Ecuador",
        "Egypt",
        "El Salvador",
        "Equatorial Guinea",
        "Eritrea",
        "Estonia",
        "Eswatini",
        "Ethiopia",
        "Falkland Islands (the) [Malvinas]",
        "Faroe Islands (the)",
        "Fiji",
        "Finland",
        "France",
        "French Guiana",
        "French Polynesia",
        "French Southern Territories (the)",
        "Gabon",
        "Gambia (the)",
        "Georgia",
        "Germany",
        "Ghana",
        "Gibraltar",
        "Greece",
        "Greenland",
        "Grenada",
        "Guadeloupe",
        "Guam",
        "Guatemala",
        "Guernsey",
        "Guinea",
        "Guinea-Bissau",
        "Guyana",
        "Haiti",
        "Heard Island and McDonald Islands",
        "Holy See (the)",
        "Honduras",
        "Hong Kong",
        "Hungary",
        "Iceland",
        "India",
        "Indonesia",
        "Iran (Islamic Republic of)",
        "Iraq",
        "Ireland",
        "Isle of Man",
        "Israel",
        "Italy",
        "Jamaica",
        "Japan",
        "Jersey",
        "Jordan",
        "Kazakhstan",
        "Kenya",
        "Kiribati",
        "Korea (the Democratic People's Republic of)",
        "Korea (the Republic of)",
        "Kuwait",
        "Kyrgyzstan",
        "Lao People's Democratic Republic (the)",
        "Latvia",
        "Lebanon",
        "Lesotho",
        "Liberia",
        "Libya",
        "Liechtenstein",
        "Lithuania",
        "Luxembourg",
        "Macao",
        "Madagascar",
        "Malawi",
        "Malaysia",
        "Maldives",
        "Mali",
        "Malta",
        "Marshall Islands (the)",
        "Martinique",
        "Mauritania",
        "Mauritius",
        "Mayotte",
        "Mexico",
        "Micronesia (Federated States of)",
        "Moldova (the Republic of)",
        "Monaco",
        "Mongolia",
        "Montenegro",
        "Montserrat",
        "Morocco",
        "Mozambique",
        "Myanmar",
        "Namibia",
        "Nauru",
        "Nepal",
        "Netherlands (the)",
        "New Caledonia",
        "New Zealand",
        "Nicaragua",
        "Niger (the)",
        "Nigeria",
        "Niue",
        "Norfolk Island",
        "Northern Mariana Islands (the)",
        "Norway",
        "Oman",
        "Pakistan",
        "Palau",
        "Palestine, State of",
        "Panama",
        "Papua New Guinea",
        "Paraguay",
        "Peru",
        "Philippines (the)",
        "Pitcairn",
        "Poland",
        "Portugal",
        "Puerto Rico",
        "Qatar",
        "Republic of North Macedonia",
        "Romania",
        "Russian Federation (the)",
        "Rwanda",
        "Réunion",
        "Saint Barthélemy",
        "Saint Helena, Ascension and Tristan da Cunha",
        "Saint Kitts and Nevis",
        "Saint Lucia",
        "Saint Martin (French part)",
        "Saint Pierre and Miquelon",
        "Saint Vincent and the Grenadines",
        "Samoa",
        "San Marino",
        "Sao Tome and Principe",
        "Saudi Arabia",
        "Senegal",
        "Serbia",
        "Seychelles",
        "Sierra Leone",
        "Singapore",
        "Sint Maarten (Dutch part)",
        "Slovakia",
        "Slovenia",
        "Solomon Islands",
        "Somalia",
        "South Africa",
        "South Georgia and the South Sandwich Islands",
        "South Sudan",
        "Spain",
        "Sri Lanka",
        "Sudan (the)",
        "Suriname",
        "Svalbard and Jan Mayen",
        "Sweden",
        "Switzerland",
        "Syrian Arab Republic",
        "Taiwan",
        "Tajikistan",
        "Tanzania, United Republic of",
        "Thailand",
        "Timor-Leste",
        "Togo",
        "Tokelau",
        "Tonga",
        "Trinidad and Tobago",
        "Tunisia",
        "Turkey",
        "Turkmenistan",
        "Turks and Caicos Islands (the)",
        "Tuvalu",
        "Uganda",
        "Ukraine",
        "United Arab Emirates (the)",
        "United Kingdom of Great Britain and Northern Ireland (the)",
        "United States Minor Outlying Islands (the)",
        "United States of America (the)",
        "Uruguay",
        "Uzbekistan",
        "Vanuatu",
        "Venezuela (Bolivarian Republic of)",
        "Viet Nam",
        "Virgin Islands (British)",
        "Virgin Islands (U.S.)",
        "Wallis and Futuna",
        "Western Sahara",
        "Yemen",
        "Zambia",
        "Zimbabwe",
        "Åland Islands"
    );

    if ($country == 'get') {
        return $countryList;
    } elseif (in_array($country, $countryList)) {
        return true;
    } else {
        return false;
    }
}

function company($pos_code = null)
{
    if ($pos_code != null) {
        $pos = posData::where('pos_code', $pos_code)->first();
        if ($pos && $pos->count() > 0) {
            return $pos;
        }
        return defaultValues();
    }
    return PosDataController::company();
}

function printInvoice($invoice)
{
    // Check if the invoice is empty
    if (empty($invoice)) {
        // Error handling (in PHP, you'd typically handle this with a redirect, log, or display message)
        return "Invoice not found";
    }

    // If the invoice contains the word 'Invoice', replace it with 'Thermal-invoice'
    if (strpos($invoice, 'Invoice') !== false) {
        $invoiceUrl = str_replace("Invoice", "Thermal-invoice", $invoice);
        // Trigger the printing process (you can redirect or echo the URL based on your app flow)
        return $invoiceUrl;
    }

    // If the invoice contains the word 'Delivery', replace it with 'Thermal-delivery'
    if (strpos($invoice, 'Delivery') !== false) {
        $invoiceUrl = str_replace("Delivery", "Thermal-delivery", $invoice);
        // Trigger the printing process
        return $invoiceUrl;
    }

    // Default case, just print the original invoice
    return $invoice;
}

function getShipperOutstanding($id)
{
    $total = 0;
    $outstandings = ProductPurchases::where('shipper_id', $id)->get();
    foreach ($outstandings as $key => $outstanding) {
        $transaction = purchaseTransactions::where('purchase_id', $outstanding->id)->where('note', 'shipper-payment')->sum('amount');
        $total += $outstanding->cbm_price - $transaction;
    }

    return $total;
}

function getSupplierOutstanding($id)
{
    $outstanding = ProductPurchases::where('supplier_id', $id)->sum('pending');
    return $outstanding;
}

function POSSettings($pos_code = null)
{
    if ($pos_code == null) {
        $pos_code = company()->pos_code;
    }
    return POSSettingsController::settings($pos_code);
}

function userData()
{
    return UserDataController::user();
}

function display404()
{
    return response()->view('errors.404')->setStatusCode(404);
}

function HashCode($val)
{
    return Crypt::encrypt($val);
}

function productImage($path)
{
    if (empty($path)) {
        return asset('assets/images/products/placeholder.svg');
    }

    if (public_path('assets/images/products/' . $path)) {
        return asset('/assets/images/products/' . $path);
    } else {
        return asset('assets/images/products/placeholder.svg');
    }
}

function profileImage($path)
{
    if (empty($path)) {
        return asset('user_profile/placeholder.png');
    }

    if (file_exists(public_path('user_profile/' . $path))) {
        return asset('user_profile/' . $path);
    } else {
        return asset('user_profile/placeholder.png');
    }
}

function categoryImage($path)
{
    if (empty($path)) {
        return asset('assets/images/categories/placeholder.svg');
    }

    if (public_path('assets/images/categories/' . $path)) {
        return asset('/assets/images/categories/' . $path);
    } else {
        return asset('assets/images/categories/placeholder.svg');
    }
}

function defaultValues()
{
    $arr = array(
        "id" => "",
        "name" => "",
        "category_name" => "",
        "pos_code" => "",
        "image" => "",
        "fname" => "",
        "lname" => "",
        "email" => "",
        "phone" => "",
        "company_name" => "",
        "industry" => "",
        "country" => "",
        "city" => "",
        "message" => "",
        "calls" => "",
        "user_id" => "",
        "created_at" => "",
        "updated_at" => "",
        "customer_id" => "",
        "ammount" => "",
        "credit_id" => "",
        "address" => "",
        "order_number" => "",
        "customer" => "",
        "total" => "",
        "total_cost" => "",
        "service_charge" => "",
        "roundup" => "",
        "payment_method" => "",
        "invoice" => "",
        "order_id" => "",
        "qr_code" => "",
        "discount" => "",
        "discount_mod" => "",
        "discounted_price" => "",
        "admin_id" => "",
        "company_name" => "",
        "industry" => "",
        "plan" => "",
        "status" => "",
        "expiry_date" => "",
        "currency" => "",
        "sku" => "",
        "pro_name" => "",
        "price" => "",
        "cost" => "",
        "qty" => "",
        "category" => "",
        "pro_image" => "",
        "supplier" => "",
        "purshace_no" => "",
        "shipping_charge" => "",
        "note" => "",
        "supplier_id" => "",
        "products" => "",
        "order_name" => "",
        "password" => "",
        "zip" => "",
        "profile" => "",
        "status" => "",
        "invitation_id" => "",
        "date" => "",
        "time" => "",
        "datetime" => "",
        "cashier" => "",
        "cashier_code" => "",
        "title" => "",
    );
    return (object)$arr;
}

function getProductImage($sku)
{
    $product = Products::where('sku', $sku)->get();
    if ($product && $product->count() > 0) {
        return productImage($product[0]->pro_image);
    }
    return productImage('');
}

function getCategory($id)
{
    if ($id == 'all') {
        $category = Categories::where('pos_code', company()->pos_code)->get();
        if ($category) {
            return (object)$category;
        }
    } else {
        $category = Categories::where('id', $id)->where('pos_code', company()->pos_code)->get();
        if ($category && $category->count() > 0) {
            return (object)$category[0];
        }
    }
    return defaultValues();
}

function getSupplier($id)
{
    if ($id == 'all') {
        $supplier = supplier::all();
        if ($supplier) {
            return (object)$supplier;
        }
    } else {
        $supplier = supplier::where('id', $id)->get();
        if ($supplier && $supplier->count() > 0) {
            return (object)$supplier[0];
        }
    }
    return defaultValues();
}

function getShippers($id)
{
    if ($id == 'all') {
        return shippers::all();
    } else {
        $supplier = shippers::where('id', $id)->get();
        if ($supplier && $supplier->count() > 0) {
            return (object)$supplier[0];
        }
    }
    return defaultValues();
}

function getPartner($id)
{
    if ($id == 'all') {
        $partners = partners::all();
        if ($partners->count() > 0) {
            return (object)$partners;
        }
    } else {
        $partners = partners::where('id', $id)->get();
        if ($partners && $partners->count() > 0) {
            return (object)$partners[0];
        }
    }
    return defaultValues();
}

function getDepartments()
{
    return array(
        array(
            "name" => "TV Repairing",
            "slug" => "tv-repairs",
        ),
        array(
            "name" => "Other Repairing",
            "slug" => "other-repairs",
        ),
    );
}

function getDepartment($id)
{

    foreach (getDepartments() as $key => $value) {
        if ($value["slug"] == $id) {
            return $value["name"];
        }
    }
    return "";
}

function verifyDepartment($id)
{
    foreach (getDepartments() as $key => $department) {
        if ($id == $department["slug"]) {
            return true;
        }
    }

    return false;
}

function getTotalOrderQTY($sku)
{
    $product = orderProducts::where('sku', $sku)->sum('qty');
    return $product;
}

function getTotalOrderSale($sku)
{
    $total = 0;
    $products = orderProducts::where('sku', $sku)->get();
    if ($products && $products->count() > 0) {
        foreach ($products as $key => $item) {
            if ($item['discount'] > 0) {
                (float)$total += (float)$item['discounted_price'] * (float)$item['qty'];
            } else {
                (float)$total += (float)$item['price'] * (float)$item['qty'];
            }
        }
    }
    return (float)$total;
}

function getDeliveryStatus($bill)
{
    $orders = orders::where('bill_no', $bill)->where('pos_code', company()->pos_code)->get();
    if ($orders->count() > 0) {
        return $orders[0]->status;
    }
    return "N/A";
}

function getQuotationURL($q_no, $pos_code)
{
    return asset('quotations/' . str_replace([' ', '.', "'", '"'], ['', '', "", ''], $q_no) . '-' . $pos_code . '.pdf');
}

function partner()
{
    return PartnersController::getPartnerDetails();
}

function hasDelivery($bill)
{
    $orders = orders::where('bill_no', $bill)->where('pos_code', company()->pos_code)->get();
    if ($orders->count() > 0) {
        return true;
    }
    return false;
}

function currency($price, $currency = '')
{
    if (strpos($price, '.')) {
        return $currency . " " . round((float)$price, 2);
    } elseif (empty($price) || $price == "0") {
        return "0.00";
    } else {
        return $currency . " " . $price . '.00';
    }
}

function sanitize($data)
{
    return strip_tags($data);
}

function login_redirect($path)
{
    if ($path == "get_path") {
        return !get_Cookie('login_redirect') ? '/account/overview' : get_Cookie('login_redirect');
    }
    return set_Cookie('login_redirect', $path);
}

function set_Cookie($name, $value)
{
    Cookie::queue($name, $value, 60 * 24);
}

function get_Cookie($name)
{
    if (Cookie::has($name)) {
        return Cookie::get($name);
    } else {
        return false;
    }
}

function CodeToUrl($value)
{
    return str_replace("/", "slash", $value);
}

function UrlToCode($value)
{
    return str_replace("slash", "/", $value);
}

function formatPhoneNumber($phoneNumber)
{
    // Remove non-numeric characters
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

    // Check if the phone number is 10 digits
    if (strlen($phoneNumber) == 10) {
        // Format the phone number as (XXX) XXX-XXXX
        return sprintf("(%s) %s-%s", substr($phoneNumber, 0, 3), substr($phoneNumber, 3, 3), substr($phoneNumber, 6));
    } else {
        // Return the original phone number if it doesn't have 10 digits
        return $phoneNumber;
    }
}

function formatOriginalPhoneNumber($phone)
{
    // Remove any leading "+" or "94" country code
    $phone = preg_replace('/^(?:\+94|94)/', '0', $phone);

    // Ensure the phone number has exactly 10 digits starting with "0"
    if (preg_match('/^0\d{9}$/', $phone)) {
        return $phone;
    }
    return null; // Return null if the number doesn't match the expected format
}

function getUser($id)
{
    $user = User::where('id', $id)->get();
    if ($user && $user->count() > 0) {
        return (object)$user[0];
    }
    return defaultValues();
}

function getUsers()
{
    return User::all();
}

function getCashierCode($id, $pos_code = null)
{
    if ($pos_code == null) {
        $pos_code = company()->pos_code;
    }
    $user = posUsers::where('user_id', $id)->where('pos_code', $pos_code)->get();
    if ($user && $user->count() > 0) {
        return (object)$user[0];
    }
    return defaultValues();
}

function getUserData($id)
{
    $user = userData::where('user_id', $id)->get();
    if ($user && $user->count() > 0) {
        return (object)$user[0];
    }
    return defaultValues();
}

function getCustomer($id)
{
    $user = customers::where('id', $id)->get();
    if ($user && $user->count() > 0) {
        return (object)$user[0];
    }
    return defaultValues();
}

function generateQR($data, $invoice = false)
{
    $qr = new QrCode();
    $qrCodes = $qr::size(100)->style('square')->generate($data);
    if ($invoice) {
        return base64_encode($qrCodes);
    }

    $filename = rand(1111, 999999) . date('d-m-Y-h-i-s') . '.svg';

    if (file_put_contents(public_path('qr_codes/' . $filename), $qrCodes)) {
        return $filename;
    } else {
        return 0;
    }
}

function generateInvoice($order_id, $inName, $bill_type)
{
    $company = PosDataController::company();

    $total = 0;
    $advance = 0;
    $delivery = 0;
    $orders = [];
    $customer = [];
    $repairs = [];

    if (is_array($order_id)) {
        foreach ($order_id as $key => $id) {
            $temp_order = Repairs::where('bill_no', $id)->get();
            if ($temp_order->count() > 0) {
                $temp_order = $temp_order[0];
                $delivery = $temp_order->delivery;
                $total += $temp_order->total;
                $advance += $temp_order->advance;
                $orders[] = array("id" => $id, "total" => $temp_order->total, "advance" => $temp_order->advance, "model" => $temp_order->model_no, "serial" => $temp_order->serial_no, 'warranty' => $temp_order->warranty);
            }
        }

        $repairs = Repairs::where('bill_no', $order_id[0])->get()[0];
        $customer = getCustomer($repairs->customer);
    } else {
        $temp_order = Repairs::where('bill_no', $order_id)->get();
        if ($temp_order->count() > 0) {
            $temp_order = $temp_order[0];
            $total += $temp_order->total;
            $delivery = $temp_order->delivery;
            $advance += $temp_order->advance;
            $orders[] = array("id" => $order_id, "total" => $temp_order->total, "advance" => $temp_order->advance, "model" => $temp_order->model_no, "serial" => $temp_order->serial_no, 'warranty' => $temp_order->warranty);
        }

        $repairs = Repairs::where('bill_no', $order_id)->where('pos_code', $company->pos_code)->get()[0];
        $customer = getCustomer($repairs->customer);
    }

    $note = $bill_type == 'newOrder' ? 'Received' : 'Delivered';
    $note2 = $bill_type == 'newOrder' ? 'Receive Note' : 'Delivery Note';

    $html = '
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
            <style>
                @page {
                    margin: 10px;
                    height: auto;
                    width: 210mm;
                 }
                body { margin: 10px; }
            </style>
        </head>
        <body style="font-family: Arial, sans-serif;">

            <!-- Header -->
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="margin: 0;">' . $company->company_name . '</h1>
                <p style="margin: 0;">' . getUserData($company->admin_id)->address . '</p>
                <p style="margin: 0;">Tel: ' . formatPhoneNumber(getUserData($company->admin_id)->phone) . '</p>
                <p style="margin: 0;">www.wefix.lk</p>
                <h2 style="margin: 20px 0;">' . $note2 . '</h2>
            </div>

            <!-- Customer Details -->
            <div style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th colspan="3" style="color: #000;padding: 5px;">Customer Details</th>
                    </tr>
                    <tr>
                        <td style="width: 33%; padding: 5px; border: 1px solid black;">Customer Name</td>
                        <td style="width: 33%; padding: 5px; border: 1px solid black;">Customer Mobile</td>
                        <td style="width: 33%; padding: 5px; border: 1px solid black;">Customer Address</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; border: 1px solid black;">' . $customer->name . '</td>
                        <td style="padding: 5px; border: 1px solid black;">' . $customer->phone . '</td>
                        <td style="padding: 5px; border: 1px solid black;">' . $customer->address . '</td>
                    </tr>
                </table>
            </div>

            <!-- Bill Info -->
            <div style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th style="width: 50%; padding: 8px; text-align: left;">Date</th>
                    </tr>
                    <tr>
                        <td style="padding: 8px;">' . date('d-m-Y H:i:s', strtotime($repairs->created_at)) . '</td>
                    </tr>
                    <tr>
                        <th style="width: 50%; padding: 8px; text-align: left;">Delivery Date:</th>
                    </tr>
                    <tr>
                        <td style="padding: 8px;' . ($repairs->status == 'Delivered' ? '' : 'display:none;') . '">' . date('d-m-Y H:i:s', strtotime($repairs->paid_at)) . '</td>
                    </tr>
                </table>
            </div>

            <!-- Item Details -->
            <div style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Bill No</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Total</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Advance</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Balance</th>
                    </tr>
            ';

    foreach ($orders as $key => $order) {
        $html .= '
            <tr>
                <td style="padding: 5px; border: 1px solid black;">' . $order["id"] . ' - ' . $order["model"] . ' (' . $order["serial"] . ')</td>
                <td style="padding: 5px; border: 1px solid black;">' . currency($order["total"], '') . '</td>
                <td style="padding: 5px; border: 1px solid black;">' . currency($order["advance"], '') . '</td>
                <td style="padding: 5px; border: 1px solid black;">' . currency($order["total"] - $order["advance"], '') . '</td>
            </tr>
        ';
    }

    if ($bill_type == "newOrder") {
        $html .= '
                </table>
            </div>

            <!-- Total -->
            <div>
                <table style="width: 30%; border-collapse: collapse;margin-left: auto;">
                    <tr>
                        <td style="padding: 5px; text-align: right;font-size: 20px;font-weight: 800;">Total:</td>
                        <td style="padding: 5px; text-align: right;font-size: 20px;font-weight: 800; width: 230px;">' . currency($total, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; text-align: right;">Advance:</td>
                        <td style="padding: 5px; text-align: right; width: 230px;">' . currency($advance, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; text-align: right;">Balance:</td>
                        <td style="padding: 5px; text-align: right; width: 230px;">' . currency(((float)$total - (float)$advance), 'LKR') . '</td>
                    </tr>
                </table>
            </div>
        ';
        $html .= '
            <!-- Checking Charges -->
            <div>
                <h3>Product Information</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 20px; border: 1px solid black; width: 50%; vertical-align: top;">
                            ' . nl2br(htmlspecialchars($repairs->note)) . '
                        </td>
                        <td style="padding: 5px; border: 1px solid black; width: 50%;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <th style="padding: 5px; border: 1px solid black;">Checking Charges</th>
                                    <th style="padding: 5px; border: 1px solid black;">Rs.</th>
                                </tr>
                                <tr>
                                    <td style="padding: 5px; border: 1px solid black;">24" INCH LCD LED</td>
                                    <td style="padding: 5px; border: 1px solid black;">1,000.00</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px; border: 1px solid black;">32" INCH LCD LED</td>
                                    <td style="padding: 5px; border: 1px solid black;">1,500.00</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px; border: 1px solid black;">40" TO 50" INCH LCD LED</td>
                                    <td style="padding: 5px; border: 1px solid black;">2,000.00</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px; border: 1px solid black;">55" INCH LCD LED</td>
                                    <td style="padding: 5px; border: 1px solid black;">3,000.00</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px; border: 1px solid black;">55" TO 100" INCH LCD LED</td>
                                    <td style="padding: 5px; border: 1px solid black;">5,000.00</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="border-top: 1px solid black;">
                <div style="padding: 8px; text-align: left; font-weight: bold; border-bottom: 1px solid black;">By agreeing to these terms, you acknowledge and accept these conditions.</div>
                <ol style="font-size: 12px; line-height: 1.5; margin-left: 20px; padding-bottom: 3px;">
                    <li>If you retrieve your item before the repair is completed, you must still pay the repair charges.</li>
                    <li>The company is not responsible for items not collected within 14 days after repair.</li>
                </ol>

                <!-- Signature Section -->
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 10px; text-align: center; width: 50%;">
                            <p>_____________________</p>
                            <p style="margin-top: 5px;">Customer Signature</p>
                        </td>
                        <td style="padding: 10px; text-align: center; width: 50%;">
                            <p style="margin-bottom: 10px;"><strong>' . substr(getUser(Auth::user()->id)->fname, 0, 11) . '</strong></p>
                            <p style="margin-top: 5px;">' . $note . ' By</p>
                        </td>
                    </tr>
                </table>
            </div>
        ';
    } else {
        $html .= '
                </table>
            </div>

            <!-- Total -->
            <div>
                <table style="width: 30%; border-collapse: collapse;margin-left: auto;">
                    <tr>
                        <td style="padding: 5px; text-align: right;font-size: 20px;font-weight: 800;">Subtotal:</td>
                        <td style="padding: 5px; text-align: right;font-size: 20px;font-weight: 800; width: 230px;">' . currency($total, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; text-align: right;">Advance:</td>
                        <td style="padding: 5px; text-align: right; width: 230px;">' . currency($advance, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; text-align: right;">Delivery:</td>
                        <td style="padding: 5px; text-align: right; width: 230px;">' . currency($delivery, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; text-align: right;">Total:</td>
                        <td style="padding: 5px; text-align: right; width: 230px;">' . currency(((float)$total - (float)$advance) + $delivery, 'LKR') . '</td>
                    </tr>
                </table>
            </div>


        ';

        $html .= '
            <p style="font-size: 14px; text-align: left;font-weight: bold; border-bottom: 1px solid #000;padding-bottom: 5px;">Warranty Disclaimer</p>
        ';

        if ($orders[0]["warranty"] == 0) {
            $html .= '
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 14px; text-align: left;">
                            This product has been repaired and is sold as-is with no warranty, express or implied. We assume no responsibility for any defects or issues that may arise after the purchase. All sales are final.
                        </td>
                    </tr>
                </table>
            ';
        } elseif ($orders[0]["warranty"] > 0) {
            $html .= '
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 14px; text-align: left;">
                            This product includes a warranty valid for ' . $orders[0]["warranty"] . ' months from the date of recived. For detailed terms and conditions, please contact us.
                        </td>
                    </tr>
                </table>
            ';
        }

        $html .= '
            <!-- Signature Section -->
            <div style="border-top: 1px solid black; border-bottom: 1px solid black;margin-top: 30px;">
                <table style="width: 100%; border-top: 1px solid black;">
                    <tr>
                        <td style="padding: 10px; text-align: center; border-right: 1px solid black; width: 50%;">
                            <p>_____________________</p>
                            <p style="margin-top: 5px;">Customer Signature</p>
                        </td>
                        <td style="padding: 10px; text-align: center; width: 50%;">
                            <p style="margin-bottom: 10px;"><strong>' . getUser(Auth::user()->id)->fname . '</strong></p>
                            <p style="margin-top: 5px;">Cashier</p>
                        </td>
                    </tr>
                </table>
            </div>
        ';
    }

    $html .= '

        <!-- Footer -->
        <p style="text-align: center; font-weight: bold;">Thank You Please Come Again</p>

        </body>
        </html>
    ';
    // $connector = new FilePrintConnector("/dev/usb/lp0");
    // $printer = new Printer($connector);

    $pdf = new Dompdf();
    $pdf->setPaper("A4", "portrait");
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('invoice/' . $bill_type . '/' . $inName);
    file_put_contents($path, $pdf->output());

    return (object)array('generated' => true, 'url' => '/invoice/' . $bill_type . '/' . $inName);
}

function generateThermalInvoice($order_id, $inName, $bill_type)
{
    $company = PosDataController::company();

    $total = 0;
    $advance = 0;
    $delivery = 0;
    $orders = [];
    $customer = [];
    $repairs = [];

    if (is_array($order_id)) {
        foreach ($order_id as $key => $id) {
            $temp_order = Repairs::where('bill_no', $id)->get();
            if ($temp_order->count() > 0) {
                $temp_order = $temp_order[0];
                $total += $temp_order->total;
                $delivery = $temp_order->delivery;
                $advance += $temp_order->advance;
                $orders[] = array("id" => $id, "total" => $temp_order->total, "advance" => $temp_order->advance, "model" => $temp_order->model_no, "serial" => $temp_order->serial_no, 'warranty' => $temp_order->warranty, "fault" => $temp_order->fault, 'has_multiple_fault'=>$temp_order->has_multiple_fault, 'multiple_fault'=> $temp_order->multiple_fault);
            }
        }

        $repairs = Repairs::where('bill_no', $order_id[0])->first();
        if ($repairs == null) {
            return (object)array('generated' => false, 'url' => '');
        }

        $customer = getCustomer($repairs->customer);
    } else {
        $temp_order = Repairs::where('bill_no', $order_id)->get();
        if ($temp_order->count() > 0) {
            $temp_order = $temp_order[0];
            $total += $temp_order->total;
            $delivery = $temp_order->delivery;
            $advance += $temp_order->advance;
            $orders[] = array("id" => $order_id, "total" => $temp_order->total, "advance" => $temp_order->advance, "model" => $temp_order->model_no, "serial" => $temp_order->serial_no, 'warranty' => $temp_order->warranty, "fault" => $temp_order->fault, 'has_multiple_fault'=>$temp_order->has_multiple_fault, 'multiple_fault'=> $temp_order->multiple_fault);
        }

        $repairs = Repairs::where('bill_no', $order_id)->get()[0];
        $customer = getCustomer($repairs->customer);
    }

    $qr_code_image = generateQR("https://wefixservers.xyz/invoice/" . $bill_type . "/" . str_replace(["Thermal-delivery", "Thermal-invoice"], ["Delivery", "Invoice"], $inName), true);
    //$POSSettings = POSSettings();

    $note = $bill_type == 'newOrder' ? 'Received' : 'Delivered';
    $note2 = $bill_type == 'newOrder' ? 'Receive Note' : 'Invoice';

    $company_name = '';
    $company_address = '';
    $company_phone = '';
    $hasPartnerLogo = false;
    $logo = '';

    if ($repairs->partner != 0) {
        $partner = getPartner($repairs->partner);
        $company_name = $partner->company;
        $company_address = $partner->address;
        $company_phone = formatPhoneNumber($partner->phone);

        $path = public_path('user_profile/' . $partner->logo);
        if (!empty($partner->logo) && file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $hasPartnerLogo = $partner->logo;
        }
    } else {
        $company_name = $company->company_name;
        $company_address = getUserData($company->admin_id)->address;
        $company_phone = formatPhoneNumber(getUserData($company->admin_id)->phone);
    }

    $html = '

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Receive Note</title>
            <style>
                @page {
                    margin: 10px;
                    height: auto;
                    width: 80mm;
                 }
                body { margin: 10px; }
            </style>
        </head>
        <body style="font-family: Arial, sans-serif;">

            <div style="text-align: center;">
            ' . ($hasPartnerLogo && !empty($logo) ? '<img height="70px" src="' . $logo . '">' : '') . '

                <h2 style="margin: 0; margin-top: 10px;">' . $company_name . '</h2>
                <p style="margin: 2px 0; font-size: 13px;">' . $company_address . '</p>
                <p style="margin: 2px 0; font-size: 13px;">Tel: ' . $company_phone . '</p>
                <p style="margin: 2px 0; font-size: 13px;">' . ($repairs->partner == 0 ? 'www.wefix.lk' : '') . '</p>
            </div>

            <h3 style="text-align: center; margin: 10px 0;">' . $note2 . '</h3>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <tr>
                    <td style="font-size: 12px;padding: 5px 0; font-weight: bold;" colspan="2">Customer details</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;">Name:</td>
                    <td style="font-size: 12px; text-align: right;">' . $customer->name . '</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;">Mobile:</td>
                    <td style="font-size: 12px; text-align: right;">' . $customer->phone . '</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;">Address:</td>
                    <td style="font-size: 12px; text-align: right;">' . $customer->address . '</td>
                </tr>

                <tr>
                    <td style="font-size: 12px;">Order Date:</td>
                    <td style="font-size: 12px; text-align: right;">' . $repairs->created_at . '</td>
                </tr>

                <tr>
                    <td style="font-size: 12px;">Delivery Date:</td>
                    <td style="font-size: 12px; text-align: right;">' . ($repairs->paid_at ? date('d-m-Y H:i:s', strtotime($repairs->paid_at)) : 'N/A') . '</td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; margin-top: 5px; border-top: 1px solid #000;">
                <tr style="">
                    <th style="color: #000;padding: 5px; text-align: left;">Order</th>
                    <th style="color: #000;padding: 5px; text-align: left;">Total</th>
                    <th style="color: #000;padding: 5px; text-align: left;">Advance</th>
                    <th style="color: #000;padding: 5px; text-align: left;">Balance</th>
                </tr>
            ';

    foreach ($orders as $key => $order) {
        $tempHtml = '';
        foreach (json_decode($order['multiple_fault']) as $key => $fault) {
            $tempHtml .= '
                <tr>
                    <td style="width: 50%;">'.$fault->fault.':</td>
                    <td style="width: 50%;">'.currency($fault->price, '').'</td>
                </tr>
            ';
        }

        $html .= '
            <tr style="width: 100%;">
                <td style="font-size: 14px; padding-top: 5px;" colspan="4"><span style="margin-right: 5px;">' . $key + 1 . '. </span> <span style="margin-right: 10px;">' . $order["id"] . ' - ' . $order["model"] . ' (' . $order["serial"] . ')</span></td>
            </tr>
            <tr style="width: 100%;">
                <td style="font-size: 13px; padding-top: 5px;" colspan="4"><span style="margin-right: 5px;">Fault: </span> <span style="margin-right: 10px; '.($order['has_multiple_fault']? 'd-none' : '').'">' . $order["fault"] . '</span> <div style="'.($order['has_multiple_fault']? '' : 'd-none').'">
                <table style="width: 100%;">
                    '.$tempHtml.'
                </table>
                </div></td>
            </tr>
            <tr style="width: 100%;">
                <td style="font-size: 14px; border-bottom: #8d8d8d 2px dotted;"></td>
                <td style="font-size: 14px; border-bottom: #8d8d8d 2px dotted;"><div style="margin-left: 5px;">' . currency($order["total"], '') . '</div></td>
                <td style="font-size: 14px; text-align: center;border-bottom: #8d8d8d 2px dotted;">' . currency($order["advance"], '') . '</td>
                <td style="font-size: 14px; text-align: right;border-bottom: #8d8d8d 2px dotted;">' . currency($order["total"] - $order["advance"], '') . '</td>
            </tr>
        ';
    }

    $html .= '</table>';

    if ($repairs->partner != 0) {
        $html .= '
                </table>

                <table style="width: 100%; border-collapse: collapse; border-top: 1px solid #000; margin-top: 10px;">
                    <tr>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px; font-weight: bold; text-align: right;">Subtotal: </td>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px;  text-align: right;">' . currency($total, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px; font-weight: bold; text-align: right;">Advance: </td>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px;  text-align: right;">' . currency($advance, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px; font-weight: bold; text-align: right;">Delivery: </td>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px;  text-align: right;">' . currency($delivery, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px; font-weight: bold; text-align: right;">Total: </td>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px;  text-align: right;">' . currency(((float)$total - (float)$advance) + $delivery, 'LKR') . '</td>
                    </tr>
                </table>
            ';
    }

    if ($repairs->partner == 0) {
        if ($bill_type == "newOrder" && $repairs->type != "other") {
            $html .= '
                <table style="width: 100%; border-collapse: collapse; border-top: 1px solid #000; margin-top: 10px;">
                    <tr>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px; font-weight: bold; text-align: right;">Total: </td>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px;  text-align: right;">' . currency($total, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px; font-weight: bold; text-align: right;">Advance: </td>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px;  text-align: right;">' . currency($advance, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px; font-weight: bold; text-align: right;">Balance: </td>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px;  text-align: right;">' . currency(((float)$total - (float)$advance), 'LKR') . '</td>
                    </tr>
                </table>
            ';
            $html .= '
                <h4 style="margin-bottom: 10px;">Product Information</h4>

                <p style="font-size: 12px; text-align: left;font-weight: bold; border-bottom: 1px solid #000;">Additional Info</p>

                <p style="font-size: 12px; text-align: left;">' . nl2br(htmlspecialchars($repairs->note)) . '</p>

                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #000;">
                        <th style="font-size: 12px; text-align: left;">Checking Charges</th>
                        <th style="font-size: 12px; text-align: right;">Rs.</th>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; padding-top: 10px;">24" INCH LCD LED</td>
                        <td style="font-size: 14px; text-align: right; padding-top: 10px;">1,000.00</td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px;">32" INCH LCD LED</td>
                        <td style="font-size: 14px; text-align: right;">1,500.00</td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px;">40" TO 50" INCH LCD LED</td>
                        <td style="font-size: 14px; text-align: right;">2,000.00</td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px;">55" INCH LCD LED</td>
                        <td style="font-size: 14px; text-align: right;">3,000.00</td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px;">55" TO 100" INCH LCD LED</td>
                        <td style="font-size: 14px; text-align: right;">5,000.00</td>
                    </tr>
                </table>
            ';
        } else {
            $html .= '
                </table>

                <table style="width: 100%; border-collapse: collapse; border-top: 1px solid #000; margin-top: 10px;">
                    <tr>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px; font-weight: bold; text-align: right;">Subtotal: </td>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px;  text-align: right;">' . currency($total, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px; font-weight: bold; text-align: right;">Advance: </td>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px;  text-align: right;">' . currency($advance, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px; font-weight: bold; text-align: right;">Delivery: </td>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px;  text-align: right;">' . currency($delivery, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px; font-weight: bold; text-align: right;">Total: </td>
                        <td style="width: 50%; font-size: 14px;padding-top: 10px;  text-align: right;">' . currency(((float)$total - (float)$advance) + $delivery, 'LKR') . '</td>
                    </tr>
                </table>
            ';
        }
    }

    if ($repairs->partner == 0 && $bill_type == "newOrder") {
        $html .= '
            <div style="border-top: 1px solid #000; margin-top: 10px;">
                <p style="font-size: 12px; margin: 10px 0; text-align: center;">
                    By agreeing to these terms, you acknowledge and accept these conditions:
                </p>
                <p style="font-size: 12px; margin: 10px 0; text-align: center;">
                    If you retrieve your item before the repair is completed, you must still pay the repair charges.
                </p>
                <p style="font-size: 12px; margin: 10px 0; text-align: center;">
                    The company is not responsible for items not collected within 14 days after repair.
                </p>
            </div>
        ';
    }

    if ($repairs->partner == 0) {
        $html .= '
        <p style="font-size: 12px; text-align: left;font-weight: bold; border-bottom: 1px solid #000;">PDF Invoice</p>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="font-size: 14px; text-align: left;">
                    Please scan this QR code to get your invoice PDF copy
                </td>
                <td style="font-size: 14px; text-align: right;">
                    <img src="data:image/svg+xml;base64,' . $qr_code_image . '" alt="QR Code">
                </td>
            </tr>
        </table>
        ';
    }

    if ($bill_type != "newOrder") {
        $html .= '
            <p style="font-size: 12px; text-align: left;font-weight: bold; border-bottom: 1px solid #000;">Warranty Disclaimer</p>
        ';

        if ($orders[0]["warranty"] == 0) {
            $html .= '
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 14px; text-align: left;">
                            This product has been repaired and is sold as-is with no warranty, express or implied. We assume no responsibility for any defects or issues that may arise after the purchase. All sales are final.
                        </td>
                    </tr>
                </table>
            ';
        } elseif ($orders[0]["warranty"] > 0) {
            $html .= '
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 14px; text-align: left;">
                            This product includes a warranty valid for ' . $orders[0]["warranty"] . ' months from the date of recived. For detailed terms and conditions, please contact us.
                        </td>
                    </tr>
                </table>
            ';
        }
    }

    $html .= '

            <table style="width: 100%; border-collapse: collapse;border-top: 1px solid #000; margin-top: 10px;">
                <tr>
                    <td style="text-align: center; width: 50%; font-size: 14px;padding-top: 50px;">-------------------------</td>
                    <td style="text-align: center; width: 50%; font-size: 14px;padding-top: 50px;">' . substr(getUser(Auth::user()->id)->fname, 0, 11) . '</td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 50%; font-size: 14px;">Customer Signature</td>
                    <td style="text-align: center; width: 50%; font-size: 14px;">' . $note . ' By</td>
                </tr>
            </table>

            <div style="text-align: center; margin-top: 20px;">
                <p style="font-size: 12px;">Thank You, Please Come Again!</p>
            </div>

        </body>
        </html>

    ';

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 227, 800]);
    $pdf->loadHtml($html, 'UTF-8');

    $GLOBALS['bodyHeight'] = 0;

    $pdf->setCallbacks([
        'myCallbacks' => [
            'event' => 'end_frame',
            'f' => function ($frame) {
                $node = $frame->get_node();
                if (strtolower($node->nodeName) === "body") {
                    $padding_box = $frame->get_padding_box();
                    $GLOBALS['bodyHeight'] += $padding_box['h'];
                }
            }
        ]
    ]);

    $pdf->render();
    unset($pdf);

    $docHeight = $GLOBALS['bodyHeight'] + 30;

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 230, $docHeight]);
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('invoice/' . $bill_type . '/' . $inName);
    file_put_contents($path, $pdf->output());

    return (object)array('generated' => true, 'url' => '/invoice/' . $bill_type . '/' . $inName);
}

function generateThermalSticker($order_id, $inName)
{
    $repair = Repairs::where('bill_no', $order_id)->first();
    $customer = getCustomer($repair->customer);

    if ($repair == null || $customer->name == '') {
        return (object)array('generated' => false, 'url' => '/invoice/' . $inName);
    }

    $html = '

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Repair Sticker</title>
            <style>
                @page {
                    margin: 10px;
                    height: auto;
                    width: 80mm;
                 }
                body { margin: 10px; }
            </style>
        </head>
        <body style="font-family: Arial, sans-serif;">

            <h3 style="text-align: center; margin: 10px 0;">' . $order_id . '</h3>

            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="font-size: 12px;padding: 5px 0; font-weight: bold;" colspan="2">Repair details</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;">Customer Name:</td>
                    <td style="font-size: 12px; text-align: right;">' . $customer->name . '</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;">Customer Mobile:</td>
                    <td style="font-size: 12px; text-align: right; font-size: 11px;">' . $customer->phone . '</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;">Fault:</td>
                    <td style="font-size: 12px; text-align: right;">' . $repair->fault . '</td>
                </tr>
                <tr>
                    <td style="font-size: 12px;">Date:</td>
                    <td style="font-size: 12px; text-align: right;">' . date('d m Y', strtotime($repair->created_at)) . '</td>
                </tr>
            </table>
        </body>
        </html>
    ';

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 230, 230]);
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('invoice/' . $inName);
    file_put_contents($path, $pdf->output());

    return (object)array('generated' => true, 'url' => '/invoice/' . $inName);
}

function generateSalesInvoice($order_id, $inName, $products, $cashin)
{
    $company = PosDataController::company();
    $repairs = Repairs::where('bill_no', $order_id)->where('pos_code', $company->pos_code)->get()[0];
    $customer = getCustomer($repairs->customer);
    //$qr_code_image = generateQR("https://nmsware.com/customer-copy/" . $company->pos_code . "/" . $order_id);
    //$POSSettings = POSSettings();

    $company_name = '';
    $company_address = '';
    $company_phone = '';

    if ($repairs->partner != 0) {
        $partner = getPartner($repairs->partner);
        $company_name = $partner->company;
        $company_address = $partner->address;
        $company_phone = formatPhoneNumber($partner->phone);
    } else {
        $company_name = $company->company_name;
        $company_address = getUserData($company->admin_id)->address;
        $company_phone = formatPhoneNumber(getUserData($company->admin_id)->phone);
    }

    $html = '
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
            <style>
                @page {
                    margin: 10px;
                    height: auto;
                    width: 210mm;
                 }
                body { margin: 10px; }
            </style>
        </head>
        <body style="font-family: Arial, sans-serif;">

            <!-- Header -->
            <div style="text-align: center; margin-bottom: 20px; margin-top: 30px;">
                <h1 style="margin: 0;">' . $company_name . '</h1>
                <p style="margin: 0;">' . $company_address . '</p>
                <p style="margin: 0;">Tel: ' . $company_phone . '</p>
                <p style="margin: 0;">' . ($repairs->partner == 0 ? 'www.wefix.lk' : '') . '</p>
                <h2 style="margin: 20px 0;">Sales Note</h2>
            </div>

            <!-- Customer Details -->
            <div style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th colspan="3" style="color: #000;padding: 5px;">Customer Details</th>
                    </tr>
                    <tr>
                        <td style="width: 33%; padding: 5px; border: 1px solid black;">Customer Name</td>
                        <td style="width: 33%; padding: 5px; border: 1px solid black;">Customer Mobile</td>
                        <td style="width: 33%; padding: 5px; border: 1px solid black;">Customer Address</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; border: 1px solid black;">' . $customer->name . '</td>
                        <td style="padding: 5px; border: 1px solid black;">' . $customer->phone . '</td>
                        <td style="padding: 5px; border: 1px solid black;">' . $customer->address . '</td>
                    </tr>
                </table>
            </div>

            <!-- Bill Info -->
            <div style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th style="width: 50%; padding: 8px; border: 1px solid black; text-align: left;">Bill No</th>
                        <th style="width: 50%; padding: 8px; border: 1px solid black; text-align: left;">Date</th>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid black;">' . $repairs->bill_no . '</td>
                        <td style="padding: 8px; border: 1px solid black;">' . date('d-m-Y H:i:s', strtotime($repairs->created_at)) . '</td>
                    </tr>
                </table>
            </div>

            <!-- Item Details -->
            <div style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Description</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Unit Price</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">QTY</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Total</th>
                    </tr>
        ';

    foreach ($products as $key => $pro) {
        $html .= '

            <tr>
                <td style="padding: 5px; border: 1px solid black;">' . $pro['name'] . '</td>
                <td style="padding: 5px; border: 1px solid black;">' . currency($pro['unit'], '') . '</td>
                <td style="padding: 5px; border: 1px solid black;">' . $pro['qty'] . '</td>
                <td style="padding: 5px; border: 1px solid black;">' . currency($pro['unit'] * $pro['qty'], '') . '</td>
            </tr>

            ';
    }

    $html .= '
                </table>
            </div>

            <!-- Total -->
            <div style="margin-bottom: 20px;">
                <table style="width: 30%; border-collapse: collapse;margin-left: auto;">
                    <tr>
                        <td style="padding: 5px; text-align: right;font-size: 20px;font-weight: 800;">Total:</td>
                        <td style="padding: 5px; text-align: right;font-size: 20px;font-weight: 800; width: 230px;">' . currency($repairs->total, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; text-align: right;">Cash Paid:</td>
                        <td style="padding: 5px; text-align: right; width: 230px;">' . currency($cashin, 'LKR') . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; text-align: right;">Balance:</td>
                        <td style="padding: 5px; text-align: right; width: 230px;">' . currency(((float)$repairs->total - (float)$cashin), 'LKR') . '</td>
                    </tr>
                </table>
            </div>


    ';

    $html .= '
        <!-- Signature Section -->
        <div style="border-top: 1px solid black; border-bottom: 1px solid black;">
            <table style="width: 100%; border-top: 1px solid black;">
                <tr>
                    <td style="padding: 10px; text-align: center; border-right: 1px solid black; width: 50%;">
                        <p>_____________________</p>
                        <p style="margin-top: 5px;">Customer Signature</p>
                    </td>
                    <td style="padding: 10px; text-align: center; width: 50%;">
                        <p style="margin-bottom: 10px;"><strong>' . getUser(Auth::user()->id)->fname . '</strong></p>
                        <p style="margin-top: 5px;">Cashier</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <p style="text-align: center; font-weight: bold; border: 1px solid black;margin: 0;padding: 10px;">Thank You Please Come Again</p>

        </body>
        </html>
    ';
    // $connector = new FilePrintConnector("/dev/usb/lp0");
    // $printer = new Printer($connector);

    $pdf = new Dompdf();
    $pdf->setPaper("A4", "portrait");
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('invoice/checkout/' . $inName);
    file_put_contents($path, $pdf->output());

    return (object)array('generated' => true, 'url' => '/invoice/checkout/' . $inName);
}

function generateThermalSalesInvoice($order_id, $inName, $products, $cashin)
{
    $company = PosDataController::company();
    $pros = Repairs::where('bill_no', $order_id)->where('pos_code', $company->pos_code)->get()[0];
    $products = json_decode($products);

    (float)$total = $pros->total;
    $customer = ($pros->customer == '0' || $pros->customer == 'other') ? 'Cash Deal' : getCustomer($pros->customer)->name;
    $qr_code_image = generateQR("https://wefixservers.xyz/customer-copy/sale/" . $company->pos_code . "/" . $order_id, true);
    $product_count = 0;

    $title = '<div style="text-align: center;margin-top: 10px; font-size: 20px; font-weight: bold;text-transform: uppercase;margin-bottom: 3px;">Delivery Receipt</div>
        <hr style="border-width: 3px; border-color: #505050; border-style: dotted; margin: 0 40px;">';

    $datetime = '
        <tr style="width: 100%;">
            <td style="font-size: 14px; width: 50%; text-align: left;"><div>Date: ' . date('d-m-Y', strtotime($pros->created_at)) . '</div></td>
            <td style="font-size: 14px; width: 50%; text-align: right;"><div>Time: ' . date('H:i:s', strtotime($pros->created_at)) . '</div></td>
        </tr>';

    $industry = '';


    $html = '

        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $company->company_name . ' Invoice ' . $pros->bill_no . '</title>
            <style>
                @page {
                    margin: 10px;
                    height: auto;
                    width: 80mm;
                 }
                body { margin: 10px; }
            </style>
        </head>
        <body style="font-family: sans-serif;margin: 0;padding: 0;">
        <div class="invoice_wrap" style="width: 99%;padding: 40px 0;box-sizing: border-box;">
            <div><h2 style="text-align: center; margin: 0;">' . $company->company_name . '</h2></div>
            ' . $industry . '
            <hr style="border-width: 2px; border-color: #000;">
            <div style="text-align: center; font-size: 12px; margin-top: 5px;">' . getUserData($company->admin_id)->address . '</div>
            <div style="text-align: center; font-size: 12px; margin-bottom: 5px;">' . formatPhoneNumber(getUserData($company->admin_id)->phone) . '</div>
            <hr style="border-width: 1px; border-color: #000; border-style: dashed;">

            ' . $title . '

            <div style="width: 100%;margin-top: -10px; font-size: 14px;">
                <table style="width: 100%;margin-bottom: 3px;">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody style="width: 100%;">
                        <tr style="width: 100%;">
                            <td style="font-size: 14px; width: 50%;"><div>Invoice No: ' . $pros->bill_no . '</div></td>
                            <td></td>
                        </tr>

                        <tr style="width: 100%;">
                            <td style="font-size: 14px; width: 50%; text-align:left;"><div>Cashier: ' . substr(getUser(Auth::user()->id)->fname, 0, 11) . '</div></td>
                            <td style="font-size: 14px; width: 50%; text-align:right;"><div>Customer: ' . $customer . '</div></td>
                        </tr>

                        ' . $datetime . '

                    </tbody>
                </table>
            </div>

            <hr style="border-width: 3px; border-color: #505050; border-style: dotted; margin: 0 40px; margin-top: 0px;margin-bottom: 5px;">

                <table style="width: 100%;">
                    <thead>
                    <tr>
                        <th style="font-weight: 300; text-align: left; font-size: 15px;">Description</th>
                        <th style="font-weight: 300; text-align: right; font-size: 15px;">Amount</th>
                    </tr>
                    </thead>
                    <tbody style="width: 100%;">


    ';


    foreach ($products as $key => $product) {
        $product_count++;

        $html .= '
                <tr style="width: 100%;">
                    <td style="font-size: 14px;" colspan="3"><span style="margin-right: 10px;">' . $key + 1 . '. </span> <span style="margin-right: 10px;">' . $product->sku . ' </span> <span style="font-size: 15px;">' . $product->name . '</span></td>
                </tr>
                <tr style="width: 100%;">
                    <td style="font-size: 14px; border-bottom: #8d8d8d 2px dotted;"><div style="margin-left: 5px;padding-bottom: 7px;">' . currency($product->qty, '') . ' <span style="margin-left: 5px;">@</span></div></td>
                    <td style="font-size: 14px; text-align: center;border-bottom: #8d8d8d 2px dotted;">' . $product->unit . '</td>
                    <td style="font-size: 14px; text-align: right;border-bottom: #8d8d8d 2px dotted;">' . currency($product->unit * $product->qty, '') . '</td>
                </tr>
            ';
    }

    $html .= '

            </tbody>
            </table>


            <table style="width: 100%;float: right; margin: 20px 0">
            <thead>
            <tr>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody style="width: 100%;">
                <tr style="width: 100%;">
                    <td style="width: 50%;text-align: right;font-size: 20px;font-weight: bold;"><div>Total:</div></td>
                    <td style="font-size: 20px;font-weight: bold; width: 50%; text-align: right;"><div>' . currency($total, 'LKR ') . '</div></td>
                </tr>
                <tr style="width: 100%;">
                    <td style="width: 50%;text-align: right;font-size: 20px;"><div>Cash Paid:</div></td>
                    <td style="font-size: 20px; width: 50%; text-align: right;"><div>' . currency($cashin, 'LKR ') . '</div></td>
                </tr>
                <tr style="width: 100%;">
                    <td style="width: 50%;text-align: right;font-size: 20px;"><div>Balance:</div></td>
                    <td style="font-size: 20px; width: 50%; text-align: right;"><div>' . currency(($cashin - $total), 'LKR ') . '</div></td>
                </tr>
            </tbody>
            </table>

            <p style="font-size: 12px; text-align: left;font-weight: bold; border-bottom: 1px solid #000;">PDF Invoice</p>

            <table style="width: 100%; border-collapse: collapse; border-bottom: 1px solid #000; padding-bottom: 10px;">
                <tr>
                    <td style="font-size: 14px; text-align: left; padding-bottom: 10px">
                        Please scan this QR code to get your invoice PDF copy
                    </td>
                    <td style="font-size: 14px; text-align: right; padding-bottom: 10px">
                        <img src="data:image/svg+xml;base64,' . $qr_code_image . '" alt="QR Code">
                    </td>
                </tr>
            </table>
            <div style="font-weight: bold;text-align: center; margin-top: 40px;">Thank You!</div>
            <div style="text-align: center;">Please come again</div>

        </div>
        </body>
        </html>

    ';

    // $connector = new FilePrintConnector("/dev/usb/lp0");
    // $printer = new Printer($connector);

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 227, 800]);
    $pdf->loadHtml($html, 'UTF-8');

    $GLOBALS['bodyHeight'] = 0;

    $pdf->setCallbacks([
        'myCallbacks' => [
            'event' => 'end_frame',
            'f' => function ($frame) {
                $node = $frame->get_node();
                if (strtolower($node->nodeName) === "body") {
                    $padding_box = $frame->get_padding_box();
                    $GLOBALS['bodyHeight'] += $padding_box['h'];
                }
            }
        ]
    ]);

    $pdf->render();
    unset($pdf);

    $docHeight = $GLOBALS['bodyHeight'] + 30;

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 230, $docHeight]);
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('invoice/checkout/' . $inName);
    file_put_contents($path, $pdf->output());

    return (object)array('generated' => true, 'url' => '/invoice/checkout/' . $inName);
}

function generateDeliveryInvoice($order_id, $inName)
{
    $company = PosDataController::company();
    $order = orders::where('id', $order_id)->where('pos_code', $company->pos_code)->get()[0];
    $pros = Repairs::where('bill_no', $order->bill_no)->where('pos_code', $company->pos_code)->get()[0];

    $products = json_decode(htmlspecialchars_decode($pros->products));

    (float)$total = $pros->total;
    $customer = ($pros->customer == '0' || $pros->customer == 'other') ? 'Cash Deal' : getCustomer($pros->customer)->name;
    //$qr_code_image = generateQR("https://nmsware.com/customer-copy/" . substr($company->pos_code, 0, 10) . "/" . $order_id);
    $product_count = 0;

    $title = '<div style="text-align: center;margin-top: 10px; font-size: 20px; font-weight: bold;text-transform: uppercase;margin-bottom: 3px;">Delivery Receipt</div>
        <hr style="border-width: 3px; border-color: #505050; border-style: dotted; margin: 0 40px;">';

    $datetime = '
        <tr style="width: 100%;">
            <td style="font-size: 14px; width: 50%; text-align: left;"><div>Date: ' . date('d-m-Y', strtotime($order->created_at)) . '</div></td>
            <td style="font-size: 14px; width: 50%; text-align: right;"><div>Time: ' . date('H:i:s', strtotime($order->created_at)) . '</div></td>
        </tr>';

    $industry = '';


    $html = '

        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $company->company_name . ' Invoice ' . $order->bill_no . '</title>
            <style>
                @page {
                    margin: 10px;
                    height: auto;
                    width: 80mm;
                 }
                body { margin: 10px; }
            </style>
        </head>
        <body style="font-family: sans-serif;margin: 0;padding: 0;">
        <div class="invoice_wrap" style="width: 99%;padding: 40px 0;box-sizing: border-box;">
            <div><h2 style="text-align: center; margin: 0;">' . $company->company_name . '</h2></div>
            ' . $industry . '
            <hr style="border-width: 2px; border-color: #000;">
            <div style="text-align: center; font-size: 12px; margin-top: 5px;">' . getUserData($company->admin_id)->address . '</div>
            <div style="text-align: center; font-size: 12px; margin-bottom: 5px;">' . formatPhoneNumber(getUserData($company->admin_id)->phone) . '</div>
            <hr style="border-width: 1px; border-color: #000; border-style: dashed;">

            ' . $title . '

            <div style="width: 100%;margin-top: -10px; font-size: 14px;">
                <table style="width: 100%;margin-bottom: 3px;">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody style="width: 100%;">
                        <tr style="width: 100%;">
                            <td style="font-size: 14px; width: 50%;"><div>Invoice No: ' . $order->bill_no . '</div></td>
                            <td></td>
                        </tr>

                        <tr style="width: 100%;">
                            <td style="font-size: 14px; width: 50%; text-align:left;"><div>Cashier: ' . substr(getUser(Auth::user()->id)->fname, 0, 11) . '</div></td>
                            <td style="font-size: 14px; width: 50%; text-align:right;"><div>Customer: ' . $customer . '</div></td>
                        </tr>

                        ' . $datetime . '

                    </tbody>
                </table>
            </div>

            <hr style="border-width: 3px; border-color: #505050; border-style: dotted; margin: 0 40px; margin-top: 0px;margin-bottom: 5px;">

                <table style="width: 100%;">
                    <thead>
                    <tr>
                        <th style="font-weight: 300; text-align: left; font-size: 15px;">Description</th>
                        <th style="font-weight: 300; text-align: right; font-size: 15px;">Amount</th>
                    </tr>
                    </thead>
                    <tbody style="width: 100%;">


    ';


    foreach ($products as $key => $product) {
        $product_count++;

        $html .= '
                <tr style="width: 100%;">
                    <td style="font-size: 14px;" colspan="3"><span style="margin-right: 10px;">' . $key + 1 . '. </span> <span style="margin-right: 10px;">' . $product->sku . ' </span> <span style="font-size: 15px;">' . $product->name . '</span></td>
                </tr>
                <tr style="width: 100%;">
                    <td style="font-size: 14px; border-bottom: #8d8d8d 2px dotted;"><div style="margin-left: 5px;padding-bottom: 7px;">' . currency($product->qty, '') . ' <span style="margin-left: 5px;">@</span></div></td>
                    <td style="font-size: 14px; text-align: center;border-bottom: #8d8d8d 2px dotted;">' . $product->unit . '</td>
                    <td style="font-size: 14px; text-align: right;border-bottom: #8d8d8d 2px dotted;">' . currency($product->unit * $product->qty, '') . '</td>
                </tr>
            ';
    }

    $html .= '

            </tbody>
            </table>


            <table style="width: 100%;float: right; border-bottom: 1px solid #000;padding-bottom: 7px;">
            <thead>
            <tr>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody style="width: 100%;">
                <tr style="width: 100%;">
                    <td style="width: 50%;text-align: right;font-size: 20px;font-weight: bold;"><div>Total:</div></td>
                    <td style="font-size: 20px;font-weight: bold; width: 50%; text-align: right;"><div>' . currency($total, '') . '</div></td>
                </tr>
            </tbody>
            </table>
            <div style="font-weight: bold;text-align: center; margin-top: 40px;">Thank You!</div>
            <div style="text-align: center;">Please come again</div>

        </div>
        </body>
        </html>

    ';

    // $connector = new FilePrintConnector("/dev/usb/lp0");
    // $printer = new Printer($connector);

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 227, 800]);
    $pdf->loadHtml($html, 'UTF-8');

    $GLOBALS['bodyHeight'] = 0;

    $pdf->setCallbacks([
        'myCallbacks' => [
            'event' => 'end_frame',
            'f' => function ($frame) {
                $node = $frame->get_node();
                if (strtolower($node->nodeName) === "body") {
                    $padding_box = $frame->get_padding_box();
                    $GLOBALS['bodyHeight'] += $padding_box['h'];
                }
            }
        ]
    ]);

    $pdf->render();
    unset($pdf);

    $docHeight = $GLOBALS['bodyHeight'] + 30;

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 230, $docHeight]);
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('invoice/delivery/' . $inName);
    file_put_contents($path, $pdf->output());

    return (object)array('generated' => true, 'url' => '/invoice/delivery/' . $inName);
}

function generatePendingInvoice($orders, $inName, $cashier, $name = null)
{
    $html = '
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
            <style>
                @page {
                    margin: 10px;
                    height: auto;
                    width: 210mm;
                 }
                body { margin: 10px; }
            </style>
        </head>
        <body style="font-family: Arial, sans-serif;">

            <div style="text-align: center; margin-bottom: 20px; margin-top: 30px;">
                <h1 style="margin: 0;">' . (is_array($cashier) ? ($name != null ? $name : 'Repair Report') : getUser($cashier)->fname) . '</h1>
            </div>
            <!-- Item Details -->
            <div style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Bill No</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Model</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Serial</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Fault</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Status</th>
                    </tr>
            ';

    foreach ($orders as $key => $order) {
        $html .= '
            <tr>
                <td style="padding: 5px; border: 1px solid black;">' . $order->bill_no . '</td>
                <td style="padding: 5px; border: 1px solid black;">' . $order->model_no . '</td>
                <td style="padding: 5px; border: 1px solid black;">' . $order->serial_no . '</td>
                <td style="padding: 5px; border: 1px solid black;">' . $order->fault . '</td>
                <td style="padding: 5px; border: 1px solid black;">' . $order->status . '</td>
            </tr>
        ';
    }

    $html .= '
            </table>
        </div>
        <div style="margin-top: 20px;">
            <p>Role Today: </p>
            <div style="border: 1px solid #000; width: 50%; height: 30px;"></div>
        </div>
        </body>
        </html>
    ';
    // $connector = new FilePrintConnector("/dev/usb/lp0");
    // $printer = new Printer($connector);

    $pdf = new Dompdf();
    $pdf->setPaper("A4", "portrait");
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('invoice/' . $inName);
    file_put_contents($path, $pdf->output());

    return (object)array('generated' => true, 'url' => '/invoice/' . $inName);
}

function generateCreditPay($totalDue, $paid, $customer, $datetime, $bill_name)
{

    $watermark = '<div style="text-align: center;margin: 0 25px; margin-top: 10px; font-size: 15px; border-top: #000 1px solid;border-bottom: #000 1px solid;padding: 5px 0;">POS by NMSware Technoloigies <br> +94 74 195 9701</div>';
    if (company()->plan == 3) {
        $watermark = '';
    }

    $html = '

    <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . company()->company_name . ' Credit Payment</title>
            <style>
            @page {
                margin: 10px;
                height: auto;
                width: 80mm;
            }
            body { margin: 10px; }
            </style>
        </head>

        <body style="font-family: sans-serif;margin: 0;padding: 0;">
            <div class="invoice_wrap"
            style="width: 99%;padding: 20px 0px;box-sizing: border-box;">
                <div>
                    <h2 style="text-align: center; margin: 0;">' . company()->company_name . '</h2>
                </div>
                <div style="text-align: center;margin-top: 5px; margin-bottom: 5px; font-size: 14px;">' . company()->company_name . '</div>
                <hr style="border-width: 2px; border-color: #000;">
                <div style="text-align: center; font-size: 12px; margin-top: 5px;">' . getUserData(company()->admin_id)->address . '</div>
                <div style="text-align: center; font-size: 12px; margin-bottom: 5px;">' . formatPhoneNumber(getUserData(company()->admin_id)->phone) . '</div>
                <hr style="border-width: 1px; border-color: #000; border-style: dashed;">
                <div
                    style="text-align: center;margin-top: 10px; font-size: 20px; font-weight: bold;text-transform: uppercase;margin-bottom: 3px;">
                    Credit Payment</div>
                <hr style="border-width: 3px; border-color: #505050; border-style: dotted; margin: 0 40px;">
                <div style="width: 100%;margin-top: 0px; font-size: 14px;">
                    <table style="width: 100%;margin-bottom: 3px;">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody style="width: 100%;">
                            <tr style="width: 100%;">
                                <td style="font-size: 14px; width: 50%; text-align: left;">
                                    <div>Date: ' . date('d/m/Y', strtotime($datetime)) . '</div>
                                </td>
                                <td style="font-size: 14px; width: 50%; text-align: right;">
                                    <div>Time: ' . date('H:i:s', strtotime($datetime)) . '</div>
                                </td>
                            </tr>

                            <tr style="width: 100%;">
                                <td style="font-size: 14px; width: 50%;">
                                    <div>Customer: ' . substr(getCustomer($customer)->name, 0, 11) . '</div>
                                </td>
                                <td style="font-size: 14px; width: 50%; text-align: right;">
                                    <div></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr style="border-width: 3px; border-color: #505050; border-style: dotted; margin: 0 40px; margin-top: 0px;margin-bottom: 5px;">


                <table style="width: 90%;float: right; border-bottom: #000 2px solid;">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody style="width: 100%;">

                        <tr style="width: 100%;">
                            <td style="font-size: 14px; width: 50%;text-align: right;">
                                <div>Total Due</div>
                            </td>
                            <td style="font-size: 14px; width: 50%; text-align: right;">
                                <div>' . currency($totalDue, '') . '</div>
                            </td>
                        </tr>

                        <tr style="width: 100%;">
                            <td style="width: 50%;text-align: right;font-size: 20px;font-weight: bold;">
                                <div>Cash Paid</div>
                            </td>
                            <td style="font-size: 20px;font-weight: bold; width: 50%; text-align: right;">
                                <div>' . currency($paid, '') . '</div>
                            </td>
                        </tr>

                        <tr style="width: 100%;">
                            <td style="font-size: 14px; width: 50%;text-align: right;">
                                <div>Due Balance</div>
                            </td>
                            <td style="font-size: 14px; width: 50%; text-align: right;">
                                <div>' . currency((float)$totalDue - (float)$paid, '') . '</div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div style="font-weight: bold;text-align: center;margin-top: 15px;">Thank You!</div>
                <div style="text-align: center;">Please come again</div>
                ' . $watermark . '
            </div>
        </body>

    </html>

    ';

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 227, 800]);
    $pdf->loadHtml($html, 'UTF-8');

    $GLOBALS['bodyHeight'] = 0;

    $pdf->setCallbacks([
        'myCallbacks' => [
            'event' => 'end_frame',
            'f' => function ($frame) {
                $node = $frame->get_node();
                if (strtolower($node->nodeName) === "body") {
                    $padding_box = $frame->get_padding_box();
                    $GLOBALS['bodyHeight'] += $padding_box['h'];
                }
            }
        ]
    ]);

    $pdf->render();
    unset($pdf);

    $docHeight = $GLOBALS['bodyHeight'] + 30;

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 230, $docHeight]);
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('credit-invoice/' . $bill_name);
    file_put_contents($path, $pdf->output());

    return true;
}

function generateParterCreditPay($totalDue, $paid, $partner, $datetime, $bill_name)
{

    $watermark = '<div style="text-align: center;margin: 0 25px; margin-top: 10px; font-size: 15px; border-top: #000 1px solid;border-bottom: #000 1px solid;padding: 5px 0;">POS by NMSware Technoloigies <br> +94 74 195 9701</div>';
    if (company()->plan == 3) {
        $watermark = '';
    }

    $html = '

    <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . company()->company_name . ' Credit Payment</title>
            <style>
            @page {
                margin: 10px;
                height: auto;
                width: 80mm;
            }
            body { margin: 10px; }
            </style>
        </head>

        <body style="font-family: sans-serif;margin: 0;padding: 0;">
            <div class="invoice_wrap"
            style="width: 99%;padding: 20px 0px;box-sizing: border-box;">
                <div>
                    <h2 style="text-align: center; margin: 0;">' . company()->company_name . '</h2>
                </div>
                <div style="text-align: center;margin-top: 5px; margin-bottom: 5px; font-size: 14px;">' . company()->company_name . '</div>
                <hr style="border-width: 2px; border-color: #000;">
                <div style="text-align: center; font-size: 12px; margin-top: 5px;">' . getUserData(company()->admin_id)->address . '</div>
                <div style="text-align: center; font-size: 12px; margin-bottom: 5px;">' . formatPhoneNumber(getUserData(company()->admin_id)->phone) . '</div>
                <hr style="border-width: 1px; border-color: #000; border-style: dashed;">
                <div
                    style="text-align: center;margin-top: 10px; font-size: 20px; font-weight: bold;text-transform: uppercase;margin-bottom: 3px;">
                    Credit Payment</div>
                <hr style="border-width: 3px; border-color: #505050; border-style: dotted; margin: 0 40px;">
                <div style="width: 100%;margin-top: 0px; font-size: 14px;">
                    <table style="width: 100%;margin-bottom: 3px;">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody style="width: 100%;">
                            <tr style="width: 100%;">
                                <td style="font-size: 14px; width: 50%; text-align: left;">
                                    <div>Date: ' . date('d/m/Y', strtotime($datetime)) . '</div>
                                </td>
                                <td style="font-size: 14px; width: 50%; text-align: right;">
                                    <div>Time: ' . date('H:i:s', strtotime($datetime)) . '</div>
                                </td>
                            </tr>

                            <tr style="width: 100%;">
                                <td style="font-size: 14px; width: 50%;">
                                    <div>Partner: ' . substr(getPartner($partner)->name, 0, 11) . '</div>
                                </td>
                                <td style="font-size: 14px; width: 50%; text-align: right;">
                                    <div></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr style="border-width: 3px; border-color: #505050; border-style: dotted; margin: 0 40px; margin-top: 0px;margin-bottom: 5px;">


                <table style="width: 90%;float: right; border-bottom: #000 2px solid;">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody style="width: 100%;">

                        <tr style="width: 100%;">
                            <td style="font-size: 14px; width: 50%;text-align: right;">
                                <div>Total Due</div>
                            </td>
                            <td style="font-size: 14px; width: 50%; text-align: right;">
                                <div>' . currency($totalDue, '') . '</div>
                            </td>
                        </tr>

                        <tr style="width: 100%;">
                            <td style="width: 50%;text-align: right;font-size: 20px;font-weight: bold;">
                                <div>Cash Paid</div>
                            </td>
                            <td style="font-size: 20px;font-weight: bold; width: 50%; text-align: right;">
                                <div>' . currency($paid, '') . '</div>
                            </td>
                        </tr>

                        <tr style="width: 100%;">
                            <td style="font-size: 14px; width: 50%;text-align: right;">
                                <div>Due Balance</div>
                            </td>
                            <td style="font-size: 14px; width: 50%; text-align: right;">
                                <div>' . currency((float)$totalDue - (float)$paid, '') . '</div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div style="font-weight: bold;text-align: center;margin-top: 15px;">Thank You!</div>
                <div style="text-align: center;">Please come again</div>
                ' . $watermark . '
            </div>
        </body>

    </html>

    ';

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 227, 800]);
    $pdf->loadHtml($html, 'UTF-8');

    $GLOBALS['bodyHeight'] = 0;

    $pdf->setCallbacks([
        'myCallbacks' => [
            'event' => 'end_frame',
            'f' => function ($frame) {
                $node = $frame->get_node();
                if (strtolower($node->nodeName) === "body") {
                    $padding_box = $frame->get_padding_box();
                    $GLOBALS['bodyHeight'] += $padding_box['h'];
                }
            }
        ]
    ]);

    $pdf->render();
    unset($pdf);

    $docHeight = $GLOBALS['bodyHeight'] + 30;

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 230, $docHeight]);
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('credit-invoice/' . $bill_name);
    file_put_contents($path, $pdf->output());

    return true;
}

function generateQuotation($q_no)
{
    $company = PosDataController::company();
    $quotation = quotations::where('q_no', $q_no)->where('pos_code', $company->pos_code)->first();

    if ($quotation == null) {
        exit;
    }

    $repair = null;

    if ($quotation->bill_no != "custom") {
        $repair = Repairs::where('bill_no', $quotation->bill_no)->where('pos_code', $company->pos_code)->first();
    } else {
        $repair = (object)["customer" => 0, "bill_no" => "New Order", "fault" => "N/A", "total" => 0, "advance" => 0];
    }

    if ($repair == null) {
        exit;
    }

    $customer = getCustomer($repair->customer);

    if ($quotation->bill_no == "custom") {
        $customer = (object)[
            'name' => isset(json_decode($quotation->products)[10]) ? json_decode($quotation->products)[10]->customer->customer_name : '',
            'phone' => isset(json_decode($quotation->products)[10]) ? json_decode($quotation->products)[10]->customer->customer_phone : '',
            'address' => isset(json_decode($quotation->products)[10]) ? json_decode($quotation->products)[10]->customer->customer_address : '',
        ];
    }

    $total = 0;

    $html = '
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Quotation</title>
            <style>
                @page {
                    margin: 10px;
                    height: auto;
                    width: 210mm;
                }

                body {
                    margin: 10px;
                }
            </style>
        </head>

        <body style="font-family: Arial, sans-serif; margin: 0; padding: 30px; padding-top: 30px; box-sizing: border-box;">

            <header style="text-align: center; margin-bottom: 20px;">
                <h1 style="margin: 0; font-size: 24px;">Quotation</h1>
                <p style="margin: 5px 0; font-size: 14px; color: #666;">' . $company->company_name . '</p>
                <p style="margin: 5px 0; font-size: 14px; color: #666;">' . getUserData($company->admin_id)->address . ' <br> '
        . formatPhoneNumber(getUserData($company->admin_id)->phone) . ' <br> www.wefix.lk</p>
            </header>

            <section style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <tr>
                            <td style="vertical-align: bottom;">
                                <p style="font-size: 13px;"><strong>Customer Name:</strong> ' .
        $customer->name . '</p>
                                                <p style="font-size: 13px;"><strong>Phone Number:</strong> ' .
        $customer->phone . '</p>
                                                <p style="font-size: 13px;"><strong>Address:</strong> ' .
        $customer->address . '</p>
                                <p style="font-size: 13px; text-transform: capitalize"><strong>Bill Number:</strong> ' .
        $repair->bill_no . '</p>
                                <p style="font-size: 13px;"><strong>Quotation Number:</strong> ' .
        $q_no . '</p>
                            </td>

                            <td style="vertical-align: bottom;">
                                <p style="font-size: 13px; text-align: right;"><strong>Date:</strong> ' . date(
            'Y-m-d',
            strtotime($quotation->created_at)
        ) . '</p>
                                <p style="font-size: 13px; text-align: right;"><strong>Valid Until:</strong> ' . date(
            'Y-m-d',
            strtotime($quotation->expiry_date)
        ) . '</p>
                                <p style="font-size: 13px; text-align: right;"><strong>Cargo Type:</strong> ' .
        $quotation->cargo_type . '</p>
                                <p style="font-size: 13px; text-align: right;"><strong>Estimated Delivery:</strong> ' .
        date('Y-m-d', strtotime($quotation->delivery_date)) . '</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f4f4f4;">
                            <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">Model No</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">Serial No</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">Fault</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
        ';

    if ($quotation->bill_no == 'custom') {
        foreach (json_decode($quotation->products) as $key => $product) {
            if (!empty($product->model_no) || !empty($product->serial_no) || !empty($product->fault)) {
                (float)$total += $product->price;
                $html .= '
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . $product->model_no . '</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . $product->serial_no . '</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . $product->fault . '</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . $product->price . '</td>
                        </tr>
                    ';
            }
        }
    } else {
        $html .= '
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . $repair->model_no . '</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . $repair->serial_no . '</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . $repair->fault . '</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . $quotation->total . '</td>
                    </tr>
            ';
    }

    $html .= '
                    </tbody>
                </table>
            </section>

            <table style="width: 100%; border-collapse: collapse; border-bottom: 1px solid #ddd;margin-bottom: 20px;">
                <tbody>
                    <tr>
                        <td style="vertical-align: top;">
                            <div style="margin-top: 0px; text-align: right; font-size: 18px;">
                        <p>Sub Total: ' . ($quotation->bill_no == "custom" ? currency($total, 'LKR') : currency($quotation->total, 'LKR')) . '</p>
                            </div>
                            <div style="margin-top: -20px; text-align: right; font-size: 18px;">
                                <p>Paid Advance: ' . currency($repair->advance, 'LKR') . '</p>
                            </div>
                            <div style="margin-top: -20px; text-align: right; font-size: 18px; font-weight: bold;">
                                <p>Total Due: ' . ($quotation->bill_no == "custom" ? currency($total, 'LKR') : currency($quotation->total - $repair->advance, 'LKR')) . '</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table style="border-bottom: 1px solid #ddd;padding-bottom: 20px;width: 100%;">
                <tr>
                    <td style="width: max-content;">
                        <b style="font-size: 13px;color: red;margin-top: 20px;width: 400px;">Please Note:</b>
                        <p style="font-size: 13px;color: #838383;margin-top: 5px;padding-bottom: 20px;width: 400px;">
                            While we ensure proper documentation and preparation for the shipment process, any delays,
                            penalties, or issues arising from government regulations, customs policies, or unforeseen legal
                            requirements during the clearing of the shipment will be beyond our responsibility. The client is
                            advised to ensure compliance with all relevant laws and requirements to avoid such situations.</p>
                    </td>
                    <td>
                        <table>
                            <thead>
                                <tr style="background-color: #f4f4f4;">
                                    <th style="text-align: left;width: 250px;padding: 5px 0;font-size: 13px;">Checking Charges</th>
                                    <th style="text-align: right;padding: 5px;font-size: 13px;">Rs</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="text-align: left;padding-top: 10px;font-size: 13px;">24" INCH LCD LED</td>
                                    <td style="text-align: right;padding-top: 10px;font-size: 13px;">1,000.00</td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;font-size: 13px;">32" INCH LCD LED</td>
                                    <td style="text-align: right;font-size: 13px;">1,500.00</td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;font-size: 13px;">40" TO 50" INCH LCD LED</td>
                                    <td style="text-align: right;font-size: 13px;">2,000.00</td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;font-size: 13px;">55" INCH LCD LED</td>
                                    <td style="text-align: right;font-size: 13px;">3,000.00</td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;font-size: 13px;">55" TO 100" INCH LCD LED</td>
                                    <td style="text-align: right;font-size: 13px;">5,000.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>

            <p style="margin-top: 20px;"><strong>Bank Details:</strong></p>

            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td style="vertical-align: top;">
                            <section style="">
                                <p>BOC Bank:</p>
                                <ul style="padding: 0; margin: 0;">
                                    <li style="list-style: none;">Branch: Grandpass</li>
                                    <li style="list-style: none;">A/C Number: 86433388</li>
                                    <li style="list-style: none;">A/C Name: M.N.Sirajdeen</li>
                                </ul>
                            </section>
                        </td>
                        <td style="vertical-align: top;">
                            <section style="">
                                <p>Sampath Bank:</p>
                                <ul style="padding: 0; margin: 0;">
                                    <li style="list-style: none;">Branch: Prince Street</li>
                                    <li style="list-style: none;">A/C Number: 104254656031</li>
                                    <li style="list-style: none;">A/C Name: M.N.Sirajdeen</li>
                                </ul>
                            </section>
                        </td>
                        <td style="vertical-align: top;">
                            <section style="">
                                <p>Nation Trust Bank:</p>
                                <ul style="padding: 0; margin: 0;">
                                    <li style="list-style: none;">Branch: Sri Sangarajah Mawatte</li>
                                    <li style="list-style: none;">A/C No: 003108033298</li>
                                    <li style="list-style: none;">A/C Name: M.A.M.Rameez</li>
                                </ul>
                            </section>
                        </td>
                    </tr>

                    <tr>
                        <td style="vertical-align: top;">
                            <section style="">
                                <p>Commercial Bank:</p>
                                <ul style="padding: 0; margin: 0;">
                                    <li style="list-style: none;">Branch: Kezar Street</li>
                                    <li style="list-style: none;">A/C No: 8017429449</li>
                                    <li style="list-style: none;">A/C Name: M.N.Sirajdeen</li>
                                </ul>
                            </section>
                        </td>
                    </tr>
                </tbody>
            </table>

            <footer style="text-align: center; font-size: 12px; color: #777;">
                <p>Thank you for choosing our services!</p>
            </footer>
        </body>

        </html>
    ';
    // $connector = new FilePrintConnector("/dev/usb/lp0");
    // $printer = new Printer($connector);

    $pdf = new Dompdf();
    $pdf->setPaper("A4", "portrait");
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('quotations/' . str_replace([' ', '.', "'", '"'], ['', '', "", ''], $q_no) . '-' . $company->pos_code . '.pdf');
    file_put_contents($path, $pdf->output());
    return (object)array('generated' => true, 'url' => '/quotations/' . str_replace([' ', '.', "'", '"'], ['', '', "", ''], $q_no) . '-' . $company->pos_code . '.pdf');
}

function generateEmployeeExpenses($datas, $inName = 'employee-expenses.pdf')
{

    $total = 0;

    $html = '
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Quotation</title>
            <style>
                @page {
                    margin: 10px;
                    height: auto;
                    width: 210mm;
                }

                body {
                    margin: 10px;
                }
            </style>
        </head>

        <body style="font-family: Arial, sans-serif; margin: 0; padding: 30px; padding-top: 30px; box-sizing: border-box;">

        <h1 style="text-align:center;">Expenses</h1>

            <section style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f4f4f4;">
                            <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">Employee</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">Amount</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">Type</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
        ';

    foreach ($datas as $key => $data) {
        if ($data->type == 'Loan' && $data->status == 'paid') {
            // Skip paid loans
        } else {
            $total += $data->amount;
        }
        $html .= '
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">' . getUser($data->user)->fname . '</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">' . currency($data->amount, '') . '</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">' . ($data->type == 'Loan' ? ($data->status == 'paid' ? 'Loan (Paid)' : 'Loan (Unpaid)') : $data->type) . '</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">' . $data->created_at . '</td>
                </tr>
            ';
    }

    $html .= '
                    </tbody>
                </table>
            </section>

            <table style="width: 100%; border-collapse: collapse; border-bottom: 1px solid #ddd;margin-bottom: 20px;">
                <tbody>
                    <tr>
                        <td style="vertical-align: top;">
                            <div style="margin-top: -20px; text-align: right; font-size: 18px; font-weight: bold;">
                                <p>Total Expenses: ' . currency($total, 'LKR') . '</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </body>

        </html>
    ';
    // $connector = new FilePrintConnector("/dev/usb/lp0");
    // $printer = new Printer($connector);

    $pdf = new Dompdf();
    $pdf->setPaper("A4", "portrait");
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('invoice/' . $inName);
    file_put_contents($path, $pdf->output());
    return (object)array('generated' => true, 'url' => '/invoice/' . $inName);
}

function generateLowStockReport($low, $inName = 'low-stock-report.pdf')
{
    $products = products::where('qty', '<=', (int)$low)->get();

    $html = '
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
            <style>
                @page {
                    margin: 10px;
                    height: auto;
                    width: 210mm;
                 }
                body { margin: 10px; }
            </style>
        </head>
        <body style="font-family: Arial, sans-serif;">

                <!-- Header -->
            <div style="text-align: center; margin-bottom: 20px; margin-top: 30px;">
                <h2 style="margin: 20px 0;">Stock Report</h2>
            </div>

            <!-- Item Details -->
            <div style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">SKU</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Product</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: right;">Price</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: right;">Stock</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: right;">Stock value</th>
                    </tr>
    ';

    $total = 0;

    foreach ($products as $key => $pro) {
        $total += $pro->cost * $pro->qty;
        $html .= '
            <tr>
                <td style="padding: 5px; border: 1px solid black;">' . $pro->sku . '</td>
                <td style="padding: 5px; border: 1px solid black;">' . $pro->pro_name . '</td>
                <td style="padding: 5px; border: 1px solid black; text-align: right;">' . currency($pro->price, '') . '</td>
                <td style="padding: 5px; border: 1px solid black; text-align: right;">' . $pro->qty . '</td>
                <td style="padding: 5px; border: 1px solid black; text-align: right;">' . currency(($pro->cost * $pro->qty), '') . '</td>
            </tr>
        ';
    }

    $html .= '
            </table>
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <tr>
                    <th style="color: #000;padding: 5px; border: 1px solid black; text-align: right;">Total stock value</th>
                </tr>
                <tr>
                    <td style="padding: 5px; border: 1px solid black;text-align: right;">' . currency($total, 'LKR') . '</td>
                </tr>
            </table>
        </div>
    ';

    $html .= '
        </body>
        </html>
    ';

    // $connector = new FilePrintConnector("/dev/usb/lp0");
    // $printer = new Printer($connector);

    $pdf = new Dompdf();
    $pdf->setPaper("A4", "portrait");
    $pdf->loadHtml($html, 'UTF-8');

    $GLOBALS['bodyHeight'] = 0;

    $pdf->setCallbacks([
        'myCallbacks' => [
            'event' => 'end_frame',
            'f' => function ($frame) {
                $node = $frame->get_node();
                if (strtolower($node->nodeName) === "body") {
                    $padding_box = $frame->get_padding_box();
                    $GLOBALS['bodyHeight'] += $padding_box['h'];
                }
            }
        ]
    ]);

    $pdf->render();
    unset($pdf);

    $docHeight = $GLOBALS['bodyHeight'] + 30;

    $pdf = new Dompdf();
    $pdf->setPaper("A4", "portrait");
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('invoice/' . $inName);
    file_put_contents($path, $pdf->output());

    return (object)array('generated' => true, 'url' => $inName);
}

function sendInvitation($email)
{
    $verify = User::where('email', $email)->get();
    if ($verify && $verify->count() > 0) {

        $invite_code = Str::random(30) . rand(1111, 9999999);
        $invite = new PosInvitation();
        $invite->user_id = $verify[0]->id;
        $invite->pos_code = company()->pos_code;
        $invite->invitation_id = $invite_code;
        $invite->status = "pending";

        if ($invite->save()) {
            return true;
        }
    }
    return false;
}

function getOrder($type = "", $order_number, $pos_id)
{
    $order_number = $order_number;
    $pos_id = $pos_id;
    $order = Repairs::where('bill_no', $order_number)->where('pos_code', $pos_id)->get();

    if ($order->count() > 0) {
        return (object)array(
            "error" => 0,
            "message" => "",
            "URL" => $order[0]->invoice
        );
    }

    return (object)array(
        "error" => 1,
        "message" => "No invoice found",
        "URL" => ""
    );
}

function paymentMethod($method)
{
    if ($method == 'cash') {
        return (object)array("class" => 'success', 'method' => 'Cash');
    }
    if ($method == 'card') {
        return (object)array("class" => 'success', 'method' => 'Card/Online');
    }
    if ($method == 'credit') {
        return (object)array("class" => 'warning', 'method' => 'Credit');
    }

    return (object)array("class" => 'danger', 'method' => 'N/A');
}

function getCaptcha()
{
    return '<div class="cf-turnstile" data-sitekey="' . env('CAPTCHA_SITE_KEY') . '" data-callback="javascriptCallback"></div>';
}

function captchaVerify($cf_turnstile_response)
{
    $ip = '';
    $headers = getallheaders();
    $headers = array_change_key_case($headers, CASE_LOWER);
    if (array_key_exists('cf-connecting-ip', $headers)) {
        $ip = $headers['cf-connecting-ip'];
    } else {
        if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (array_key_exists('HTTP_X_FORWARDED', $_SERVER)) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } else if (array_key_exists('HTTP_FORWARDED_FOR', $_SERVER)) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (array_key_exists('HTTP_FORWARDED', $_SERVER)) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    }

    if (empty($ip)) {
        return view('auth.login')->with('error', 'CAPTCHA verification error');
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret' => env('CAPTCHA_SITE_SECRET'),
        'response' => $cf_turnstile_response,
        'remoteip' => $ip,
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $output = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($output, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return (object) array('error' => true, 'msg' => 'Cannot verify CAPTCHA at this time');
    }
    if (!(is_array($response) && sizeof($response) > 0)) {
        return (object) array('error' => true, 'msg' => 'CAPTCHA verification error');
    }
    if (sizeof(array_diff(['success', 'hostname', 'error-codes', 'challenge_ts'], array_keys($response))) > 0) {
        // verification fail, not all required fields exists
        return (object) array('error' => true, 'msg' => 'CAPTCHA verification error');
    }
    if (!!$response['success'] && $response['hostname'] == env('APP_DOMAIN') && strtotime('now') - strtotime($response['challenge_ts']) < (strtotime('now') + 60)) {
        return (object) array('error' => false, 'msg' => 'CAPTCHA verification success');
    } else {
        return (object) array('error' => true, 'msg' => 'CAPTCHA verification error');
    }
    return (object) array('error' => true, 'msg' => 'CAPTCHA verification error');
}

function hasDashboard()
{
    if (Auth::check()) {
        $dash = posData::where('admin_id', Auth::user()->id)->where("status", 'active')->get();
        if ($dash && $dash->count() > 0) {
            return true;
        }
    }

    return false;
}

function getTotalRepairSum($repairs, $field)
{
    $sum = 0;

    $repairs = (array)$repairs;

    if (count($repairs) > 0) {
        foreach ($repairs as $key => $repair) {

            $sum += $repair[$field];

            foreach ($repair["child"] as $key => $child) {
                $sum += $child[$field];
            }
        }
    }

    return $sum;
}

function getSpare($id)
{
    $pare = products::where('id', $id)->get();
    if ($pare && $pare->count() > 0) {
        return (object)array(
            "code" => $pare[0]["sku"],
            "name" => $pare[0]["pro_name"],
            "price" => $pare[0]["price"],
            "cost" => $pare[0]["cost"],
            "qty" => $pare[0]["qty"],
        );
    }

    return (object)array(
        "code" => "",
        "name" => "",
        "price" => "",
        "cost" => "",
        "qty" => "",
    );
}

function getRepair($id)
{
    $repair = Repairs::where('bill_no', $id)->where('pos_code', company()->pos_code)->first();

    if ($repair && $repair->count() > 0) {
        return $repair;
    }

    return (object)array(
        "bill_no" => '',
        "model_no" => '',
        "serial_no" => '',
        "fault" => '',
        "note" => '',
        "advance" => '',
        "total" => '',
        "cost" => '',
        "customer" => '',
        "partner" => '',
        "cashier" => '',
        "techie" => '',
        "spares" => '',
        "status" => '',
        "pos_code" => '',
        "invoice" => '',
        "type" => '',
        "products" => '',
        "parent" => '',
        "created_at" => '',
        "updated_at" => '',
    );
}

function statusToBootstrap($status)
{
    switch ($status) {
        case 'pending':
            return 'warning';
            break;
        case 'paid':
            return 'success';
            break;
        case 'approved':
            return 'success';
            break;
        case 'returned':
            return 'danger';
            break;
        case 'cancelled':
            return 'danger';
            break;
        case 'blocked':
            return 'secondary';
            break;
        default:
            # code...
            break;
    }
}


function divide($num1, $num2)
{
    if ($num1 != 0 && $num2 != 0) {
        return number_format(($num1 / $num2), 1);
    }

    return 0;
}
