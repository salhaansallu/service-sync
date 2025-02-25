<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>We Fix CRM System - Partner Portal</title>
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

<body class="">
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
                        <li class="{{ Request::is('partner-portal') ? 'active' : '' }}">
                            <a href="/partner-portal" class="svg-icon">
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
                        <li class="{{ Request::is('/partner-portal/repairs*') ? 'active' : '' }}">
                            <a href="#category" class="collapsed"
                                data-toggle="collapse" aria-expanded="false">
                                <svg class="svg-icon" id="p-dash7" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="none"
                                    stroke="currentColor" stroke-width="30" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M352 320c88.4 0 160-71.6 160-160c0-15.3-2.2-30.1-6.2-44.2c-3.1-10.8-16.4-13.2-24.3-5.3l-76.8 76.8c-3 3-7.1 4.7-11.3 4.7L336 192c-8.8 0-16-7.2-16-16l0-57.4c0-4.2 1.7-8.3 4.7-11.3l76.8-76.8c7.9-7.9 5.4-21.2-5.3-24.3C382.1 2.2 367.3 0 352 0C263.6 0 192 71.6 192 160c0 19.1 3.4 37.5 9.5 54.5L19.9 396.1C7.2 408.8 0 426.1 0 444.1C0 481.6 30.4 512 67.9 512c18 0 35.3-7.2 48-19.9L297.5 310.5c17 6.2 35.4 9.5 54.5 9.5zM80 408a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                </svg>
                                <span class="ml-4">Repairs</span>
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </a>
                            <ul id="category"
                                class="iq-submenu collapse {{ Request::is('/partner-portal/repairs*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('/partner-portal/repairs?status=pending') ? 'active' : '' }}">
                                    <a href="/partner-portal/repairs?status=pending">
                                        <i class="fa-solid fa-minus"></i><span>Pending Repairs</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('/dashboard/repairs?status=repaired') ? 'active' : '' }}">
                                    <a href="/partner-portal/repairs?status=repaired">
                                        <i class="fa-solid fa-minus"></i><span>Finished Repairs</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('/dashboard/repairs?status=delivered') ? 'active' : '' }}">
                                    <a href="/partner-portal/repairs?status=delivered">
                                        <i class="fa-solid fa-minus"></i><span>Delivered Repairs</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('/dashboard/repairs?status=return') ? 'active' : '' }}">
                                    <a href="/partner-portal/repairs?status=return">
                                        <i class="fa-solid fa-minus"></i><span>Returned Repairs</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('/dashboard/repairs?status=customer-pending') ? 'active' : '' }}">
                                    <a href="/partner-portal/repairs?status=customer-pending">
                                        <i class="fa-solid fa-minus"></i><span>Customer Pending Repairs</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('/dashboard/repairs?status=awaiting-parts') ? 'active' : '' }}">
                                    <a href="/partner-portal/repairs?status=awaiting-parts">
                                        <i class="fa-solid fa-minus"></i><span>Awaiting Parts Repairs</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
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
                                        <img src="{{ profileImage(partner()->logo) }}" class="img-fluid rounded"
                                            alt="user">
                                    </a>
                                    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <div class="card shadow-none m-0">
                                            <div class="card-body p-0 text-center">
                                                <form action="" class="d-none" enctype="multipart/form-data" id="partnerLogo" method="post">
                                                    <input type="file" name="logo" id="ParterLogoImage">
                                                </form>
                                                <div class="media-body profile-detail text-center">
                                                    <img src="{{ asset('assets/assets/images/page-img/profile-bg.jpg') }}" alt="profile-bg" class="rounded-top img-fluid mb-4">
                                                    <label for="ParterLogoImage" style="cursor: pointer;"><img src="{{ profileImage(partner()->logo) }}" alt="profile-img" class="rounded profile-img img-fluid avatar-70"></label>
                                                </div>
                                                <div class="p-3">
                                                    <h5 class="mb-1">{{ partner()->email }}</h5>
                                                    <p class="mb-0">Since {{ date('d M Y', strtotime(partner()->created_at)) }}</p>
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
                            </span> We Fix TV Panel Repair PVT LTD. Developed by <a href="https://nmsware.com">NMSware
                                Technologies</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        $("#ParterLogoImage").change(function(e) {
            e.preventDefault();

            // if (document.getElementById("product_image").value != "" && !['png', 'jpeg', 'jpg'].includes(checkFileExtension('product_image'))) {
            //     return toastr.error("Please select 'png', 'jpeg', or 'jpg' image", 'Error');
            // }

            var formData = new FormData();
            var inputValue = $('#ParterLogoImage')[0].files[0]; // Get input field value
            formData.append('logo', inputValue);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                type: "post",
                url: '/partner-portal/logo-update',
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,

                success: function(response) {
                    if (response.error == 0) {
                        toastr.success(response.msg, 'Success');
                        setInterval(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.msg, 'Error');
                    }
                }
            });
        });
    </script>

</body>

</html>
