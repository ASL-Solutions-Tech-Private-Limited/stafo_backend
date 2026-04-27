<!DOCTYPE html>
<html lang="en">

<head>

  <!-- meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- SEO Meta Tags -->
  <title>@yield('title')</title>
  <meta name="description"
    content="Learn about STAFO - providing innovative HR management solutions with 24/7 support, 99% customer satisfaction, and trusted by 32+ companies across India. Experience our 15-day free trial." />
  <meta name="keywords"
    content="STAFO about us, HR software company, HRMS provider India, HR management solutions, HR technology company, employee management system" />

  <!-- Open Graph Meta Tags -->
  <meta property="og:title" content="About STAFO | Leading HRMS Solution Provider in India">
  <meta property="og:description"
    content="Discover STAFO's story - Empowering Indian businesses with innovative HR solutions. 24/7 support, 99% satisfaction rate, and trusted by 32+ companies.">
  <meta property="og:image" content="https://stafo.in/assets/images/icon/c_logo.png">
  <meta property="og:url" content="https://stafo.in/about-us.html">
  <meta property="og:type" content="website">

  <!-- Twitter Card Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="About STAFO | HR Solutions Provider">
  <meta name="twitter:description"
    content="Leading HRMS solution provider in India with 24/7 support and 99% customer satisfaction. Try our 15-day free trial.">
  <meta name="twitter:image" content="https://stafo.in/assets/images/icon/c_logo.png">

  <!-- Additional SEO Meta Tags -->
  <meta name="robots" content="index, follow">
  <meta name="author" content="STAFO">
  <meta name="revisit-after" content="7 days">
  <link rel="canonical" href="https://stafo.in">

  <!-- Favicon Icon -->
  <link rel="shortcut icon" href="{{asset('assets/images/favicon.png')}}" />

  <!-- inject css start -->

  <!--== bootstrap -->
  <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />

  <!--== animate -->
  <link href="{{asset('assets/css/animate.css')}}" rel="stylesheet" type="text/css" />

  <!--== line-awesome -->
  <link href="{{asset('assets/css/line-awesome.min.css')}}" rel="stylesheet" type="text/css" />

  <!--== magnific-popup -->
  <link href="{{asset('assets/css/magnific-popup.css')}}" rel="stylesheet" type="text/css" />

  <!--== owl.carousel -->
  <link href="{{asset('assets/css/owl.carousel.css')}}" rel="stylesheet" type="text/css" />

  <!--== spacing -->
  <link href="{{asset('assets/css/spacing.css')}}" rel="stylesheet" type="text/css" />

  <!--== theme.min -->
  <link href="{{asset('assets/css/theme.min.css')}}" rel="stylesheet" />

  <!-- inject css end -->
   @yield('css')
</head>

