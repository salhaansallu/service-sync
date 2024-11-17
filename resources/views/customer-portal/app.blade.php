<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>NMSware CRM System</title>
    <meta name="robots" content="noindex">


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/images/brand/favicon3.ico') }}" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assets/css/backende209.css?v=1.0.0') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assets/vendor/remixicon/fonts/remixicon.css') }}">

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @vite(['resources/views/pos/sass/app.scss', 'resources/js/app.js'])
    <script src="{{ asset('assets/assets/js/backend-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/assets/js/table-treeview.js') }}"></script>
    <script src="{{ asset('assets/assets/js/customizer.js') }}"></script>
    <script async src="{{ asset('assets/assets/js/chart-custom.js') }}"></script>
    <script src="{{ asset('assets/assets/js/app.js') }}"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.1/js/dataTables.fixedColumns.min.js"></script>
    <script>
        window.addEventListener('offline', () => {
            alert('No internet connection, please check your network');
        });

        window.addEventListener('click', () => {
            if (!window.navigator.onLine) {
                alert('No internet connection, please check your network');
            }
        });
    </script>
</head>

<body class=" ">
    @yield('customer')
</body>

</html>
