@extends('frontend.layouts.master')

@section('css')
    <link rel="stylesheet" href="main/css/owl.carousel.min.css">
    <link rel="stylesheet" href="main/css/owl.theme.default.min.css">

    <link rel="stylesheet" href="main/css/about-us.css">
    <link rel="stylesheet" href="{{ asset('main/css/dashboard.css') }}">
@endsection

@section('content')
    <!-- Banner Section -->
    <section class="banner price-banner">
        <div class="overlay"></div>
        <div class="banner-content">
            <h1 class="display-4 fw-bold">Price</h1>

        </div>
    </section>

    <!-- Price Section -->
    <section class="price">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <!-- Pricing Section -->
                    <div class="owl-carousel owl-theme text-center owl-custom">
                        @foreach ($packages as $package)
                            <div class="item">
                                <div class="card border-0 shadow">

                                    <div class="card-header bg-dark text-white rounded-top">RECOMMENDED</div>

                                    <div class="card-body">
                                        <h2 class="card-title h4">{{ $package->package_name }}</h2>
                                        <p class="card-text text-muted">{{ strip_tags($package->description) }}</p>

                                        <p class="display-6 fw-bold">₹{{ number_format($package->price, 2) }}</p>
                                        @if ($package->discount_price)
                                            <p class="text-muted">Discount Price:
                                                ₹{{ number_format($package->discount_price, 2) }}</p>
                                        @endif

                                        <!-- Simplified Days/Months Calculation -->
                                        @if ($package->days)
                                            <p class="text-muted">
                                                Duration:
                                                @if ($package->days < 30)
                                                    {{ $package->days }} days
                                                @else
                                                    {{ round($package->days / 30) }} months
                                                @endif
                                            </p>
                                        @endif

                                        <button class="btn btn-outline-theme">Get it now</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>


                </div>
            </div>
        </div>
    </section>

    <!-- About end Section -->

    <!-- about cards -->
    <section>
        <div class="about-cards">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-md-7 mx-auto">
                        <h2 class="text-capitalize text-center">Our mission
                        </h2>
                        <p class="desc text-center">
                            At STAFO, our mission is to empower businesses with intuitive, technology-driven solutions that
                            enhance efficiency and optimize workforce management. We aim to provide organizations with
                            seamless tools that eliminate manual tasks, reduce errors, and ensure a smooth workflow across
                            all levels of employee administration..
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <img src="{{ asset('main/images/card-sm-img.webp') }}" class="card-img-top"
                                alt="Intuitive management">
                            <div class="card-body">
                                <h5 class="card-title">Our Vision
                                </h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the bulk
                                    of the card's content.</p>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <img src="{{ asset('main/images/card-sol.webp') }}" class="card-img-top" alt="card-img">
                            <div class="card-body">
                                <h5 class="card-title">Deliver valuable solution
                                </h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the bulk
                                    of the card's content.</p>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <img src="{{ asset('main/images/card-bus.webp') }}" class="card-img-top"
                                alt="Trust in local businesses">
                            <div class="card-body">
                                <h5 class="card-title">Trust in local businesses
                                </h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the bulk
                                    of the card's content.</p>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <img src="{{ asset('main/images/card-manage.webp') }}" class="card-img-top" alt="card-img">
                            <div class="card-body">
                                <h5 class="card-title">Intuitive management
                                </h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the bulk
                                    of the card's content.</p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <!-- AOS (Animate On Scroll) -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script> -->
    <script src="main/js/owl.carousel.min.js"></script>


    <!-- <script>
            // Initialize AOS animations
            AOS.init({
                duration: 1000,
                once: true,
            });
        </script> -->
    <script>
        $(document).ready(function () {
            $(".owl-carousel").owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    1000: {
                        items: 3
                    }
                }
            });
        });
    </script>
@endsection