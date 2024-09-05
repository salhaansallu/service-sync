@extends('layouts.app')

@section('content')
    <div class="hero hero-container"
        style="background-image: url('{{ asset('assets/images/hero/about-hero.png') }}') !important; background-size: cover !important; background-position: left !important;">
        <div class="hero-content">
            <div class="row row-cols-2 {{ Request::is('about') ? 'about-hero' : '' }}">
                <div class="col">
                    <h1>Unlocking <span>Digital Potential,</span> <br>One Solution at a Time</h1>
                    <p class="text-dark">
                        Step into the future with us. Our mission goes beyond conventional IT solutions;
                        we're architects of digital evolution. With a commitment to excellence, we merge expertise and
                        creativity to shape a dynamic, tech-driven tomorrow. Explore innovation; embrace transformation.
                    </p>
                    <a href="/signin?ref=get_started" class="primary-btn">Get started for free</a>
                </div>
                <div class="col">
                    <img src="{{ asset('assets/images/hero/about-img.png') }}" alt="hero sales image">
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="heading mt-5 pt-5">
            <h2>About <span>Us</span></h2>
            <p class="text-center">Get to know about us</p>
        </div>
    </div>

    <div class="services mt-0">
        <div class="container">
            <div class="row row-cols-2">
                <div class="col text-sm-center text-xs-center">
                    <img src="{{ asset('assets/images/brand/about-image.png') }}"
                        alt="NMSware Tehnologies Cloud POS Service">
                </div>
                <div class="col">
                    <div class="heading">
                        <h3><span>NMSware Technologies</span></h3>
                        <p>
                            Welcome to our realm of innovation! At NMSware Technologies, we're not just providing IT
                            solutions; we're crafting digital experiences that redefine possibilities. With a team
                            passionate about technology, we navigate complexities to deliver solutions that transcend
                            expectations. Explore a world where your IT challenges find ingenious resolutions.
                        </p>
                    </div>

                    <h5>
                        What we do
                    </h5>
                    <ul>
                        <li>Cloud POS</li>
                        <li>Custom software & web application development</li>
                        <li>Graphics designing</li>
                        <li>IT Consultation</li>
                        <li></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="vision">
        <div class="container">
            <div class="row row-cols-1 row-cols-xl-2 row-cols-lg-2 row-cols-md-2">
                <div class="col">
                    <div class="heading">
                        <h3>Our <span>Mission</span></h3>
                    </div>
                    <div class="description">
                        <i class="fa-solid fa-quote-left"></i>
                        <b>Fostering a digital transformation that propels businesses into a future of streamlined
                            operations, superior customer engagement, and enduring growth.</b>
                        <i class="fa-solid fa-quote-right"></i>
                    </div>
                </div>

                <div class="col">
                    <div class="heading">
                        <h3>Our <span>Vision</span></h3>
                    </div>
                    <div class="description">
                        <i class="fa-solid fa-quote-left"></i>
                        <b>To lead the evolution of business operations and empowering businesses for a seamless digital
                            future.</b>
                        <i class="fa-solid fa-quote-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="growwithus bg-grey">
        <div class="heading">
            <h3 class="text-center">Grow Up Your <span>Business</span> With Us</h3>
            <p class="text-center">Contact us now. Digitalize your business and maximize your income.</p>
        </div>

        <div class="container mt-5">
            <div class="row row-cols-3">
                <div class="col">
                    <div class="number">
                        50+
                    </div>
                    <div class="sub">Clients</div>
                </div>

                <div class="col">
                    <div class="number">
                        25+
                    </div>
                    <div class="sub">Projects</div>
                </div>

                <div class="col">
                    <div class="number">
                        5+
                    </div>
                    <div class="sub">Years of expertise</div>
                </div>
            </div>
        </div>
    </div>

    <div class="projects" id="projects">
        <div class="heading">
            <h2>Our <span>Projects</span></h2>
            <p class="text-center">Few of our completed projects</p>
        </div>

        <div class="container mt-5">
            <div class="row row-cols-3">
                <div class="col">
                    <div class="details"
                        style="background-image: url({{ asset('assets/images/projects/nihonjidosha.jpg') }});">
                        <div class="name">Nihon Jidosha</div>
                        <div class="duration">5 month ago</div>
                        <div class="description">
                            Revving up the digital presence! Proud to partner with Nihon
                            Jidosha to transform their online experience. Our web
                            development expertise has turbocharged their website, en...
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="details"
                        style="background-image: url({{ asset('assets/images/projects/pretzelplate.jpg') }});">
                        <div class="name">Pretzel Plate</div>
                        <div class="duration">1 month ago</div>
                        <div class="description">
                            Pretzel Plate just got a digital upgrade! Thrilled to sweeten the online presence of Pretzel Plate with our web development magic. Now you can explore their tempting treats and place...
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="details"
                        style="background-image: url({{ asset('assets/images/projects/winluckhk.jpg') }});">
                        <div class="name">Nihon Jidosha</div>
                        <div class="duration">Few weeks ago</div>
                        <div class="description">
                            Our recent collaboration with Winluck HK involved crafting a tailored web solution, enhancing their brand visibility, and optimizing operations...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
