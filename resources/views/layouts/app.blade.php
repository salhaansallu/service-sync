<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#52bbdd">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>NMSware Technologies, CRM System</title>
        <meta name=”robots” content="noindex">
        <meta name="description" content="Project by NMSware Technologies PVT LTD">
        <meta property="og:title" content="Project by NMSware Technologies PVT LTD, Delivering World-Class IT Services | nmsware.com" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="https://nmsware.com/assets/images/brand/logo-grey-bg-og.jpg" />
        <meta property="og:url" content="{{ Request::url() }}" />

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/images/brand/favicon3.ico') }}" type="image/x-icon">

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @if (Request::is('customer-copy/*'))
        <!-- CSS -->
        <link rel="stylesheet" href="{{ asset('assets/assets/css/backend-plugin.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/assets/css/backende209.css?v=1.0.0') }}">

        <!-- Scripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        @vite(['resources/views/pos/sass/app.scss'])
        <script src="{{ asset('assets/assets/js/backend-bundle.min.js') }}"></script>
        <script src="{{ asset('assets/assets/js/table-treeview.js') }}"></script>
        <script src="{{ asset('assets/assets/js/customizer.js') }}"></script>
        <script async src="{{ asset('assets/assets/js/chart-custom.js') }}"></script>
        <script src="{{ asset('assets/assets/js/app.js') }}"></script>
    @endif
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>

