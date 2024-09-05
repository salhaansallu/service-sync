<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#52bbdd">
    <link rel="canonical" href="{{ str_replace('https://nmsware.com', 'https://www.nmsware.com', Request::url()) }}" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (Request::is('/'))
        <title>NMSware Technologies, IT solutions | nmsware.com</title>
        <meta name=”robots” content="index, follow">
        <meta name="keywords"
            content="Free Cloud Point of Sale, Free POS System, E-commerce Solutions, Inventory Management, Mobile POS, Business Analytics, Custom Software, Software Solutions, Application Development, Web Design, Content Management Systems, Website Maintenance, UI/UX Design, Graphic Design, IT service company, Software company, Web development, Web development company">
        <meta name="description"
            content="Step into the future with us. Our mission goes beyond conventional IT solutions; we're architects of digital evolution. With a commitment to excellence, we merge expertise and creativity to shape a dynamic, tech-driven tomorrow. Explore innovation; embrace transformation.">
        <meta property="og:title" content="NMSware Technologies, Delivering World-Class IT Services | nmsware.com" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="https://nmsware.com/assets/images/brand/logo-grey-bg-og.jpg" />
        <meta property="og:url" content="{{ Request::url() }}" />
    @elseif (Request::is('sri-lanka'))
        <title>NMSware Technologies, Delivering world-class IT services in Sri Lanka | nmsware.com</title>
        <meta name=”robots” content="index, follow">
        <meta name="keywords"
            content="Free Cloud Point of Sale sri lanka, Free POS System sri lanka, E-commerce Solutions sri lanka, Inventory Management, Mobile POS, Business Analytics, Custom Software, Software Solutions sri lanka, Application Development sri lanka, Web Design sri lanka, Content Management Systems, Website Maintenance, UI/UX Design sri lanka, Graphic Design sri lanka, IT service company sri lanaka, Software company sri lanaka, Web development, Web development company in sri lanka,">
        <meta name="description"
            content="In the heart of Sri Lanka, we empower businesses to compete on the global stage. Unleash the power of technology with Sri Lanka's premier IT partner for your business.">
        <meta property="og:title"
            content=">NMSware Technologies, Delivering world-class IT services in Sri Lanka | nmsware.com" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="https://nmsware.com/assets/images/brand/logo-grey-bg-og.jpg" />
        <meta property="og:url" content="{{ Request::url() }}" />
    @elseif (Request::is('about'))
        <title>NMSware Technologies, Empowering Sri Lankan businesses through technology | nmsware.com</title>
        <meta name=”robots” content="index, follow">
        <meta name="keywords"
            content="about nmsware technologies, about nmsware.com, about nmsware, Empowering Sri Lankan Businesses through Technology">
        <meta name="description"
            content="Step into the future with us. Our mission goes beyond conventional IT solutions; we're architects of digital evolution. With a commitment to excellence, we merge expertise and creativity to shape a dynamic, tech-driven tomorrow. Explore innovation; embrace transformation.">
        <meta property="og:title" content="About NMSware Technologies" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="https://nmsware.com/assets/images/brand/logo-grey-bg-og.jpg" />
        <meta property="og:url" content="{{ Request::url() }}" />
    @elseif (Request::is('signin'))
        <title>Sign in NMSware Technologies | nmsware.com</title>
        <meta name=”robots” content="index, follow">
        <meta name="keywords"
            content="sign in to nmsware technologies, sign in to nmsware.com, sign in to nmsware technologies sri lanka">
        <meta name="description" content="Sign in to NMSware Technologies. Access free cloud POS system.">
        <meta property="og:title" content="Sign in NMSware Technologies" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="https://nmsware.com/assets/images/brand/logo-grey-bg-og.jpg" />
        <meta property="og:url" content="{{ Request::url() }}" />
    @elseif (Request::is('signup'))
        <title>Register NMSware Technologies | nmsware.com</title>
        <meta name=”robots” content="index, follow">
        <meta name="keywords"
            content="Register to nmsware technologies, Register to nmsware.com, Register to nmsware, register for nmsware technologies sri lanka">
        <meta name="description" content="Register to NMSware Technologies. Access free cloud POS system.">
        <meta property="og:title" content="Register NMSware Technologies" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="https://nmsware.com/assets/images/brand/logo-grey-bg-og.jpg" />
        <meta property="og:url" content="{{ Request::url() }}" />
    @elseif (Request::is('pricing'))
        <title>Free Cloud POS System | nmsware.com</title>
        <meta name=”robots” content="index, follow">
        <meta name="keywords"
            content="free cloud pos, free cloud pos system pricing, free cloud pos sri lanka, free cloud point of sale in sri lanka, free cloud pos nmsware technologies">
        <meta name="description" content="NMSware Technologies Free Cloud POS System pricing.">
        <meta property="og:title" content="Free Cloud POS System" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="https://nmsware.com/assets/images/brand/logo-grey-bg-og.jpg" />
        <meta property="og:url" content="{{ Request::url() }}" />
    @elseif (Request::is('contact'))
        <title>Contact NMSware Technologies | nmsware.com</title>
        <meta name=”robots” content="index, follow">
        <meta name="keywords" content="contact nmsware technologies, contact nmsware.com">
        <meta name="description" content="Contact NMSware Technologies sales team.">
        <meta property="og:title" content="Contact NMSware Technologies" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="https://nmsware.com/assets/images/brand/logo-grey-bg-og.jpg" />
        <meta property="og:url" content="{{ Request::url() }}" />
    @elseif (Request::is('account/*'))
        <title>Account NMSware Technologies | nmsware.com</title>
        <meta name=”robots” content="noindex, nofollow">
    @elseif (Request::is('privacy-policy'))
        <title>Privacy Policy NMSware Technologies | nmsware.com</title>
        <meta name=”robots” content="noindex, nofollow">
    @elseif (Request::is('password/reset'))
        <title>Reset your password NMSware Technologies | nmsware.com</title>
        <meta name=”robots” content="noindex, nofollow">
    @else
        <title>NMSware Technologies | nmsware.com</title>
        <meta name=”robots” content="noindex, nofollow">
        <meta name="description"
            content="Step into the future with us. Our mission goes beyond conventional IT solutions; we're architects of digital evolution. With a commitment to excellence, we merge expertise and creativity to shape a dynamic, tech-driven tomorrow. Explore innovation; embrace transformation.">
        <meta property="og:image" content="https://nmsware.com/assets/images/brand/logo-grey-bg-og.jpg" />
        <meta property="og:url" content="{{ Request::url() }}" />
    @endif

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

    <script>
        (function(w, d, s, r, n) {
            w.TrustpilotObject = n;
            w[n] = w[n] || function() {
                (w[n].q = w[n].q || []).push(arguments)
            };
            a = d.createElement(s);
            a.async = 1;
            a.src = r;
            a.type = 'text/java' + s;
            f = d.getElementsByTagName(s)[0];
            f.parentNode.insertBefore(a, f)
        })(window, document, 'script', 'https://invitejs.trustpilot.com/tp.min.js', 'tp');
        tp('register', '5XmM11iEr2OILioa');
    </script>
</head>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-19EW0EWHFF"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-19EW0EWHFF');
</script>

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
