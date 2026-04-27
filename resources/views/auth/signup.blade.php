<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign Up</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- fontawesome css link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- bootstrap css link -->
    <link rel="stylesheet" href="main/css/bootstrap.min.css">
    <!-- swipper css link -->
    <link rel="stylesheet" href="main/css/swiper.min.css">
    <!-- lightcase css links -->
    <link rel="stylesheet" href="main/css/lightcase.css">
    <!-- odometer css link -->
    <link rel="stylesheet" href="main/css/odometer.css">
    <!-- line-awesome-icon css -->
    <link rel="stylesheet" href="main/css/icomoon.css">
    <!-- line-awesome-icon css -->
    <link rel="stylesheet" href="main/css/line-awesome.min.css">
    <!-- animate.css -->
    <link rel="stylesheet" href="main/css/animate.css">
    <!-- aos.css -->
    <link rel="stylesheet" href="main/css/aos.css">
    <!-- nice select css -->
    <link rel="stylesheet" href="main/css/style.css">
    <link rel="stylesheet" href="main/css/responsive.css">
    <!-- nice select css -->
    <style>
        /* Form Container */
        .otp-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 50px 15px;
        }

        /* Form Styling */
        .signup-form {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Form Title */
        .signup-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .otp-container {
                padding: 30px 10px;
            }
        }
    </style>
</head>

<body>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <header class="header-section two shadow-sm">
        <div class="header">
            <div class="header-bottom-area">
                <div class="container custom-container">
                    <div class="header-menu-content">
                        <nav class="navbar navbar-expand-xl p-0">
                            <a class="site-logo site-title " href="{{ url('/') }}">
                                <!-- <h2 class="text-dark fw-bold m-0">ALSHR</h2> -->
                                <img src="{{asset('main/logo/logo.jpg')}}" alt="logo" width="100px" height="100">
                            </a>
                            <button class="navbar-toggler d-block d-xl-none ml-auto" type="button"
                                data-toggle="collapse" data-target="#navbarSupportedContent"
                                aria-controls="navbarSupportedContent" aria-expanded="false"
                                aria-label="Toggle navigation">
                                <span class="toggle-bar"></span>
                            </button>
                            <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                                <ul class="navbar-nav main-menu">
                                    <li><a href="{{ url('/') }}">Home</a></li>
                                    <li><a href="{{ route('aboutUs') }}">About</a></li>
                                    <li><a href="{{ route('contactUs') }}">Contact</a></li>
                                </ul>
                                <div class="header-right">
                                    <div class="header-action-area d-flex align-items-center">
                                        <!-- <div class="header-action">
                                            <a href="#" class="btn--base"><i
                                                    class="fa-regular fa-user mr-2"></i>Sign Up</a>
                                        </div> -->
                                        <div class="header-action ml-2">
                                            <a href="login-2" class="btn--base active"><i
                                                    class="fa-solid fa-right-to-bracket mr-2"></i>Login</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
End Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <section class="login-section pt-120 pb-40">
        <div class="container h-custom">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-md-9 col-lg-8">
                    <!-- signup Form -->
                    <div class="signup-container mt-4" id="signup-container">
                        <div class="signup-form">
                            <h2>Sign Up</h2>
                            <form id="signup-form">
                                <div class="mb-3">
                                    <label for="Name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="Name"
                                        placeholder="Enter your Name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="Email" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="Email"
                                        placeholder="Enter your Email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="Number" class="form-label">Mobile Number</label>
                                    <input type="text" class="form-control" id="Number"
                                        placeholder="Enter your Mobile Number" required>
                                </div>
                                <div class="mb-3">
                                    <label for="signup-password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="signup-password"
                                        placeholder="Enter your password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="Com-password" class="form-label">Confirm Password</label>
                                    <input type="text" class="form-control" id="Com-password"
                                        placeholder="Enter your Confirm Password" required>
                                </div>
                                <button type="submit" class="btn btn--base active w-100">Register</button>
                                <div class="mt-3 text-center">
                                    <a href="login-2" id="back-to-login-link">Back to Login</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Footer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <footer class="footer-section five pt-40">
        <div class="container">
            <div class="row mb-30-none">
                <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                    <div class="footer-widget">
                        <div class="footer-logo">
                            <a class="site-logo site-title" href="index.html">
                                <h2 class="text-dark fw-bold m-0">ALSHR</h2>
                            </a>
                        </div>
                        <p>ALSHR Lorem ipsum dolor sit amet consectetur, adipisicing elit. Soluta veritatis
                            necessitatibus accusamus tempora consequatur tempore quas error, iusto similique sit?</p>
                        <ul class="footer-social two">
                            <li><a href="#0"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#0"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#0"><i class="fab fa-google-plus-g"></i></a></li>
                            <li><a href="#0"><i class="fab fa-instagram"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6 mb-30">
                    <div class="footer-widget">
                        <h4 class="title">Services</h4>
                        <ul class="footer-list">
                            <li><a href="#0">Accountant</a></li>
                            <li><a href="#0">HRM Software</a></li>
                            <li><a href="#0">App Homepage</a></li>
                            <li><a href="#0">NFT Mining</a></li>
                            <li><a href="#0">Marketing tools</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6 mb-30">
                    <div class="footer-widget">
                        <h4 class="title">Quick links</h4>
                        <ul class="footer-list">
                            <li><a href="#0">About us</a></li>
                            <li><a href="#0">News</a></li>
                            <li><a href="#0">Blog</a></li>
                            <li><a href="#0">FAQ</a></li>
                            <li><a href="#0">Careers</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                    <div class="footer-widget">
                        <h5 class="title">Subscribe newsletter</h5>
                        <form class="footer-subscribe-form three">
                            <input type="email" class="form--control" placeholder="Enter your mail..."
                                autocomplete="off">
                            <button type="submit">SUBSCRIBE NOW!</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="copyright-wrapper five">
                <div class="row justify-content-center">
                    <div class="col-xl-12">
                        <div class="copyright-area">
                            <p>Copyright © 2025 <a href="index.html">ALSHR</a>. All Rights Reserved.</p>
                            <ul class="copyright-list">
                                <li><a href="#0">Terms & Condition</a></li>
                                <li><a href="#0">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Footer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
</body>

</html>