<body>
    @isset($errormsg)
        <script>
            toastr.error("{{ $errormsg }}", "Error");
        </script>
    @endisset
    <div id="app">
        <nav
            class="navigation {{ Request::is('about') || Request::is('create-account') || Request::is('account/*') || Request::is('pricing') || Request::is('customer-copy/*') ? 'light' : '' }}">
            <div class="innernav hero-container">
                <div class="brand">
                    <a href="/"><img src="{{ asset('assets/images/brand/logo.png') }}"
                            alt="NMSware Technologies logo"></a>
                </div>
                <div class="nav-links">
                    <div class="mobile-menu">
                        <i class="open_menu fa-solid fa-bars"></i>
                        <i id="menu_close" class="menu_close fa-solid fa-times hide"></i>
                    </div>
                    <ul class="menulist">
                        <li><a href="/">Home</a></li>
                        <li><a href="/pricing">Pricing</a></li>
                        <li><a href="javascript:void(0)">Company</a>
                            <ul class="openonhover">
                                <li>
                                    <ul>
                                        <li><a href="/about">About us</a></li>
                                        {{-- <li><a href="">Partners</a></li> --}}
                                        <li><a href="{{ Request::is('/') ? '#services' : '/#services' }}">Services</a>
                                        </li>
                                        <li><a
                                                href="{{ Request::is('/about') ? '#projects' : '/about/#projects' }}">Projects</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <img src="{{ asset('assets/images/brand/logo.png') }}"
                                        alt="NMSware Technologies logo">
                                </li>
                            </ul>
                        </li>
                        <li><a href="/contact">Contact us</a></li>
                        @guest
                            <li><a href="/signin">Sign in</a></li>
                        @else
                            <li><a href="/account/overview">Account</a></li>
                        @endguest

                        @if (hasDashboard())
                            <li><a href="/account/overview" class="primary-btn border"
                                    style="color: white !important;">Dashboard</a></li>
                        @else
                            <li><a href="/signin?ref=get_started" class="primary-btn border"
                                    style="color: white !important;">Get Started</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>

        <div class="container">
            <div class="addvert">
                <div class="row">
                    <div class="col col-lg-7">
                        <h1>Try Cloud POS For <strong>Free</strong></h1>
                        <div class="subfeature">
                            <span><i class="fa-solid fa-check"></i> Free plan</span> <span><i
                                    class="fa-solid fa-check"></i> Multi device support</span> <span><i
                                    class="fa-solid fa-check"></i> For all businesses</span>
                        </div>
                    </div>
                    <div class="col col-lg-5">
                        <a href="/signin?ref=get_started" class="dark-btn">Try now</a>
                        <a href="/signin">Sign in</a>
                    </div>
                </div>
            </div>
        </div>

        <footer>
            <div class="inner">
                <div class="container">
                    <div class="footer_brand">
                        <img src="{{ asset('assets/images/brand/logo-white.png') }}" alt="">
                    </div>

                    <div class="row row-cols-1 row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2">
                        <div class="col">
                            <div class="footer_head">Get Started</div>
                            <ul>
                                <li><a href="/signin?ref=get_started">Try cloud POS</a></li>
                                @guest
                                    <li><a href="/signin">Sign in</a></li>
                                @else
                                    <li><a href="/account/overview">Account</a></li>
                                @endguest
                                <li><a href="/contact">Get quotations</a></li>
                                <li><a href="/pricing">See pricing</a></li>
                            </ul>
                        </div>

                        <div class="col">
                            <div class="footer_head">Company</div>
                            <ul>
                                <li><a href="/about">About us</a></li>
                                {{-- <li><a href="">Partners</a></li> --}}
                                <li><a href="{{ Request::is('/') ? '#services' : '/#services' }}">Services</a></li>
                                <li><a
                                        href="{{ Request::is('/about') ? '#projects' : '/about/#projects' }}">Projects</a>
                                </li>
                                <li><a href="/privacy-policy">Privacy Policy</a></li>
                            </ul>
                        </div>

                        <div class="col">
                            <div class="footer_head">Social media</div>
                            <ul>
                                {{-- <li><a href=""><i class="fa-brands fa-instagram"></i> Instagram</a></li> --}}
                                <li><a href="https://www.facebook.com/nmsware/"><i
                                            class="fa-brands fa-facebook-f"></i>
                                        Facebook</a></li>
                                <li><a href="https://www.instagram.com/nmsware_official/"><i
                                            class="fa-brands fa-instagram"></i>
                                        Instagram</a></li>
                                <li><a href="https://linkedin.com/company/nmsware/"><i
                                            class="fa-brands fa-linkedin-in"></i> Linkedin</a></li>
                                <li><a href="https://wa.me/94741959701"><i class="fa-brands fa-whatsapp"></i>
                                        Whatsapp</a></li>
                            </ul>
                        </div>

                        <div class="col">
                            <div class="footer_head">Contact</div>
                            <ul>
                                <li><a href="tel:+94741959701"><i class="fa-solid fa-phone"></i> +94 74 195 9701</a>
                                </li>
                                <li><a href="mailto:info@nmsware.com"><i class="fa-solid fa-envelope"></i>
                                        info@nmsware.com</a></li>
                            </ul>
                        </div>

                        <div class="col">
                            <div class="footer_head">Newsletter</div>
                            <p>Subscribe and be up-to-date</p>
                            <form action="" id="newsletterform" onsubmit="return false;">
                                <input type="email" placeholder="Enter email address" id="newsletter_email"
                                    required>
                                <button type="submit">Subscribe</button>
                            </form>
                            <script>
                                $(document).ready(function() {
                                    $("#newsletterform").submit(function(e) {
                                        e.preventDefault();
                                        if ($("#newsletter_email").val() != "") {
                                            $.ajax({
                                                type: "post",
                                                url: "/newsletter",
                                                data: {
                                                    email: $("#newsletter_email").val(),
                                                    _token: '{{ csrf_token() }}'
                                                },
                                                dataType: "json",
                                                success: function(newsres) {
                                                    if (newsres.error == 0) {
                                                        toastr.success(newsres.msg, "Success");
                                                        $("#newsletter_email").val("");
                                                    } else {
                                                        toastr.error(newsres.msg, "Success");
                                                    }
                                                }
                                            });
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>

                    <div class="copyrights">
                        Copyright &copy; {{ date('Y') }} NMSware Technologies. All Rights Received
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
