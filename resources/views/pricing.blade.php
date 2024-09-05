@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #f3f3f3 !important;
        }

        .contact-bg {
            height: 700px;
        }

        .addvert {
            display: none;
        }

        footer {
            padding-top: 100px
        }

        .hero-content h1 {
            margin-bottom: 130px
        }
    </style>

    <div class="pricing-plans contact-bg"
        style="background-image: url('{{ asset('assets/images/pricing/pricing-hero.png') }}')">
        <div class="hero-content">
            <h1>Affordable pricing plans <br> for your creative business</h1>
        </div>
    </div>

    <div class="pricing">
        <div class="container">
            <div class="heading mb-5">
                <h2>Price plans</h2>
            </div>
            <div class="pricing-wrap">
                <div class="basic" style="border-radius: 10px 0 0 10px;">
                    <div class="plan_name">
                        <div class="plan-title">Basic</div>
                        <div class="price">Free</div>
                    </div>
                    <div class="features">
                        <ul>
                            <li><i class="fa-solid fa-check"></i> POS</li>
                            <li><i class="fa-solid fa-check"></i> Unlimited products</li>
                            <li><i class="fa-solid fa-check"></i> Temporary products</li>
                            <li><i class="fa-solid fa-check"></i> Current day sales</li>
                            <li><i class="fa-solid fa-check"></i> Supports LKR, USD, GBP, EUR, INR</li>
                        </ul>

                        <div class="button">
                            {{-- <a href="pricing/free/?ref=get_started" class="primary-btn submit-btn border-only">Get started</a> --}}
                            <a href="/pricing/free/?ref=get_started" class="primary-btn submit-btn border-only">Get started</a>
                        </div>
                    </div>
                </div>

                <div class="basic premium">
                    <div class="top_title">Most popular
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <path
                                d="M14.5565 20.6281C14.6097 20.8416 14.796 21 15.016 21C15.238 21 15.4253 20.8385 15.478 20.6229C15.644 19.9435 15.9672 19.2124 16.4583 18.4297C17.5538 16.7008 19.6941 15.0042 21.6264 14.4565C21.8421 14.3953 22 14.2039 22 13.9797C22 13.7574 21.8447 13.5668 21.6305 13.5074C20.7555 13.2647 19.8963 12.8414 19.0651 12.25C17.2643 10.9758 15.9068 9.11587 15.4796 7.37835C15.4263 7.1616 15.2376 7 15.0144 7C14.7946 7 14.6077 7.15693 14.5536 7.36997C14.3417 8.20487 13.9497 9.01738 13.3776 9.84375C12.1599 11.6103 10.2966 12.9412 8.36486 13.5024C8.1523 13.5641 8 13.7551 8 13.9765C8 14.2027 8.15899 14.3961 8.37698 14.4565C9.29403 14.7105 10.2218 15.1884 11.1354 15.8776C13.0859 17.3405 14.1833 19.129 14.5565 20.6281Z"
                                fill="white" />
                            <path
                                d="M6.74659 10.7875C6.77696 10.9095 6.88343 11 7.00916 11C7.136 11 7.24303 10.9077 7.27315 10.7845C7.36803 10.3963 7.55268 9.97854 7.83333 9.53125C8.45934 8.54334 9.68234 7.5738 10.7865 7.26084C10.9097 7.22591 11 7.11649 11 6.98841C11 6.86139 10.9112 6.75247 10.7888 6.71851C10.2889 6.5798 9.79788 6.33795 9.32292 6C8.29388 5.27191 7.51817 4.20907 7.27403 3.2162C7.24358 3.09234 7.13577 3 7.00822 3C6.88262 3 6.77581 3.08967 6.74492 3.21141C6.62383 3.68849 6.39983 4.15279 6.07292 4.625C5.37706 5.63448 4.31236 6.39498 3.20849 6.71564C3.08703 6.75092 3 6.86006 3 6.98655C3 7.11581 3.09085 7.22636 3.21542 7.26086C3.73945 7.40602 4.26958 7.67906 4.79167 8.07292C5.90622 8.90883 6.53334 9.93087 6.74659 10.7875Z"
                                fill="white" />
                        </svg>
                    </div>
                    <div class="plan_name">
                        <div class="plan-title">Premium</div>
                        <div class="price">LKR 2898 <span>/month</span></div>
                    </div>
                    <div class="features">
                        <ul>
                            <li><i class="fa-solid fa-check"></i> Permanent products & categories</li>
                            <li><i class="fa-solid fa-check"></i> Unlimited products</li>
                            <li><i class="fa-solid fa-check"></i> Permanent customer management</li>
                            <li><i class="fa-solid fa-check"></i> Sales reports & statements management</li>
                            <li><i class="fa-solid fa-check"></i> Employee management</li>
                            <li><i class="fa-solid fa-check"></i> Multiple cashier management</li>
                            <li><i class="fa-solid fa-check"></i> Credit management</li>
                            <li><i class="fa-solid fa-check"></i> Supports LKR, USD, GBP, EUR, INR</li>
                        </ul>

                        <div class="button">
                            <a href="/pricing/premium/?ref=get_started" class="primary-btn submit-btn">Get started</a>
                            {{-- <a href="/contact" class="primary-btn submit-btn">Get started</a> --}}
                        </div>
                    </div>
                </div>

                <div class="basic" style="border-radius: 0 10px 10px 0;">
                    <div class="plan_name">
                        <div class="plan-title">Watermark-Free</div>
                        <div class="price">LKR 3000 <span>/month</span></div>
                    </div>
                    <div class="features">
                        <ul>
                            <li><i class="fa-solid fa-check"></i> Includes all premium features</li>
                            <li><i class="fa-solid fa-check"></i> Unlimited products</li>
                            <li><i class="fa-solid fa-check"></i> Supports LKR, USD, GBP, EUR, INR</li>
                            <li><i class="fa-solid fa-check"></i> NMSware watermark-free in all bills</li>
                        </ul>

                        <div class="button">
                            <a href="/pricing/watermark-free/?ref=get_started" class="primary-btn submit-btn border-only">Get started</a>
                            {{-- <a href="/contact" class="primary-btn submit-btn border-only">Get started</a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="faq">
        <div class="heading">
            <h2>Frequently asked questions</h2>
            <p class="text-center">Can't find the answer you're looking for ? <a href="/contact">Reach out to customer support team.</a></p>
        </div>

        <div class="container">
            <div class="q_a">
                <div class="row row-cols-1 mt-3">
                    <div class="col">
                        <a class="" data-bs-toggle="collapse" href="#qa1" role="button" aria-expanded="false"
                            aria-controls="qa1">
                            Do you provide computers and systems ? <i class="fa-solid fa-angle-down"></i>
                        </a>
                        <div class="collapse" id="qa1">
                            <div class="card card-body">
                                <p>Yes, we do provide computers and systems within Sri Lanka. To get your quotations, <a
                                        class="d-inline" href="/contact">Contact us now</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <a class="" data-bs-toggle="collapse" href="#qa2" role="button" aria-expanded="false"
                            aria-controls="qa2">
                            Do you accept bank deposits as payment method ? <i class="fa-solid fa-angle-down"></i>
                        </a>
                        <div class="collapse" id="qa2">
                            <div class="card card-body">
                                <p>Yes, in order to pay via bank transfer, contact us via <a
                                        href="https://wa.me/94766673957" class="d-inline">WhatsApp</a> or <a href="/contact"
                                        class="d-inline">send us a message</a>, we'll get back to you as soon as possible.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <a class="" data-bs-toggle="collapse" href="#qa3" role="button" aria-expanded="false"
                            aria-controls="qa3">
                            Can I later switch plans to upgrade or downgrade? <i class="fa-solid fa-angle-down"></i>
                        </a>
                        <div class="collapse" id="qa3">
                            <div class="card card-body">
                                <p>Yes, you can upgrade or downgrade later by just contacting via <a
                                        href="https://wa.me/94766673957" class="d-inline">WhatsApp</a> or <a href="/contact"
                                        class="d-inline">sending us a message</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <a class="" data-bs-toggle="collapse" href="#qa4" role="button" aria-expanded="false"
                            aria-controls="qa4">
                            Do you charge any setup fees? <i class="fa-solid fa-angle-down"></i>
                        </a>
                        <div class="collapse" id="qa4">
                            <div class="card card-body">
                                <p><strong>No</strong>, We don't charge any setup fees.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
