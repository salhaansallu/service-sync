<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Checkout | NMSware Technologies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('assets/assets/js/jquery.creditCardValidator.js') }}"></script>
    <script type="text/javascript" src="https://www.2checkout.com/checkout/api/2co.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700');

        body {
            font-family: 'Roboto Condensed', sans-serif;
            color: #262626;
            margin: 5% 0;
            background-color: #fff
        }

        #error-message {
            margin: 20px 0;
        }

        .container {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        @media (min-width: 1200px) {
            .container {
                max-width: 1140px;
            }
        }

        .d-xl-flex {
            display: flex;
            flex-direction: row;
            background: #f6f6f6;
            border-radius: 0 0 5px 5px;
            padding: 25px;
            background-color: #f6f6f6;
        }

        form {
            flex: 4;
            background-color: #f6f6f6;
        }

        .Yorder {
            flex: 2;
            background-color: #f6f6f6;
        }

        .title {
            background: -webkit-gradient(linear, left top, right bottom, color-stop(0, #5195A8), color-stop(100, #70EAFF));
            background: -moz-linear-gradient(top left, #5195A8 0%, #70EAFF 100%);
            background: -ms-linear-gradient(top left, #5195A8 0%, #70EAFF 100%);
            background: -o-linear-gradient(top left, #5195A8 0%, #70EAFF 100%);
            background: linear-gradient(to bottom right, #5195A8 0%, #70EAFF 100%);
            border-radius: 5px 5px 0 0;
            padding: 20px;
            color: #f6f6f6;
        }

        h2 {
            margin: 0;
            padding-left: 15px;
        }

        .required {
            color: red;
        }

        label,
        table {
            display: block;
            margin: 15px;
        }

        label>span {
            float: left;
            width: 25%;
            margin-top: 12px;
            padding-right: 10px;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        select {
            width: 70%;
            height: 30px;
            padding: 5px 10px;
            margin-bottom: 10px;
            border: 1px solid #dadada;
            color: #888;
            outline: none
        }

        select {
            width: 72%;
            height: 45px;
            padding: 5px 10px;
            margin-bottom: 10px;
        }

        .Yorder {
            margin-top: 15px;
            height: 400px;
            padding: 20px;
            border: 1px solid #dadada;
        }

        table {
            margin: 0;
            padding: 0;
        }

        th {
            border-bottom: 1px solid #dadada;
            padding: 10px 0;
        }

        tr>td:nth-child(1) {
            text-align: left;
            color: #2d2d2a;
        }

        tr>td:nth-child(2) {
            text-align: right;
            color: #52ad9c;
        }

        td {
            border-bottom: 1px solid #dadada;
            padding: 25px 25px 25px 0;
        }

        p {
            display: block;
            color: #888;
            margin: 0;
            padding-left: 25px;
        }

        .Yorder>div {
            padding: 15px 0;
        }

        button {
            width: 50%;
            margin-top: 10px;
            margin-left: 15px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: #52ad9c;
            color: #fff;
            font-size: 15px;
            font-weight: bold;
        }

        input[type="submit"] {
            width: 50%;
            margin-top: 10px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: #52ad9c;
            color: #fff;
            font-size: 15px;
            font-weight: bold;
        }

        button:hover {
            cursor: pointer;
            background: #428a7d;
        }

        @media (max-width: 545px) {
            label {
                display: flex;
                flex-direction: column;
                margin-bottom: 0px;
            }

            label>span {
                width: 100%;
            }

            input {
                margin-bottom: 0 !important;
                width: 100% !important;
            }

            input[type="submit"] {
                margin-bottom: 25px !important;
            }
        }

        input.required {
            border: rgb(255, 0, 0) 1px solid;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="title">
            <h2>Checkout Here</h2>
        </div>
        <div class="d-block d-xl-flex d-lg-flex">
            <form id="paymentForm" action="/checkout" onsubmit="return false;" method="POST">
                @csrf
                <div id="error-message"></div>
                <input id="token" name="token" type="hidden" value="">
                <label>
                    <span class="fname">Card Number <span class="required">*</span></span>
                    <input type="text" placeholder="1234 5678 9012 3456" maxlength="20" id="card_number"
                        name="card_number">
                </label>
                <label>
                    <span class="lname">Expiry Month <span class="required">*</span></span>
                    <input type="text" placeholder="MM" maxlength="2" id="expiry_month" name="expiry_month">
                </label>
                <label>
                    <span class="lname">Expiry Year <span class="required">*</span></span>
                    <input type="text" placeholder="YYYY" maxlength="4" id="expiry_year" name="expiry_year">
                </label>
                <label>
                    <span class="lname">CVV <span class="required">*</span></span>
                    <input type="text" placeholder="123" maxlength="4" id="cvv" name="cvv">
                </label>
                <label>
                    <span class="lname"></span>
                    <input type="submit" name="card_submit" id="cardSubmitBtn" value="Confirm" />
                </label>
                <input type="hidden" name="card_type" id="card_type" value="" />
            </form>
            <div class="Yorder">
                <table>
                    <tr>
                        <th colspan="2">Your order</th>
                    </tr>
                    <tr>
                        <td>{{ $name }} plan of NMSware Cloud POS</td>
                        <td>${{ $amount }}</td>
                    </tr>
                    <tr>
                        <td>Subtotal</td>
                        <td>${{ $amount }}</td>
                    </tr>
                </table><br>
            </div><!-- Yorder -->
        </div>
    </div>

    <script type="text/javascript">
        function cardFormValidate() {
            var cardValid = 0;

            // Card number validation
            $('#card_number').validateCreditCard(function(result) {
                var cardType = (result.card_type == null) ? '' : result.card_type.name;
                if (cardType == 'Visa') {
                    var backPosition = result.valid ? '2px -163px, 320px -87px' : '2px -163px, 320px -61px';
                } else if (cardType == 'MasterCard') {
                    var backPosition = result.valid ? '2px -247px, 320px -87px' : '2px -247px, 320px -61px';
                } else if (cardType == 'Maestro') {
                    var backPosition = result.valid ? '2px -289px, 320px -87px' : '2px -289px, 320px -61px';
                } else if (cardType == 'Discover') {
                    var backPosition = result.valid ? '2px -331px, 320px -87px' : '2px -331px, 320px -61px';
                } else if (cardType == 'Amex') {
                    var backPosition = result.valid ? '2px -121px, 320px -87px' : '2px -121px, 320px -61px';
                } else {
                    var backPosition = result.valid ? '2px -121px, 320px -87px' : '2px -121px, 320px -61px';
                }
                $('#card_number').css("background-position", backPosition);
                if (result.valid) {
                    $("#card_type").val(cardType);
                    $("#card_number").removeClass('required');
                    cardValid = 1;
                } else {
                    $("#card_type").val('');
                    $("#card_number").addClass('required');
                    cardValid = 0;
                }
            });

            // Card details validation
            var email = $("#email").val();
            var cardName = $("#name_on_card").val();
            var expMonth = $("#expiry_month").val();
            var expYear = $("#expiry_year").val();
            var cvv = $("#cvv").val();
            var regName = /^[a-z ,.'-]+$/i;
            var regEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
            var regMonth = /^01|02|03|04|05|06|07|08|09|10|11|12$/;
            var regYear = /^2023|2024|2025|2026|2027|2028|2029|2030|2031|2032|2033|2034|2035|2036$/;
            var regCVV = /^[0-9]{3,3}$/;
            if (cardValid == 0) {
                $("#card_number").addClass('required');
                $("#card_number").focus();
                return false;
            } else if (!regMonth.test(expMonth)) {
                $("#card_number").removeClass('required');
                $("#expiry_month").addClass('required');
                $("#expiry_month").focus();
                return false;
            } else if (!regYear.test(expYear)) {
                $("#card_number").removeClass('required');
                $("#expiry_month").removeClass('required');
                $("#expiry_year").addClass('required');
                $("#expiry_year").focus();
                return false;
            } else if (!regCVV.test(cvv)) {
                $("#card_number").removeClass('required');
                $("#expiry_month").removeClass('required');
                $("#expiry_year").removeClass('required');
                $("#cvv").addClass('required');
                $("#cvv").focus();
                return false;
            } else {
                $("#card_number").removeClass('required');
                $("#expiry_month").removeClass('required');
                $("#expiry_year").removeClass('required');
                $("#cvv").removeClass('required');
                $("#name_on_card").removeClass('required');
                return true;
            }
        }

        $(document).ready(function() {
            //card validation on input fields
            $('#paymentForm input[type=text]').on('keyup', function() {
                cardFormValidate();
            });
        });

        //2Checkout JavaScript library
        // A success callback of TCO token request
        var success = function(data) {
            // Set the token in the payment form
            $('#paymentForm #token').val(data.response.token.token);

            $("#error-message").hide();
            $("#error-message").html("");

            // Submit the form with TCO token
            $('#paymentForm').removeAttr('onsubmit');
            $('#paymentForm').submit();
        };

        // A Error callback of TCO token request.
        var error = function(data) {
            var errorMsg = "";
            if (data.errorCode === 200) {
                tokenRequest();
            } else {
                errorMsg = data.errorMsg;
                $("#error-message").show();
                $("#error-message").html(errorMsg);
                $("#submit-btn").show();
                $("#loader").hide();
            }
        };

        function tokenRequest() {
            //var valid = cardFormValidate();
            var valid = true;
            if (valid == true) {
                $("#submit-btn").hide();
                $("#loader").css("display", "inline-block");
                var args = {
                    sellerId: '{{ env("2C_SELLER_ID") }}',
                    publishableKey: '{{ env("2C_PUBLISHER_KEY") }}',
                    ccNo: $("#card_number").val(),
                    cvv: $("#cvv").val(),
                    expMonth: $("#expiry_month").val(),
                    expYear: $("#expiry_year").val()
                };

                // Request 2Checkout token
                TCO.requestToken(success, error, args);
            }
        }

        $(function() {
            TCO.loadPubKey('production');
            $("#cardSubmitBtn").on('click', function(e) {
                tokenRequest();
                return false;
            });
        });
    </script>
</body>

</html>