<body>

  <!-- page wrapper start -->

  <div class="page-wrapper">

    <!-- preloader start -->

    <div id="ht-preloader">
      <div class="loader clear-loader">
        <img src="{{asset('assets/images/icon/c_logo.png')}}" width="70" height="70" alt="loading...">
      </div>
    </div>

    <!-- preloader end -->
    <!--header start-->

    @include('frontend.layouts.header')

    <!--header end-->   

    <!--hero section start-->
    @if(request()->is('/'))
 
      <section class="hero-banner position-relative hero-shape2 custom-py-1 overflow-hidden">
      <div class="container">
        <div class="row align-items-center justify-content-between">
          <div class="col-12 col-lg-5">
            <h1 class="mb-4 font-w-4">
              Welcome to STAFO <br /> <span class="font-w-6 text-primary">Smart, Simple & Affordable HRMS</span>
            </h1>
            <p class="lead mb-4">India&apos;s trusted HR software for startups, SMEs, and remote teams.
              Manage attendance, payroll, leave, tasks, CRM, and real-time location tracking — all in one app.</p>
            <!-- <a href="#" class="btn btn-primary">
              Start Free Trial <i class="fa-solid fa-arrow-right" style="color: #1f1f1f;"></i>
            </a> -->
            <a href="https://play.google.com/store/apps/details?id=com.stafo.app&hl=en_IN" class="mx-1" target="_blank">
              <img src="{{asset('assets/images/icon/playstore.svg')}}" width="150" height="46" alt="playstore">
            </a>
            <!-- <p class="mt-2">🎁 15-Day Free Trial – No Credit Card Needed</p> -->
          </div>

          <div class="col-12 col-lg-6 mt-5 mt-lg-0">
            <div class="bg-white shadow-primary rounded overflow-hidden p-3 me-lg-n8">
              <div class="owl-carousel no-pb" data-dots="false" data-items="1" data-autoplay="true">
                
                @foreach($homebanner as $banner)
                <div class="item">
                  <img class="img-fluid border border-light" src="{{ asset('uploads/homebanner/')}}/{{$banner->image}}" alt="">
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
        <!-- / .row -->
      </div>
      <!-- / .container -->
    </section>
    @else    
    <section class="hero-banner position-relative custom-pt-1 custom-pb-2 bg-light"
      data-bg-img="{{asset('assets/images/bg/02.png')}}">
      <div class="container">
        <div class="row text-white text-center">
          <div class="col">
            <h1 class="text-dark"> @yield('heading')</h1>

          </div>
        </div>
        <!-- / .row -->
      </div>
      <!-- / .container -->
      <div class="shape-1 bottom">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
          <path fill="#fff" fill-opacity="1"
            d="M0,288L48,288C96,288,192,288,288,266.7C384,245,480,203,576,208C672,213,768,267,864,245.3C960,224,1056,128,1152,96C1248,64,1344,96,1392,112L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
          </path>
        </svg>
      </div>
    </section>
    @endif
    <!--hero section end-->


    <!--body content start-->

    <div class="page-content">

       @yield('content')


      <!--newsletter start-->

      <!-- <section>
        <div class="container">
          <div class="row">
            <div class="col">
              <div class="bg-light bg-pos-l py-6 px-4 px-lg-6 text-center rounded"
                data-bg-img="assets/images/bg/02.png">
                <div class="mb-5">
                  <h2><span class="font-w-4 d-block">Start Your Free Trial</span> Experience STAFO Today</h2>
                </div>
                <div class="row justify-content-center">
                  <div class="col-lg-9">
                    <div class="subscribe-form text-center">
                      <form id="mc-form" class="row mb-3">
                        <div class="col-md">
                          <input type="text" value="" name="NAME" class="name form-control border-0 shadow-sm rounded"
                            id="mc-name" placeholder="Your Name" required="">
                        </div>
                        <div class="col-md">
                          <input type="email" value="" name="EMAIL"
                            class="email form-control border-0 shadow-sm rounded mt-3 mt-md-0" id="mc-email"
                            placeholder="Business Email Address" required="">
                        </div>
                        <div class="col-md-auto">
                          <input class="btn btn-primary mt-3 mt-md-0" type="submit" name="subscribe"
                            value="Start 15-Day Free Trial">
                        </div>
                      </form>
                      <small class="text-dark">No credit card required. Get full access to all features for 15
                        days.</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section> -->


      <!--newsletter end-->

    </div>

    <!--body content end-->


    <!--footer start-->

    @include('frontend.layouts.footer')
    

    <!--footer end-->

  </div>

  <!-- page wrapper end -->


  <!--back-to-top start-->

  <div class="scroll-top"><a class="smoothscroll" href="#top">Scroll Top</a></div>

  <!--back-to-top end-->

  <!-- inject js start -->

  <!--== jquery -->
  <script src="{{asset('assets/js/jquery.min.js')}}"></script>

  <!--== bootstrap -->
  <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>

  <!--== owl-carousel -->
  <script src="{{asset('assets/js/owl.carousel.min.js')}}"></script>

  <!--== magnific-popup -->
  <script src="{{asset('assets/js/jquery.magnific-popup.min.js')}}"></script>

  <!--== counter -->
  <script src="{{asset('assets/js/counter.js')}}"></script>

  <!--== countdown -->
  <script src="{{asset('assets/js/jquery.countdown.min.js')}}"></script>

  <!--== particles -->
  <script src="{{asset('assets/js/particles.js')}}"></script>

  <!--== typer -->
  <script src="{{asset('assets/js/typer.js')}}"></script>

  <!--== wow -->
  <script src="{{asset('assets/js/wow.min.js')}}"></script>

  <!--== theme-script -->
  <script src="{{asset('assets/js/theme-script.js')}}"></script>

  <!-- fontawesome -->
  <script src="https://kit.fontawesome.com/0f23341f0d.js" crossorigin="anonymous"></script>

  <!-- inject js end -->
@yield('js')
</body>

</html>