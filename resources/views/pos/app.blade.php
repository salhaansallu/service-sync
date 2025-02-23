<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>We Fix CRM System</title>
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

    <div class="wrapper">
        <div class="iq-sidebar  sidebar-default ">
            <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
                <a href="/dashboard" class="header-logo">
                    {{-- <img src="{{ asset('assets/images/brand/logo.png') }}" class="light-logo" alt="logo"> --}}
                    <h5 class="logo-title light-logo ml-3">We Fix</h5>
                </a>
                <div class="iq-menu-bt-sidebar ml-0">
                    <i class="fa-solid fa-bars wrapper-menu"></i>
                </div>
            </div>
            <div class="custom-scroller">
                <nav class="iq-sidebar-menu">
                    <ul id="iq-sidebar-toggle" class="iq-menu">
                        <li class="">
                            <a href="/pos" class="svg-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" fill="currentColor"
                                    height="20" viewBox="0 0 640 512">
                                    <path
                                        d="M64 64l0 288 512 0 0-288L64 64zM0 64C0 28.7 28.7 0 64 0L576 0c35.3 0 64 28.7 64 64l0 288c0 35.3-28.7 64-64 64L64 416c-35.3 0-64-28.7-64-64L0 64zM128 448l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-384 0c-17.7 0-32-14.3-32-32s14.3-32 32-32z" />
                                </svg>
                                <span class="ml-4">TV Repairs</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="/other-pos" class="svg-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                    stroke="currentColor" stroke-width="30" stroke-linecap="round"
                                    stroke-linejoin="round" viewBox="0 0 512 512">
                                    <path
                                        d="M352 320c88.4 0 160-71.6 160-160c0-15.3-2.2-30.1-6.2-44.2c-3.1-10.8-16.4-13.2-24.3-5.3l-76.8 76.8c-3 3-7.1 4.7-11.3 4.7L336 192c-8.8 0-16-7.2-16-16l0-57.4c0-4.2 1.7-8.3 4.7-11.3l76.8-76.8c7.9-7.9 5.4-21.2-5.3-24.3C382.1 2.2 367.3 0 352 0C263.6 0 192 71.6 192 160c0 19.1 3.4 37.5 9.5 54.5L19.9 396.1C7.2 408.8 0 426.1 0 444.1C0 481.6 30.4 512 67.9 512c18 0 35.3-7.2 48-19.9L297.5 310.5c17 6.2 35.4 9.5 54.5 9.5zM80 408a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                </svg>
                                <span class="ml-4">Other Repairs</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                            <a href="/dashboard" class="svg-icon">
                                <svg class="svg-icon" id="p-dash1" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                    </path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                                <span class="ml-4">Dashboards</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('dashboard/products*') ? 'active' : '' }}">
                            <a href="#product" class="collapsed {{ company()->plan == 1 ? 'no-collapsable' : '' }}"
                                data-toggle="collapse" aria-expanded="false">
                                <svg class="svg-icon" id="p-dash2" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <span class="ml-4">Spare Parts</span>
                                @if (company()->plan == 1)
                                    <span class="badge"><i class="fa-solid fa-crown text-warning"></i></span>
                                @else
                                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="10 15 15 20 20 15"></polyline>
                                        <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                    </svg>
                                @endif
                            </a>
                            <ul id="product"
                                class="iq-submenu collapse {{ Request::is('dashboard/products*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/products') ? 'active' : '' }}">
                                    <a href="/dashboard/products">
                                        <i class="fa-solid fa-minus"></i><span>List Spare Parts</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/products/create') ? 'active' : '' }}">
                                    <a href="/dashboard/products/create">
                                        <i class="fa-solid fa-minus"></i><span>Add Spare Parts</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ Request::is('dashboard/repairs*') ? 'active' : '' }}">
                            <a href="#category" class="collapsed {{ company()->plan == 1 ? 'no-collapsable' : '' }}"
                                data-toggle="collapse" aria-expanded="false">
                                <svg class="svg-icon" id="p-dash7" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="none"
                                    stroke="currentColor" stroke-width="30" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M352 320c88.4 0 160-71.6 160-160c0-15.3-2.2-30.1-6.2-44.2c-3.1-10.8-16.4-13.2-24.3-5.3l-76.8 76.8c-3 3-7.1 4.7-11.3 4.7L336 192c-8.8 0-16-7.2-16-16l0-57.4c0-4.2 1.7-8.3 4.7-11.3l76.8-76.8c7.9-7.9 5.4-21.2-5.3-24.3C382.1 2.2 367.3 0 352 0C263.6 0 192 71.6 192 160c0 19.1 3.4 37.5 9.5 54.5L19.9 396.1C7.2 408.8 0 426.1 0 444.1C0 481.6 30.4 512 67.9 512c18 0 35.3-7.2 48-19.9L297.5 310.5c17 6.2 35.4 9.5 54.5 9.5zM80 408a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                </svg>
                                <span class="ml-4">Repairs</span>
                                @if (company()->plan == 1)
                                    <span class="badge"><i class="fa-solid fa-crown text-warning"></i></span>
                                @else
                                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="10 15 15 20 20 15"></polyline>
                                        <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                    </svg>
                                @endif
                            </a>
                            <ul id="category"
                                class="iq-submenu collapse {{ Request::is('dashboard/repairs*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/repairs') ? 'active' : '' }}">
                                    <a href="/dashboard/repairs">
                                        <i class="fa-solid fa-minus"></i><span>List TV Repairs</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/repairs/other-repairs') ? 'active' : '' }}">
                                    <a href="/dashboard/repairs/other-repairs">
                                        <i class="fa-solid fa-minus"></i><span>List Other Repairs</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ Request::is('dashboard/repair-commissions*') ? 'active' : '' }}">
                            <a href="#commission" class="collapsed {{ company()->plan == 1 ? 'no-collapsable' : '' }}"
                                data-toggle="collapse" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="svg-icon" width="20" height="20" viewBox="0 0 576 512" fill="none"
                                stroke="currentColor" stroke-width="30" stroke-linecap="round"
                                stroke-linejoin="round"><path d="M312 24l0 10.5c6.4 1.2 12.6 2.7 18.2 4.2c12.8 3.4 20.4 16.6 17 29.4s-16.6 20.4-29.4 17c-10.9-2.9-21.1-4.9-30.2-5c-7.3-.1-14.7 1.7-19.4 4.4c-2.1 1.3-3.1 2.4-3.5 3c-.3 .5-.7 1.2-.7 2.8c0 .3 0 .5 0 .6c.2 .2 .9 1.2 3.3 2.6c5.8 3.5 14.4 6.2 27.4 10.1l.9 .3s0 0 0 0c11.1 3.3 25.9 7.8 37.9 15.3c13.7 8.6 26.1 22.9 26.4 44.9c.3 22.5-11.4 38.9-26.7 48.5c-6.7 4.1-13.9 7-21.3 8.8l0 10.6c0 13.3-10.7 24-24 24s-24-10.7-24-24l0-11.4c-9.5-2.3-18.2-5.3-25.6-7.8c-2.1-.7-4.1-1.4-6-2c-12.6-4.2-19.4-17.8-15.2-30.4s17.8-19.4 30.4-15.2c2.6 .9 5 1.7 7.3 2.5c13.6 4.6 23.4 7.9 33.9 8.3c8 .3 15.1-1.6 19.2-4.1c1.9-1.2 2.8-2.2 3.2-2.9c.4-.6 .9-1.8 .8-4.1l0-.2c0-1 0-2.1-4-4.6c-5.7-3.6-14.3-6.4-27.1-10.3l-1.9-.6c-10.8-3.2-25-7.5-36.4-14.4c-13.5-8.1-26.5-22-26.6-44.1c-.1-22.9 12.9-38.6 27.7-47.4c6.4-3.8 13.3-6.4 20.2-8.2L264 24c0-13.3 10.7-24 24-24s24 10.7 24 24zM568.2 336.3c13.1 17.8 9.3 42.8-8.5 55.9L433.1 485.5c-23.4 17.2-51.6 26.5-80.7 26.5L192 512 32 512c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l36.8 0 44.9-36c22.7-18.2 50.9-28 80-28l78.3 0 16 0 64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0-16 0c-8.8 0-16 7.2-16 16s7.2 16 16 16l120.6 0 119.7-88.2c17.8-13.1 42.8-9.3 55.9 8.5zM193.6 384c0 0 0 0 0 0l-.9 0c.3 0 .6 0 .9 0z"/></svg>
                                <span class="ml-4">Repair Commissions</span>
                                @if (company()->plan == 1)
                                    <span class="badge"><i class="fa-solid fa-crown text-warning"></i></span>
                                @else
                                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="10 15 15 20 20 15"></polyline>
                                        <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                    </svg>
                                @endif
                            </a>
                            <ul id="commission"
                                class="iq-submenu collapse {{ Request::is('dashboard/repair-commissions*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/repair-commissions/list') ? 'active' : '' }}">
                                    <a href="/dashboard/repair-commissions/list">
                                        <i class="fa-solid fa-minus"></i><span>List Report</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ Request::is('dashboard/quotation*') ? 'active' : '' }}">
                            <a href="#quotation" class="collapsed {{ company()->plan == 1 ? 'no-collapsable' : '' }}"
                                data-toggle="collapse" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="svg-icon" width="20"
                                    height="20" fill="none" stroke="currentColor" stroke-width="40"
                                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 384 512">
                                    <path
                                        d="M64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-288-128 0c-17.7 0-32-14.3-32-32L224 0 64 0zM256 0l0 128 128 0L256 0zM64 80c0-8.8 7.2-16 16-16l64 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L80 96c-8.8 0-16-7.2-16-16zm0 64c0-8.8 7.2-16 16-16l64 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-64 0c-8.8 0-16-7.2-16-16zm128 72c8.8 0 16 7.2 16 16l0 17.3c8.5 1.2 16.7 3.1 24.1 5.1c8.5 2.3 13.6 11 11.3 19.6s-11 13.6-19.6 11.3c-11.1-3-22-5.2-32.1-5.3c-8.4-.1-17.4 1.8-23.6 5.5c-5.7 3.4-8.1 7.3-8.1 12.8c0 3.7 1.3 6.5 7.3 10.1c6.9 4.1 16.6 7.1 29.2 10.9l.5 .1s0 0 0 0s0 0 0 0c11.3 3.4 25.3 7.6 36.3 14.6c12.1 7.6 22.4 19.7 22.7 38.2c.3 19.3-9.6 33.3-22.9 41.6c-7.7 4.8-16.4 7.6-25.1 9.1l0 17.1c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-17.8c-11.2-2.1-21.7-5.7-30.9-8.9c0 0 0 0 0 0c-2.1-.7-4.2-1.4-6.2-2.1c-8.4-2.8-12.9-11.9-10.1-20.2s11.9-12.9 20.2-10.1c2.5 .8 4.8 1.6 7.1 2.4c0 0 0 0 0 0s0 0 0 0s0 0 0 0c13.6 4.6 24.6 8.4 36.3 8.7c9.1 .3 17.9-1.7 23.7-5.3c5.1-3.2 7.9-7.3 7.8-14c-.1-4.6-1.8-7.8-7.7-11.6c-6.8-4.3-16.5-7.4-29-11.2l-1.6-.5s0 0 0 0c-11-3.3-24.3-7.3-34.8-13.7c-12-7.2-22.6-18.9-22.7-37.3c-.1-19.4 10.8-32.8 23.8-40.5c7.5-4.4 15.8-7.2 24.1-8.7l0-17.3c0-8.8 7.2-16 16-16z" />
                                </svg>
                                <span class="ml-4">Quotations</span>
                                @if (company()->plan == 1)
                                    <span class="badge"><i class="fa-solid fa-crown text-warning"></i></span>
                                @else
                                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="10 15 15 20 20 15"></polyline>
                                        <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                    </svg>
                                @endif
                            </a>
                            <ul id="quotation"
                                class="iq-submenu collapse {{ Request::is('dashboard/quotation*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/quotations') ? 'active' : '' }}">
                                    <a href="/dashboard/quotations">
                                        <i class="fa-solid fa-minus"></i><span>List Quotations</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/quotations/create') ? 'active' : '' }}">
                                    <a href="/dashboard/quotations/create">
                                        <i class="fa-solid fa-minus"></i><span>Add Quotation</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ Request::is('dashboard/order*') ? 'active' : '' }}">
                            <a href="#orders" class="collapsed {{ company()->plan == 1 ? 'no-collapsable' : '' }}"
                                data-toggle="collapse" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="svg-icon" width="20"
                                    height="20" fill="none" stroke="currentColor" stroke-width="40"
                                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 640 512">
                                    <path
                                        d="M112 0C85.5 0 64 21.5 64 48l0 48L16 96c-8.8 0-16 7.2-16 16s7.2 16 16 16l48 0 208 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L64 160l-16 0c-8.8 0-16 7.2-16 16s7.2 16 16 16l16 0 176 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L64 224l-48 0c-8.8 0-16 7.2-16 16s7.2 16 16 16l48 0 144 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L64 288l0 128c0 53 43 96 96 96s96-43 96-96l128 0c0 53 43 96 96 96s96-43 96-96l32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-64 0-32 0-18.7c0-17-6.7-33.3-18.7-45.3L512 114.7c-12-12-28.3-18.7-45.3-18.7L416 96l0-48c0-26.5-21.5-48-48-48L112 0zM544 237.3l0 18.7-128 0 0-96 50.7 0L544 237.3zM160 368a48 48 0 1 1 0 96 48 48 0 1 1 0-96zm272 48a48 48 0 1 1 96 0 48 48 0 1 1 -96 0z" />
                                </svg>
                                <span class="ml-4">Orders</span>
                                @if (company()->plan == 1)
                                    <span class="badge"><i class="fa-solid fa-crown text-warning"></i></span>
                                @else
                                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="10 15 15 20 20 15"></polyline>
                                        <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                    </svg>
                                @endif
                            </a>
                            <ul id="orders"
                                class="iq-submenu collapse {{ Request::is('dashboard/order*') || Request::is('dashboard/bill*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/orders') ? 'active' : '' }}">
                                    <a href="/dashboard/orders">
                                        <i class="fa-solid fa-minus"></i><span>List Deliveries</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/bills') ? 'active' : '' }}">
                                    <a href="/dashboard/bills">
                                        <i class="fa-solid fa-minus"></i><span>List Orders</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ Request::is('dashboard/china-order*') ? 'active' : '' }}">
                            <a href="#chinaOrders"
                                class="collapsed {{ company()->plan == 1 ? 'no-collapsable' : '' }}"
                                data-toggle="collapse" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="svg-icon" width="20"
                                    height="20" fill="none" stroke="currentColor" stroke-width="40"
                                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 512 512">
                                    <path
                                        d="M192 93.7C192 59.5 221 0 256 0c36 0 64 59.5 64 93.7l0 66.3L497.8 278.5c8.9 5.9 14.2 15.9 14.2 26.6l0 56.7c0 10.9-10.7 18.6-21.1 15.2L320 320l0 80 57.6 43.2c4 3 6.4 7.8 6.4 12.8l0 42c0 7.8-6.3 14-14 14c-1.3 0-2.6-.2-3.9-.5L256 480 145.9 511.5c-1.3 .4-2.6 .5-3.9 .5c-7.8 0-14-6.3-14-14l0-42c0-5 2.4-9.8 6.4-12.8L192 400l0-80L21.1 377C10.7 380.4 0 372.7 0 361.8l0-56.7c0-10.7 5.3-20.7 14.2-26.6L192 160l0-66.3z" />
                                </svg>
                                <span class="ml-4">China Orders</span>
                                @if (company()->plan == 1)
                                    <span class="badge"><i class="fa-solid fa-crown text-warning"></i></span>
                                @else
                                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="10 15 15 20 20 15"></polyline>
                                        <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                    </svg>
                                @endif
                            </a>
                            <ul id="chinaOrders"
                                class="iq-submenu collapse {{ Request::is('dashboard/china-order*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/china-order-list') ? 'active' : '' }}">
                                    <a href="/dashboard/china-order-list">
                                        <i class="fa-solid fa-minus"></i><span>List China Orders</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/add-china-order') ? 'active' : '' }}">
                                    <a href="/dashboard/china-order-add">
                                        <i class="fa-solid fa-minus"></i><span>Add China Order</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ Request::is('dashboard/sales-report*') ? 'active' : '' }}">
                            <a href="#reports" class="collapsed {{ company()->plan == 1 ? 'no-collapsable' : '' }}"
                                data-toggle="collapse" aria-expanded="false">
                                <svg class="svg-icon" id="p-dash7" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                <span class="ml-4">Reports</span>
                                @if (company()->plan == 1)
                                    <span class="badge"><i class="fa-solid fa-crown text-warning"></i></span>
                                @else
                                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="10 15 15 20 20 15"></polyline>
                                        <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                    </svg>
                                @endif
                            </a>
                            <ul id="reports"
                                class="iq-submenu collapse {{ Request::is('dashboard/sales-report*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/sales-report') ? 'active' : '' }}">
                                    <a href="/dashboard/sales-report">
                                        <i class="fa-solid fa-minus"></i><span>Sales report</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/spare-report') ? 'active' : '' }}">
                                    <a href="/dashboard/spare-report">
                                        <i class="fa-solid fa-minus"></i><span>Spare parts report</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/sales-report/customer') ? 'active' : '' }}">
                                    <a href="/dashboard/sales-report/customer">
                                        <i class="fa-solid fa-minus"></i><span>Customer report</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/sales-report/re-service') ? 'active' : '' }}">
                                    <a href="/dashboard/sales-report/re-service">
                                        <i class="fa-solid fa-minus"></i><span>Re-service report</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/stock-report') ? 'active' : '' }}">
                                    <a href="/dashboard/stock-report">
                                        <i class="fa-solid fa-minus"></i><span>Stock report</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ Request::is('dashboard/petty-cash*') ? 'active' : '' }}">
                            <a href="#petty-cash"
                                class="collapsed {{ company()->plan == 1 ? 'no-collapsable' : '' }}"
                                data-toggle="collapse" aria-expanded="false">

                                <svg class="svg-icon" width="20" fill="currentColor" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                    <path
                                        d="M0 112.5L0 422.3c0 18 10.1 35 27 41.3c87 32.5 174 10.3 261-11.9c79.8-20.3 159.6-40.7 239.3-18.9c23 6.3 48.7-9.5 48.7-33.4l0-309.9c0-18-10.1-35-27-41.3C462 15.9 375 38.1 288 60.3C208.2 80.6 128.4 100.9 48.7 79.1C25.6 72.8 0 88.6 0 112.5zM288 352c-44.2 0-80-43-80-96s35.8-96 80-96s80 43 80 96s-35.8 96-80 96zM64 352c35.3 0 64 28.7 64 64l-64 0 0-64zm64-208c0 35.3-28.7 64-64 64l0-64 64 0zM512 304l0 64-64 0c0-35.3 28.7-64 64-64zM448 96l64 0 0 64c-35.3 0-64-28.7-64-64z" />
                                </svg>
                                <span class="ml-4">Petty Cash</span>
                                @if (company()->plan == 1)
                                    <span class="badge"><i class="fa-solid fa-crown text-warning"></i></span>
                                @else
                                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="10 15 15 20 20 15"></polyline>
                                        <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                    </svg>
                                @endif
                            </a>
                            <ul id="petty-cash"
                                class="iq-submenu collapse {{ Request::is('dashboard/petty-cash*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/petty-cash/tv-repairs') ? 'active' : '' }}">
                                    <a href="/dashboard/petty-cash/tv-repairs">
                                        <i class="fa-solid fa-minus"></i><span>TV Repairing</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/petty-cash/other-repairs') ? 'active' : '' }}">
                                    <a href="/dashboard/petty-cash/other-repairs">
                                        <i class="fa-solid fa-minus"></i><span>Other Repairing</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ Request::is('dashboard/purchase*') ? 'active' : '' }}">
                            <a href="#purchase" class="collapsed {{ company()->plan == 1 ? 'no-collapsable' : '' }}"
                                data-toggle="collapse" aria-expanded="false">
                                <svg class="svg-icon" id="p-dash5" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2">
                                    </rect>
                                    <line x1="1" y1="10" x2="23" y2="10"></line>
                                </svg>
                                <span class="ml-4">Purchases</span>
                                @if (company()->plan == 1)
                                    <span class="badge"><i class="fa-solid fa-crown text-warning"></i></span>
                                @else
                                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="10 15 15 20 20 15"></polyline>
                                        <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                    </svg>
                                @endif
                            </a>
                            <ul id="purchase"
                                class="iq-submenu collapse {{ Request::is('dashboard/purchase*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/purchases') ? 'active' : '' }}">
                                    <a href="/dashboard/purchases">
                                        <i class="fa-solid fa-minus"></i><span>List Purchases</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/purchase/create') ? 'active' : '' }}">
                                    <a href="/dashboard/purchase/create">
                                        <i class="fa-solid fa-minus"></i><span>Add purchase</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{{ Request::is('dashboard/product-purchase*') ? 'active' : '' }}">
                            <a href="#productPurchase" class="collapsed" data-toggle="collapse" aria-expanded="false">
                                <svg class="svg-icon" width="20" height="20" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M234.5 5.7c13.9-5 29.1-5 43.1 0l192 68.6C495 83.4 512 107.5 512 134.6l0 242.9c0 27-17 51.2-42.5 60.3l-192 68.6c-13.9 5-29.1 5-43.1 0l-192-68.6C17 428.6 0 404.5 0 377.4L0 134.6c0-27 17-51.2 42.5-60.3l192-68.6zM256 66L82.3 128 256 190l173.7-62L256 66zm32 368.6l160-57.1 0-188L288 246.6l0 188z"/></svg>
                                <span class="ml-4">Product Purchases</span>
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </a>
                            <ul id="productPurchase"
                                class="iq-submenu collapse {{ Request::is('dashboard/product-purchase*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li
                                    class="{{ isAdmin() ? '' : 'd-none' }} {{ Request::is('dashboard/product-purchases') ? 'active' : '' }}">
                                    <a href="/dashboard/product-purchases">
                                        <i class="fa-solid fa-minus"></i><span>List Purchases</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/product-purchase/create') ? 'active' : '' }}">
                                    <a href="/dashboard/product-purchase/create">
                                        <i class="fa-solid fa-minus"></i><span>Add purchase</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{{ Request::is('dashboard/credits') ? 'active' : '' }}">
                            <a href="{{ company()->plan == 1 ? '#' : '/dashboard/credits' }}" class="">
                                <svg class="svg-icon" id="p-dash7" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                <span class="ml-4">Manage Credit</span>
                                @if (company()->plan == 1)
                                    <span class="badge"><i class="fa-solid fa-crown text-warning"></i></span>
                                @endif
                            </a>
                        </li>
                        <li class="{{ Request::is('dashboard/invoice-settings') ? 'active' : '' }}">
                            <a href="{{ company()->plan == 1 ? '#' : '/dashboard/invoice-settings' }}"
                                class="">
                                <svg class="svg-icon" id="p-dash7" width="20" height="20"
                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                    <path
                                        d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM64 80c0-8.8 7.2-16 16-16h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16zm0 64c0-8.8 7.2-16 16-16h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16zm128 72c8.8 0 16 7.2 16 16v17.3c8.5 1.2 16.7 3.1 24.1 5.1c8.5 2.3 13.6 11 11.3 19.6s-11 13.6-19.6 11.3c-11.1-3-22-5.2-32.1-5.3c-8.4-.1-17.4 1.8-23.6 5.5c-5.7 3.4-8.1 7.3-8.1 12.8c0 3.7 1.3 6.5 7.3 10.1c6.9 4.1 16.6 7.1 29.2 10.9l.5 .1 0 0 0 0c11.3 3.4 25.3 7.6 36.3 14.6c12.1 7.6 22.4 19.7 22.7 38.2c.3 19.3-9.6 33.3-22.9 41.6c-7.7 4.8-16.4 7.6-25.1 9.1V440c0 8.8-7.2 16-16 16s-16-7.2-16-16V422.2c-11.2-2.1-21.7-5.7-30.9-8.9l0 0 0 0c-2.1-.7-4.2-1.4-6.2-2.1c-8.4-2.8-12.9-11.9-10.1-20.2s11.9-12.9 20.2-10.1c2.5 .8 4.8 1.6 7.1 2.4l0 0 0 0 0 0c13.6 4.6 24.6 8.4 36.3 8.7c9.1 .3 17.9-1.7 23.7-5.3c5.1-3.2 7.9-7.3 7.8-14c-.1-4.6-1.8-7.8-7.7-11.6c-6.8-4.3-16.5-7.4-29-11.2l-1.6-.5 0 0c-11-3.3-24.3-7.3-34.8-13.7c-12-7.2-22.6-18.9-22.7-37.3c-.1-19.4 10.8-32.8 23.8-40.5c7.5-4.4 15.8-7.2 24.1-8.7V232c0-8.8 7.2-16 16-16z" />
                                </svg>

                                <span class="ml-4">Invoice Settings @if (company()->plan != 1)
                                        <span class="badge text-bg-primary p-1">New</span>
                                    @endif
                                </span>
                                @if (company()->plan == 1)
                                    <span class="badge"><i class="fa-solid fa-crown text-warning"></i></span>
                                @endif
                            </a>
                        </li>
                        {{-- <li class="">
                            <a href="#return" class="collapsed {{ company()->plan ==1? 'no-collapsable' : '' }}" data-toggle="collapse" aria-expanded="false">
                                <svg class="svg-icon" id="p-dash6" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="4 14 10 14 10 20"></polyline>
                                    <polyline points="20 10 14 10 14 4"></polyline>
                                    <line x1="14" y1="10" x2="21" y2="3"></line>
                                    <line x1="3" y1="21" x2="10" y2="14"></line>
                                </svg>
                                <span class="ml-4">Returns</span>
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </a>
                            <ul id="return" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="">
                                    <a href="page-list-returns.html">
                                        <i class="fa-solid fa-minus"></i><span>List Returns</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="page-add-return.html">
                                        <i class="fa-solid fa-minus"></i><span>Add Return</span>
                                    </a>
                                </li>
                            </ul>
                        </li> --}}
                        <li
                            class="{{ Request::is('dashboard/customer*') || Request::is('dashboard/users*') || Request::is('dashboard/supplier*') ? 'active' : '' }}">
                            <a href="#people" class="collapsed {{ company()->plan == 1 ? 'no-collapsable' : '' }}"
                                data-toggle="collapse" aria-expanded="false">
                                <svg class="svg-icon" id="p-dash8" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <span class="ml-4">People</span>
                                @if (company()->plan == 1)
                                    <span class="badge"><i class="fa-solid fa-crown text-warning"></i></span>
                                @else
                                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="10 15 15 20 20 15"></polyline>
                                        <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                    </svg>
                                @endif
                            </a>
                            <ul id="people"
                                class="iq-submenu collapse {{ Request::is('dashboard/customer*') || Request::is('dashboard/users*') || Request::is('dashboard/supplier*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/customers') ? 'active' : '' }}">
                                    <a href="/dashboard/customers">
                                        <i class="fa-solid fa-minus"></i><span>Customers</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/customer/create') ? 'active' : '' }}">
                                    <a href="/dashboard/customer/create">
                                        <i class="fa-solid fa-minus"></i><span>Add Customers</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/partners') ? 'active' : '' }}">
                                    <a href="/dashboard/partners">
                                        <i class="fa-solid fa-minus"></i><span>Partners</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/partner/create') ? 'active' : '' }}">
                                    <a href="/dashboard/partner/create">
                                        <i class="fa-solid fa-minus"></i><span>Add Partner</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/users') ? 'active' : '' }}">
                                    <a href="/dashboard/users">
                                        <i class="fa-solid fa-minus"></i><span>Users</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/users/create') ? 'active' : '' }}">
                                    <a href="/dashboard/users/create">
                                        <i class="fa-solid fa-minus"></i><span>Add Users</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/suppliers') ? 'active' : '' }}">
                                    <a href="/dashboard/suppliers">
                                        <i class="fa-solid fa-minus"></i><span>Suppliers</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/suppliers/create') ? 'active' : '' }}">
                                    <a href="/dashboard/suppliers/create">
                                        <i class="fa-solid fa-minus"></i><span>Add Suppliers</span>
                                    </a>
                                </li>

                                <li class="{{ Request::is('dashboard/shippers') ? 'active' : '' }}">
                                    <a href="/dashboard/shippers">
                                        <i class="fa-solid fa-minus"></i><span>Shippers</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/shipper/create') ? 'active' : '' }}">
                                    <a href="/dashboard/shipper/create">
                                        <i class="fa-solid fa-minus"></i><span>Add Shippers</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ Request::is('dashboard/user/update') ? 'active' : '' }}">
                            <a href="/dashboard/user/update">
                                <svg class="svg-icon" id="p-dash10" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <polyline points="17 11 19 13 23 9"></polyline>
                                </svg>
                                <span class="ml-4">User Details</span>
                            </a>
                        </li>

                        <li class="{{ Request::is('dashboard/sms') ? 'active' : '' }}">
                            <a href="/dashboard/sms">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-icon"
                                    id="p-dash11" width="20" height="20" fill="currentColor">
                                    <path
                                        d="M88.2 309.1c9.8-18.3 6.8-40.8-7.5-55.8C59.4 230.9 48 204 48 176c0-63.5 63.8-128 160-128s160 64.5 160 128s-63.8 128-160 128c-13.1 0-25.8-1.3-37.8-3.6c-10.4-2-21.2-.6-30.7 4.2c-4.1 2.1-8.3 4.1-12.6 6c-16 7.2-32.9 13.5-49.9 18c2.8-4.6 5.4-9.1 7.9-13.6c1.1-1.9 2.2-3.9 3.2-5.9zM208 352c114.9 0 208-78.8 208-176S322.9 0 208 0S0 78.8 0 176c0 41.8 17.2 80.1 45.9 110.3c-.9 1.7-1.9 3.5-2.8 5.1c-10.3 18.4-22.3 36.5-36.6 52.1c-6.6 7-8.3 17.2-4.6 25.9C5.8 378.3 14.4 384 24 384c43 0 86.5-13.3 122.7-29.7c4.8-2.2 9.6-4.5 14.2-6.8c15.1 3 30.9 4.5 47.1 4.5zM432 480c16.2 0 31.9-1.6 47.1-4.5c4.6 2.3 9.4 4.6 14.2 6.8C529.5 498.7 573 512 616 512c9.6 0 18.2-5.7 22-14.5c3.8-8.8 2-19-4.6-25.9c-14.2-15.6-26.2-33.7-36.6-52.1c-.9-1.7-1.9-3.4-2.8-5.1C622.8 384.1 640 345.8 640 304c0-94.4-87.9-171.5-198.2-175.8c4.1 15.2 6.2 31.2 6.2 47.8l0 .6c87.2 6.7 144 67.5 144 127.4c0 28-11.4 54.9-32.7 77.2c-14.3 15-17.3 37.6-7.5 55.8c1.1 2 2.2 4 3.2 5.9c2.5 4.5 5.2 9 7.9 13.6c-17-4.5-33.9-10.7-49.9-18c-4.3-1.9-8.5-3.9-12.6-6c-9.5-4.8-20.3-6.2-30.7-4.2c-12.1 2.4-24.8 3.6-37.8 3.6c-61.7 0-110-26.5-136.8-62.3c-16 5.4-32.8 9.4-50 11.8C279 439.8 350 480 432 480z" />
                                </svg>
                                <span class="ml-4">Send SMS</span>
                            </a>
                        </li>

                        {{-- <li class="{{ Request::is('dashboard/request-features') ? 'active' : '' }}">
                            <a href="/dashboard/request-features">
                                <i class="fa-regular fa-circle-question"></i>
                                <span class="ml-3">Request Feature</span>
                            </a>
                        </li> --}}
                    </ul>

                    @if (company()->plan == 1)
                        <div id="sidebar-bottom" class=" sidebar-bottom">
                            <div class="card border-none">
                                <div class="card-body p-0">
                                    <div class="sidebarbottom-content">
                                        <div class="image"><img
                                                src="{{ asset('assets/assets/images/layouts/side-bkg.png') }}"
                                                class="img-fluid" alt="side-bkg"></div>
                                        <h6 class="mt-4 px-4 body-title">Get More Feature by Upgrading</h6>
                                        <a href="/pricing" class="btn sidebar-bottom-btn mt-4"><i
                                                class="fa-solid fa-crown"></i> Go Premium</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="p-3"></div>
                </nav>
            </div>
        </div>
        <div class="iq-top-navbar">
            <div class="iq-navbar-custom">
                <nav class="navbar navbar-expand-lg navbar-light p-0">
                    <div class="iq-navbar-logo d-flex align-items-center justify-content-between">
                        <i class="ri-menu-line wrapper-menu"></i>
                        <a href="/dashboard" class="header-logo">
                            {{-- <img src="{{ asset('assets/assets/images/logo.png') }}" class="img-fluid"
                                alt="logo"> --}}
                            {{-- <h5 class="logo-title ml-3"></h5> --}}

                        </a>
                    </div>
                    {{-- <div class="iq-search-bar device-search">
                        <form action="#" class="searchbox">
                            <a class="search-link" href="#"><i class="ri-search-line"></i></a>
                            <input type="text" class="text search-input" placeholder="Search here...">
                        </form>
                    </div> --}}
                    <div class="d-flex align-items-center">
                        {{-- <div class="change-mode">
                          <div class="custom-control custom-switch custom-switch-icon custom-control-inline">
                              <div class="custom-switch-inner">
                                  <p class="mb-0"> </p>
                                  <input type="checkbox" class="custom-control-input" id="dark-mode" data-active="true">
                                  <label class="custom-control-label" for="dark-mode" data-mode="toggle">
                                      <span class="switch-icon-left"><i class="a-left ri-moon-clear-line"></i></span>
                                      <span class="switch-icon-right"><i class="a-right ri-sun-line"></i></span>
                                  </label>
                              </div>
                          </div>
                      </div> --}}
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-label="Toggle navigation">
                            <i class="ri-menu-3-line"></i>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ml-auto navbar-list align-items-center">
                                {{-- <li class="nav-item nav-icon dropdown">
                                    <a href="#" class="search-toggle dropdown-toggle btn border add-btn"
                                        id="dropdownMenuButton02" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <img src="../assets/images/small/flag-01.png" alt="img-flag"
                                            class="img-fluid image-flag mr-2">En
                                    </a>
                                    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                        <div class="card shadow-none m-0">
                                            <div class="card-body p-3">
                                                <a class="iq-sub-card" href="#"><img
                                                        src="../assets/images/small/flag-02.png" alt="img-flag"
                                                        class="img-fluid mr-2">French</a>
                                                <a class="iq-sub-card" href="#"><img
                                                        src="../assets/images/small/flag-03.png" alt="img-flag"
                                                        class="img-fluid mr-2">Spanish</a>
                                                <a class="iq-sub-card" href="#"><img
                                                        src="../assets/images/small/flag-04.png" alt="img-flag"
                                                        class="img-fluid mr-2">Italian</a>
                                                <a class="iq-sub-card" href="#"><img
                                                        src="../assets/images/small/flag-05.png" alt="img-flag"
                                                        class="img-fluid mr-2">German</a>
                                                <a class="iq-sub-card" href="#"><img
                                                        src="../assets/images/small/flag-06.png" alt="img-flag"
                                                        class="img-fluid mr-2">Japanese</a>
                                            </div>
                                        </div>
                                    </div>
                                </li> --}}
                                {{-- <li>
                                    <a href="#" class="btn border add-btn shadow-none mx-2 d-none d-md-block"
                                        data-toggle="modal" data-target="#new-order"><i
                                            class="las la-plus mr-2"></i>New
                                        Order</a>
                                </li> --}}
                                {{-- <li class="nav-item nav-icon search-content">
                                    <a href="#" class="search-toggle rounded" id="dropdownSearch"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-search-line"></i>
                                    </a>
                                    <div class="iq-search-bar iq-sub-dropdown dropdown-menu"
                                        aria-labelledby="dropdownSearch">
                                        <form action="#" class="searchbox p-2">
                                            <div class="form-group mb-0 position-relative">
                                                <input type="text" class="text search-input font-size-12"
                                                    placeholder="type here to search...">
                                                <a href="#" class="search-link"><i
                                                        class="ri-search-line"></i></a>
                                            </div>
                                        </form>
                                    </div>
                                </li> --}}
                                <li class="nav-item nav-icon dropdown">
                                    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton2"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-mail">
                                            <path
                                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                            </path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                        <span class="bg-primary"></span>
                                    </a>
                                    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                        <div class="card shadow-none m-0">
                                            <div class="card-body p-0 ">
                                                <div class="cust-title p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <h5 class="mb-0">All Messages</h5>
                                                        <a class="badge badge-primary badge-card" href="#">0</a>
                                                    </div>
                                                </div>
                                                <div class="px-3 pt-0 pb-0 sub-card">
                                                    <div class="iq-sub-card text-center p-3">
                                                        <i>No new messages</i>
                                                    </div>
                                                    {{-- <a href="#" class="iq-sub-card">
                                                        <div class="media align-items-center cust-card py-3">
                                                            <div class="">
                                                                <img class="avatar-50 rounded-small"
                                                                    src="{{ asset('assets/assets/images/user/01.jpg') }}"
                                                                    alt="03">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <h6 class="mb-0">Kianna Carder</h6>
                                                                    <small class="text-dark"><b>11 : 21 pm</b></small>
                                                                </div>
                                                                <small class="mb-0">Lorem ipsum dolor sit
                                                                    amet</small>
                                                            </div>
                                                        </div>
                                                    </a> --}}
                                                </div>
                                                <a class="right-ic btn btn-primary btn-block position-relative p-2 disabled"
                                                    href="" role="button">
                                                    View All
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="nav-item nav-icon dropdown">
                                    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-bell">
                                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                        </svg>
                                        <span class="bg-primary "></span>
                                    </a>
                                    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                        <div class="card shadow-none m-0">
                                            <div class="card-body p-0 ">
                                                <div class="cust-title p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <h5 class="mb-0">All Notifications</h5>
                                                        <a class="badge badge-primary badge-card" href="#">0</a>
                                                    </div>
                                                </div>
                                                <div class="px-3 pt-0 pb-0 sub-card">
                                                    <div class="iq-sub-card text-center p-3">
                                                        <i>No new notifications</i>
                                                    </div>
                                                    {{-- <a href="#" class="iq-sub-card">
                                                        <div class="media align-items-center cust-card py-3">
                                                            <div class="">
                                                                <img class="avatar-50 rounded-small"
                                                                    src="{{ asset('assets/assets/images/user/01.jpg') }}"
                                                                    alt="03">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <h6 class="mb-0">Kianna Carder</h6>
                                                                    <small class="text-dark"><b>11 : 21 pm</b></small>
                                                                </div>
                                                                <small class="mb-0">Lorem ipsum dolor sit
                                                                    amet</small>
                                                            </div>
                                                        </div>
                                                    </a> --}}
                                                </div>
                                                <a class="right-ic btn btn-primary btn-block position-relative p-2 disabled"
                                                    href="" role="button">
                                                    View All
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="nav-item nav-icon dropdown caption-content">
                                    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton4"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <img src="{{ profileImage(userData()->profile) }}" class="img-fluid rounded"
                                            alt="user">
                                    </a>
                                    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <div class="card shadow-none m-0">
                                            <div class="card-body p-0 text-center">
                                                <div class="media-body profile-detail text-center">
                                                    <img src="{{ asset('assets/assets/images/page-img/profile-bg.jpg') }}"
                                                        alt="profile-bg" class="rounded-top img-fluid mb-4">
                                                    <img src="{{ profileImage(userData()->profile) }}"
                                                        alt="profile-img"
                                                        class="rounded profile-img img-fluid avatar-70">
                                                </div>
                                                <div class="p-3">
                                                    <h5 class="mb-1">{{ Auth::user()->email }}</h5>
                                                    <p class="mb-0">Since
                                                        {{ date('d M Y', strtotime(Auth::user()->created_at)) }}</p>
                                                    <div class="d-flex align-items-center justify-content-center mt-3">
                                                        <a href="/dashboard/user/update"
                                                            class="btn border mr-2">Update Profile</a>
                                                        <a href="/account/logout" class="btn border">Sign Out</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        {{-- <div class="modal fade" id="new-order" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="popup text-left">
                            <h4 class="mb-3">New Order</h4>
                            <div class="content create-workform bg-body">
                                <div class="pb-3">
                                    <label class="mb-2">Email</label>
                                    <input type="text" class="form-control" placeholder="Enter Name or Email">
                                </div>
                                <div class="col-lg-12 mt-4">
                                    <div class="d-flex flex-wrap align-items-ceter justify-content-center">
                                        <div class="btn btn-primary mr-4" data-dismiss="modal">Cancel</div>
                                        <div class="btn btn-outline-primary" data-dismiss="modal">Create</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        @if (company()->plan == 1)
            <script>
                $(document).ready(function() {
                    $("#staticBackdrop").modal('show');
                });
            </script>

            <style>
                .custom-modal-body {
                    background-color: #5165ff;
                    border-color: #5165ff;
                }

                .intro-1 {
                    font-size: 20px
                }

                .close {
                    color: #fff
                }

                .close:hover {
                    color: #fff
                }

                .intro-2 {
                    font-size: 13px
                }

                .custom-btn-primary {
                    color: #5165ff !important;
                    background-color: #fffaff;
                    border-color: #fffaff;
                    padding: 12px;
                    font-weight: 700;
                    border-radius: 41px;
                    padding-right: 20px;
                    padding-left: 20px;
                }
            </style>

            <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body custom-modal-body">
                            <div class="text-right"> <i class="fa fa-close close"
                                    onclick="$('#staticBackdrop').modal('hide')" data-dismiss="modal"></i> </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-center mt-2"> <img src="https://i.imgur.com/zZUiqsU.png"
                                            width="200">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-white mt-4">
                                        <span class="intro-1">Premium Cloud POS</span>
                                        <div class="mt-2"> <span class="intro-2">Gain access to lot more features
                                                with
                                                premium.
                                                Free accounts include temporary features with dashboard access limited
                                                to
                                                current
                                                day's data management</span> </div>
                                        <div class="mt-4 mb-5"> <a href="/pricing"
                                                class="btn btn-primary custom-btn-primary">Upgrade to
                                                premium
                                                <i class="fa-solid fa-crown"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @yield('dashboard')
    </div>

    <!-- Wrapper End-->
    <footer class="iq-footer">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item"><strong>Version: </strong>1.0.0</li>
                            </ul>
                        </div>
                        <div class="col-lg-6 text-right">
                            <span class="mr-1">
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> &copy;
                            </span> We Fix TV Panel Repair PVT LTD.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
