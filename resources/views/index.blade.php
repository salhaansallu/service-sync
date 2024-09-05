@extends('layouts.app')

@section('content')
    <div class="hero hero-container" style="background-image: url('{{ asset('assets/images/hero/hero-bg.png') }}')">
        <div class="hero-content">
            <div class="row row-cols-2">
                <div class="col">
                    <h1>Unleashing Solutions, <br>Building <span>Digital Futures</span></h1>
                    <p>
                        We're not just creating IT solutions; we're architects of digital transformation. With a commitment to innovation, we strive to unleash a future where technology transforms possibilities into realities. Join us on this exciting journey toward a digital frontier, where we build the foundations of tomorrow's success.
                    </p>
                    <a href="/signin?ref=get_started" class="primary-btn">Get started for free</a>
                </div>
                <div class="col">
                    <img src="{{ asset('assets/images/hero/hero-image.png') }}" alt="hero sales image">
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="heading mt-5 pt-5">
            <h2>Our <span>Services</span></h2>
            <p class="text-center">We help digitalize your business in any way</p>
        </div>
    </div>

    <div class="services bg-grey" id="services">
        <div class="container">
            <div class="row row-cols-2">
                <div class="col text-sm-center text-xs-center">
                    <img src="{{ asset('assets/images/services/pos.png') }}" alt="NMSware Tehnologies Cloud POS Service">
                </div>
                <div class="col">
                    <div class="heading">
                        <h3>Cloud POS System</h3>
                        <p>
                            Revolutionize your business with our cutting-edge Free Cloud POS System services. We provide a seamless, secure, and efficient point-of-sale experience, empowering you to manage transactions, inventory, and customer interactions effortlessly in the cloud. Elevate your retail game with us!
                        </p>
                    </div>

                    <h5>
                        Features
                    </h5>
                    <ul>
                        <li>POS</li>
                        <li>Product Inventory</li>
                        <li>Customer Management</li>
                        <li>Sales Management</li>
                        <li>Cashier Management</li>
                        <li>etc...</li>
                        <li><a href="/signin?ref=get_started" class="primary-btn">Try now</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="services">
        <div class="container">
            <div class="row row-cols-2">
                <div class="col">
                    <div class="heading">
                        <h3>Software & Web Development</h3>
                        <p>
                            Elevate your digital presence with our top-notch Software & Web Development services. We craft bespoke solutions that align with your business goals, ensuring a seamless user experience and optimal functionality. Transform your ideas into impactful digital realities with us.
                        </p>
                    </div>

                    <h5>
                        Features
                    </h5>
                    <ul>
                        <li>Custom Software</li>
                        <li>Custom Web based Application</li>
                        <li>After Delivery Support</li>
                        <li>Consultation</li>
                        <li>etc...</li>
                        <li><a href="/contact" class="primary-btn">Contact us</a></li>
                    </ul>
                </div>
                <div class="col text-sm-center text-xs-center text-right">
                    <img src="{{ asset('assets/images/services/software.png') }}"
                        alt="NMSware Tehnologies Cloud POS Service">
                </div>
            </div>
        </div>
    </div>

    <div class="services bg-grey">
        <div class="container">
            <div class="row row-cols-2">
                <div class="col text-sm-center text-xs-center">
                    <img src="{{ asset('assets/images/services/graphic_design.png') }}"
                        alt="NMSware Tehnologies Cloud POS Service">
                </div>
                <div class="col">
                    <div class="heading">
                        <h3>Graphic Design</h3>
                        <p>
                            Unleash creativity with our Graphic Design services. We bring ideas to life through stunning visuals, capturing attention and conveying messages effectively. Elevate your brand with our innovative designs that make a lasting impact in the digital landscape.
                        </p>
                    </div>

                    <h5>
                        Features
                    </h5>
                    <ul>
                        <li>Custom Design</li>
                        <li>Logo Design</li>
                        <li>Banner Design</li>
                        <li>Flyer Design</li>
                        <li>Business Card Design</li>
                        <li><a href="/contact" class="primary-btn">Contact us</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="whyus" style="margin-bottom: 170px">
        <div class="container">
            <div class="heading mt-5 pt-5">
                <h2>Why <span>NMSware Technologies</span></h2>
                <p class="text-center">See why our clients choose us</p>
            </div>

            <div class="features">
                <div class="feature">
                    <div class="icon">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div class="title"><b>Professional Staff</b></div>
                    <div class="des text-center">
                        Our dedicated team of professionals brings expertise honed through rigorous training. With a commitment
                    </div>
                </div>
                <div class="feature">
                    <div class="icon">
                        <i class="fa-solid fa-pen-ruler"></i>
                    </div>
                    <div class="title"><b>Tailored Approach</b></div>
                    <div class="des text-center">
                        Receive customized solutions designed to meet your unique business needs.
                    </div>
                </div>
                <div class="feature">
                    <div class="icon">
                        <i class="fa-solid fa-microchip"></i>
                    </div>
                    <div class="title"><b>Cutting-edge Tech</b></div>
                    <div class="des text-center">
                        Implement the latest technologies for enhanced efficiency and performance.
                    </div>
                </div>
                <div class="feature">
                    <div class="icon">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="title"><b>User-Centric Design</b></div>
                    <div class="des text-center">
                        Ensure a seamless and engaging experience for end-users across all services.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="testimonials bg-grey">
        <div class="container">
            <div class="row">
                <div class="col-3">
                    <div class="heading">
                        <h3><span>Client Testimonials</span></h3>
                        <p>See what our customers think about us</p>
                    </div>
                </div>
                <div class="col-9">
                    <div class="testimonial-wrap">
                        <div class="testimonial text-center">
                            <div class="profile">
                                <img src="{{ asset('assets/images/testimonial/user.svg') }}" alt="">
                            </div>
                            <div class="author"><b>John Doe</b></div>
                            <div class="saying">
                                <i class="fa-solid fa-quote-left"></i>
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro nam doloremque,
                                <i class="fa-solid fa-quote-right"></i>
                            </div>
                        </div>

                        <div class="testimonial text-center">
                            <div class="profile">
                                <img src="{{ asset('assets/images/testimonial/user.svg') }}" alt="">
                            </div>
                            <div class="author"><b>John Doe</b></div>
                            <div class="saying">
                                <i class="fa-solid fa-quote-left"></i>
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro nam doloremque,
                                <i class="fa-solid fa-quote-right"></i>
                            </div>
                        </div>

                        <div class="testimonial text-center">
                            <div class="profile">
                                <img src="{{ asset('assets/images/testimonial/user.svg') }}" alt="">
                            </div>
                            <div class="author"><b>John Doe</b></div>
                            <div class="saying">
                                <i class="fa-solid fa-quote-left"></i>
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro nam doloremque,
                                <i class="fa-solid fa-quote-right"></i>
                            </div>
                        </div>

                        <div class="testimonial text-center">
                            <div class="profile">
                                <img src="{{ asset('assets/images/testimonial/user.svg') }}" alt="">
                            </div>
                            <div class="author"><b>John Doe</b></div>
                            <div class="saying">
                                <i class="fa-solid fa-quote-left"></i>
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Porro nam doloremque,
                                <i class="fa-solid fa-quote-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@endsection
