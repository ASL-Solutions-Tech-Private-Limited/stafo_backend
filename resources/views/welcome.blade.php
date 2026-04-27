@extends('frontend.layouts.master')
@section('title', 'STAFO | Leading HRMS Solution Provider in India')

@section('content')

      <!--feature start-->

      <section class="p-0">
        <div class="container">
          <div class="row align-items-center justify-content-center">
            <div class="col-lg-5 col-md-6 pe-lg-4">
              <div class="mb-5">
                <h2 class="mb-0">Exclusive services <span class="font-w-4 d-block">Everything you need to manage your
                    workforce efficiently and effectively.</span>
                </h2>
              </div>
              <div class="px-lg-7 px-4 py-5 rounded bg-white shadow text-center">
                <div>
                  <img class="img-fluid" src="assets/images/svg/01.svg" alt="">
                </div>
                <h5 class="mt-4 mb-3">Dashboard</h5>
                <p class="mb-0">Real-time overview with live employee tracking, CRM insights, attendance, payroll, and
                  tasks all in one place.</p>
              </div>
              <div class="px-lg-7 px-4 py-5 rounded bg-white shadow text-center mt-5">
                <div>
                  <img class="img-fluid" src="assets/images/svg/02.svg" alt="">
                </div>
                <h5 class="mt-4 mb-3">Easy to use</h5>
                <p class="mb-0">No Technical Skills needed, start in minutes with guided setup.</p>
              </div>
            </div>
            <div class="col-lg-5 col-md-6 ps-lg-4 mt-5 mt-lg-0">
              <div class="px-lg-7 px-4 py-5 rounded bg-white shadow text-center">
                <div>
                  <img class="img-fluid" src="assets/images/hero/app.svg" alt="" width="250">
                </div>
                <h5 class="mt-4 mb-3">Mobile Apps</h5>
                <p class="mb-0">Manage HR on the go with our user-friendly Android App</p>
              </div>
              <div class="px-lg-7 px-4 py-5 rounded bg-white shadow text-center mt-5">
                <div>
                  <img class="img-fluid" src="assets/images/svg/04.svg" alt="">
                </div>
                <h5 class="mt-4 mb-3">User Friendly</h5>
                <p class="mb-0">Taking design from Stafo design and typography, contemporary page layouts.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!--feature end-->


      <!--card start-->

      <section class="pb-0">
        <div class="container">
          <div class="row justify-content-center text-center">
            <div class="col-lg-8">
              <div class="mb-5">
                <h2>Powerful HR Features for All</h2>
                <p class="lead mb-0">Everything you need to manage your workforce efficiently and effectively.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="position-relative bg-light pt-0 z-index-1">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-12 z-index-1">
              <div class="col-12 gap-3 col align-items-center">
                <div class="row">
                  <!-- CRM Card -->
                  <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body text-center p-4">
                        <div
                          class="icon-wrapper bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                          style="width: 70px; height: 70px;">
                          <i class="fas fa-users fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold">CRM</h5>
                        <p class="card-text text-muted">Streamlined leave requests, approvals, and balance tracking</p>
                      </div>
                    </div>
                  </div>

                  <!-- Payroll Management Card -->
                  <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body text-center p-4">
                        <div
                          class="icon-wrapper bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                          style="width: 70px; height: 70px;">
                          <i class="fas fa-money-bill-wave fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold">Payroll Management</h5>
                        <p class="card-text text-muted">Automated payroll processing with tax calculations and
                          compliance</p>
                      </div>
                    </div>
                  </div>

                  <!-- Employee Management Card -->
                  <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body text-center p-4">
                        <div
                          class="icon-wrapper bg-info bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                          style="width: 70px; height: 70px;">
                          <i class="fas fa-user-tie fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold">Employee Management</h5>
                        <p class="card-text text-muted">Comprehensive employee profiles, onboarding, and lifecycle
                          management</p>
                      </div>
                    </div>
                  </div>

                  <!-- Performance Analytics Card -->
                  <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body text-center p-4">
                        <div
                          class="icon-wrapper bg-warning bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                          style="width: 70px; height: 70px;">
                          <i class="fas fa-chart-line fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold">Performance Analytics</h5>
                        <p class="card-text text-muted">Data-driven insights and performance evaluation tools</p>
                      </div>
                    </div>
                  </div>

                  <!-- Attendance Tracking Card -->
                  <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body text-center p-4">
                        <div
                          class="icon-wrapper bg-danger bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                          style="width: 70px; height: 70px;">
                          <i class="fas fa-calendar-check fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold">Attendance Tracking</h5>
                        <p class="card-text text-muted">Real-time attendance monitoring with automated time tracking</p>
                      </div>
                    </div>
                  </div>

                  <!-- Security & Compliance Card -->
                  <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body text-center p-4">
                        <div
                          class="icon-wrapper bg-secondary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                          style="width: 70px; height: 70px;">
                          <i class="fas fa-shield-alt fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold">Security & Compliance</h5>
                        <p class="card-text text-muted">Enterprise-grade security with GDPR and compliance features</p>
                      </div>
                    </div>
                  </div>

                  <!-- Leave Management Card -->
                  <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body text-center p-4">
                        <div
                          class="icon-wrapper bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                          style="width: 70px; height: 70px;">
                          <i class="fas fa-calendar-alt fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold">Leave Management</h5>
                        <p class="card-text text-muted">Streamlined leave requests, approvals, and balance tracking</p>
                      </div>
                    </div>
                  </div>

                  <!-- Task Management Card -->
                  <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body text-center p-4">
                        <div
                          class="icon-wrapper bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                          style="width: 70px; height: 70px;">
                          <i class="fas fa-tasks fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold">Task Management</h5>
                        <p class="card-text text-muted">Efficient task assignment, tracking, and completion workflows
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Trip Management Card -->
                  <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body text-center p-4">
                        <div
                          class="icon-wrapper bg-info bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                          style="width: 70px; height: 70px;">
                          <i class="fas fa-plane fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold">Trip Management</h5>
                        <p class="card-text text-muted">Comprehensive travel planning, approvals, and expense tracking
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- / .row -->
          </div>
          <!-- / .container -->
          <div class="shape-1 overflow-hidden">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
              <path fill="#ffffff" fill-opacity="1"
                d="M0,64L48,80C96,96,192,128,288,160C384,192,480,224,576,202.7C672,181,768,107,864,69.3C960,32,1056,32,1152,80C1248,128,1344,224,1392,272L1440,320L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z">
              </path>
            </svg>
          </div>
      </section>

      <!--cards end-->

      <!--counter start-->

      <section>
        <div class="container">
          <div class="row justify-content-center text-center">
            <div class="col-lg-8">
              <div class="mb-5">
                <h2><span class="font-w-4 d-block">We're the best company</span> always deliver more than expected</h2>
                <p class="lead mb-0">Join hundreds of companies transforming their HR operations</p>
              </div>
            </div>
          </div>
          <div class="row align-items-center text-center">
            <div class="col-12 col-sm-6 col-lg-3">
              <div>
                <div class="d-flex align-items-center justify-content-center"> <i
                    class="flaticon-project ic-3x text-primary me-2"></i>
                  <span class="count-number display-4 text-dark" data-to="28" data-speed="1000">32</span>
                  <span class="display-4 text-dark">+</span>
                </div>
                <h6 class="text-light mb-0">Companies Trust Us</h6>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mt-4 mt-sm-0">
              <div>
                <div class="d-flex align-items-center justify-content-center"> <i
                    class="flaticon-group ic-3x text-primary me-2"></i>
                  <span class="count-number display-4 text-dark" data-to="500" data-speed="1000">5000</span>
                  <span class="display-4 text-dark">K+</span>
                </div>
                <h6 class="text-light mb-0">Employees Managed</h6>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mt-4 mt-lg-0">
              <div>
                <div class="d-flex align-items-center justify-content-center"> <i
                    class="flaticon-opinion ic-3x text-primary me-2"></i>
                  <span class="count-number display-4 text-dark" data-to="99" data-speed="1000">99</span>
                  <span class="display-4 text-dark">%</span>
                </div>
                <h6 class="text-light mb-0">Uptime Guarantee</h6>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mt-4 mt-lg-0">
              <div>
                <div class="d-flex align-items-center justify-content-center"> <i
                    class="flaticon-affection ic-3x text-primary me-2"></i>
                  <span class="count-number display-4 text-dark" data-to="120" data-speed="1000">120</span>
                  <span class="display-4 text-dark">+</span>
                </div>
                <h6 class="text-light mb-0">Happy Customers</h6>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!--counter end-->


      <!--about start-->

      <section class="py-4">
        <div class="container">
          <div class="row align-items-center justify-content-between">
            <div class="col-12 col-lg-6">
              <img src="assets/images/about/06.svg" alt="Image" class="img-fluid">
            </div>
            <div class="col-12 col-lg-5 mt-5 mt-lg-0">
              <div class="mb-4">
                <h2>Why Choose Stafo?<span class="font-w-4 d-block">Smart Web & Mobile-first HRMS </span></h2>
                <p class="lead mb-0">Indian SMEs—unifying attendance, payroll, and HR with real-time tracking and zero
                  manual errors.</p>
              </div>
              <div class="row no-gutters">
                <div class="col-sm">
                  <div class="mb-3">
                    <div class="d-flex align-items-center">
                      <div> <i class="las la-angle-right"></i>
                      </div>
                      <p class="mb-0 ms-3">All-in-one HRMS</p>
                    </div>
                  </div>
                  <div class="mb-3">
                    <div class="d-flex align-items-center">
                      <div> <i class="las la-angle-right"></i>
                      </div>
                      <p class="mb-0 ms-3">Mobile App access</p>
                    </div>
                  </div>
                  <div class="mb-3">
                    <div class="d-flex align-items-center">
                      <div> <i class="las la-angle-right"></i>
                      </div>
                      <p class="mb-0 ms-3">High Performance</p>
                    </div>
                  </div>
                </div>
                <div class="col-sm">
                  <div class="mb-3">
                    <div class="d-flex align-items-center">
                      <div> <i class="las la-angle-right"></i>
                      </div>
                      <p class="mb-0 ms-3">QR & Face ID Attendance</p>
                    </div>
                  </div>
                  <div class="mb-3">
                    <div class="d-flex align-items-center">
                      <div> <i class="las la-angle-right"></i>
                      </div>
                      <p class="mb-0 ms-3">Real-time Location Tracking</p>
                    </div>
                  </div>
                  <div class="mb-3">
                    <div class="d-flex align-items-center">
                      <div> <i class="las la-angle-right"></i>
                      </div>
                      <p class="mb-0 ms-3">Built for Indian SMEs</p>
                    </div>
                  </div>
                </div>
              </div>
              <!-- <a href="#" class="btn btn-outline-primary mt-4">
                Learn More
              </a> -->
            </div>
          </div>
        </div>
      </section>

      <!--about end-->


      <!--step start-->

      <section>
        <div class="container">
          <div class="row justify-content-center text-center">
            <div class="col-lg-8">
              <div class="mb-5">
                <h2 class="mb-0">Tutorials<span class="font-w-4 d-block">How to use Stafo ?</span></h2>
              </div>
            </div>
          </div>
          <div class="row text-center">
            <!-- Card 1 -->
            <div class="col-12 col-md-4">
              <div>
                <div class="p-1 rounded shadow position-relative me-3" style="background-color: #075c04;">
                  <div class="video-thumbnail position-relative">
                    <!-- YouTube Thumbnail (max resolution) -->
                    <img src="https://img.youtube.com/vi/muShWxlcFBw/maxresdefault.jpg" class="img-fluid rounded"
                      alt="Video 1 thumbnail">

                    <!-- Play Button Overlay -->
                    <div class="play-button-overlay position-absolute top-50 start-50 translate-middle">
                      <i class="fas fa-play-circle fa-3x text-white opacity-75"></i>
                    </div>
                  </div>
                </div>
                <h4 class="mt-4 mb-2">How to Use STAFO</h4>
                <p class="mb-0 text-light"> AI-Powered HRMS for Payroll, Attendance ,Employee Management,CRM & Live
                  Location Tracking.</p>
              </div>
            </div>

            <!-- Card 2 -->
            <div class="col-12 col-md-4 mt-5">
              <div>
                <div class="p-1 rounded shadow position-relative me-3" style="background-color: #075c04;">
                  <div class="video-thumbnail position-relative">
                    <img src="https://img.youtube.com/vi/mNamVaxBi4g/maxresdefault.jpg" class="img-fluid rounded"
                      alt="Video 2 thumbnail">
                    <div class="play-button-overlay position-absolute top-50 start-50 translate-middle">
                      <i class="fas fa-play-circle fa-3x text-white opacity-75"></i>
                    </div>

                  </div>
                </div>
                <h4 class="mt-4 mb-2">How to Add & Manage Employees</h4>
                <p class="mb-0 text-light">HRMS for Startups & Small Businesses.</p>
              </div>
            </div>

            <!-- Card 3 -->
            <div class="col-12 col-md-4 mt-5 mt-md-0">
              <div>
                <div class="p-1 rounded shadow position-relative me-3" style="background-color: #075c04;">
                  <div class="video-thumbnail position-relative">
                    <img src="https://img.youtube.com/vi/WEFFAjBiQsM/maxresdefault.jpg" class="img-fluid rounded"
                      alt="Video 3 thumbnail">
                    <div class="play-button-overlay position-absolute top-50 start-50 translate-middle">
                      <i class="fas fa-play-circle fa-3x text-white opacity-75"></i>
                    </div>

                  </div>
                </div>
                <h4 class="mt-4 mb-2">How to Verify Your Company Profile</h4>
                <p class="mb-0 text-light">Learn how to easily verify your company profile in STAFO to unlock full HRMS
                  features and ensure secure access.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!--step end-->


    <!--body content end-->
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#submitForm').on('click', function() {
                var formData = $('#callbackRequestForm').serialize();
                $.ajax({
                    url: '{{ route('request.callback') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                $('#exampleModal').modal(
                                    'hide');
                                location
                                    .reload(); // Reload the page to reflect the changes (optional)
                            });
                        }
                        $('#exampleModal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'There was an error processing your request. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
@endsection
