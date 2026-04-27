@extends('frontend.layouts.master') 
@section('title', 'About Us | STAFO - Leading HRMS Solution Provider in India')
@section('heading', 'About Us')
 @section('content')

      <!--about start-->

      <section class="">
        <div class="container">
          <div class="row align-items-center justify-content-between">
            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
              <img src="assets/images/about/01.png" alt="Image" class="img-fluid rounded">
            </div>
            <div class="col-12 col-lg-6">
              <div class="mb-5">
                <h2><span class="font-w-4 d-block">India's Trusted HRMS Software</span>Smart Workforce Management</h2>
                <p class="lead mb-0">STAFO is a cloud-based HR management software in India that simplifies payroll
                  processing, attendance tracking, leave management, CRM, and real-time location monitoring. Designed
                  for startups and SMEs, STAFO helps automate HR tasks and boost business productivity with an
                  easy-to-use, mobile-first platform.</p>
              </div>
              <div class="d-flex align-items-start mb-4">
                <div class="me-3"> <span class="list-dot" data-bg-color="#01a479"></span>
                </div>
                <div>
                  <h6 class="mb-2">Smart & Simple HRMS</h6>
                  <p class="mb-0">Next-generation HRMS software built to make workforce management simpler, faster, and
                    smarter.</p>
                </div>
              </div>
              <div class="d-flex align-items-start">
                <div class="me-3"> <span class="list-dot" data-bg-color="#ffbe30"></span>
                </div>
                <div>
                  <h6 class="mb-2">Made in India, for India</h6>
                  <p class="mb-0">Affordable HR software with regional customization, local support, and tools designed
                    for Indian businesses.</p>
                </div>
              </div>
              <!-- <a href="#" class="btn btn-outline-primary mt-5">Start 15-Day Free Trial</a> -->
            </div>
          </div>
        </div>
      </section>

      <!--about end-->


      <!--counter start-->

      <section>
        <div class="container">
          <div class="row justify-content-center text-center">
            <div class="col-lg-8">
              <div class="mb-5">
                <h2><span class="font-w-4 d-block">Our Mission & Vision</span> Empowering Indian Businesses</h2>
                <p class="lead mb-0">To become India's most trusted cloud-based HRMS, offering scalable, reliable, and
                  user-friendly tools that help businesses manage their teams efficiently.</p>
              </div>
            </div>
          </div>
          <div class="row align-items-center text-center">
            <div class="col-12 col-sm-6 col-lg-3">
              <div>
                <div class="d-flex align-items-center justify-content-center"> <i
                    class="flaticon-project ic-3x text-primary me-2"></i>
                  <span class="count-number display-4 text-dark" data-to="1000" data-speed="2000">1000</span>
                  <span class="display-4 text-dark">+</span>
                </div>
                <h6 class="text-light mb-0">Active Users</h6>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mt-4 mt-sm-0">
              <div>
                <div class="d-flex align-items-center justify-content-center"> <i
                    class="flaticon-group ic-3x text-primary me-2"></i>
                  <span class="count-number display-4 text-dark" data-to="24" data-speed="2000">24</span>
                  <span class="display-4 text-dark">/7</span>
                </div>
                <h6 class="text-light mb-0">Support Available</h6>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mt-4 mt-lg-0">
              <div>
                <div class="d-flex align-items-center justify-content-center"> <i
                    class="flaticon-opinion ic-3x text-primary me-2"></i>
                  <span class="count-number display-4 text-dark" data-to="99" data-speed="2000">99</span>
                  <span class="display-4 text-dark">%</span>
                </div>
                <h6 class="text-light mb-0">Customer Satisfaction</h6>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mt-4 mt-lg-0">
              <div>
                <div class="d-flex align-items-center justify-content-center"> <i
                    class="flaticon-affection ic-3x text-primary me-2"></i>
                  <span class="count-number display-4 text-dark" data-to="15" data-speed="2000">15</span>
                  <span class="display-4 text-dark">Days</span>
                </div>
                <h6 class="text-light mb-0">Free Trial</h6>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!--counter end-->


      <!--service start-->

      <section>
        <div class="container">
          <div class="row align-items-center justify-content-between">
            <div class="col-12 col-lg-5 mb-5 mb-lg-0">
              <div class="mb-5">
                <h2><span class="font-w-4 d-block">Our Core Values</span> What Sets Us Apart</h2>
                <p class="lead mb-0">STAFO is built on strong values that prioritize customer success and innovation.
                </p>
              </div>
              <div class="accordion" id="accordion">
                <div class="accordion-item rounded mb-2">
                  <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button border-0 mb-0 bg-transparent" type="button"
                      data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                      aria-controls="collapseOne">
                      Delivering Valuable Solutions
                    </button>
                  </h2>
                  <div id="collapseOne" class="accordion-collapse border-0 collapse show" aria-labelledby="headingOne"
                    data-bs-parent="#accordion">
                    <div class="accordion-body text-muted">We create real solutions to real HR problems—saving time,
                      lowering costs, and improving accuracy. From Face ID attendance and GPS tracking to automated
                      timesheets, STAFO is built to perform.</div>
                  </div>
                </div>
                <div class="accordion-item rounded mb-2">
                  <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button border-0 mb-0 bg-transparent collapsed" type="button"
                      data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                      aria-controls="collapseTwo">
                      Supporting Local Businesses
                    </button>
                  </h2>
                  <div id="collapseTwo" class="accordion-collapse border-0 collapse" aria-labelledby="headingTwo"
                    data-bs-parent="#accordion">
                    <div class="accordion-body text-muted">Made in India, for India. We offer affordable HR software
                      with regional customization, local support, and tools designed for Indian SMEs, startups, and
                      field teams.</div>
                  </div>
                </div>
                <div class="accordion-item rounded">
                  <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button border-0 mb-0 bg-transparent collapsed" type="button"
                      data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false"
                      aria-controls="collapseThree">
                      Simple, Intuitive Tools for All
                    </button>
                  </h2>
                  <div id="collapseThree" class="accordion-collapse border-0 collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordion">
                    <div class="accordion-body text-muted">STAFO is easy to use—even for non-tech users. HR teams,
                      managers, and employees can manage leave requests, payroll, tasks, and CRM activities effortlessly
                      from desktop or mobile.</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-6">
              <div class="row">
                <div class="col-md-6">
                  <div class="p-4 rounded shadow">
                    <div class="bg-primary p-2 d-inline-block rounded">
                      <div class="f-icon-m text-white"> <i class="flaticon-lightbulb"></i>
                      </div>
                    </div>
                    <h5 class="mt-4 mb-3">Face ID Attendance</h5>
                    <p class="mb-0">Secure and contactless attendance tracking with facial recognition technology for
                      your workforce.</p>
                  </div>
                </div>
                <div class="col-md-6 mt-5">
                  <div class="p-4 rounded shadow">
                    <div class="bg-primary p-2 d-inline-block rounded">
                      <div class="f-icon-m text-white"> <i class="flaticon-migrating"></i>
                      </div>
                    </div>
                    <h5 class="mt-4 mb-3">Real-time Tracking</h5>
                    <p class="mb-0">Monitor field employees with GPS tracking and get live updates on team locations and
                      activities.</p>
                  </div>
                </div>
                <div class="col-md-6 mt-5 mt-md-0">
                  <div class="p-4 rounded shadow">
                    <div class="bg-primary p-2 d-inline-block rounded">
                      <div class="f-icon-m text-white"> <i class="flaticon-graphs"></i>
                      </div>
                    </div>
                    <h5 class="mt-4 mb-3">Automated Payroll</h5>
                    <p class="mb-0">Streamline salary processing with automatic attendance integration and compliance
                      management.</p>
                  </div>
                </div>
                <div class="col-md-6 mt-5">
                  <div class="p-4 rounded shadow">
                    <div class="bg-primary p-2 d-inline-block rounded">
                      <div class="f-icon-m text-white"> <i class="flaticon-3d-modeling"></i>
                      </div>
                    </div>
                    <h5 class="mt-4 mb-3">Mobile-First CRM</h5>
                    <p class="mb-0">Manage customer relationships, tasks, and team collaboration from any device,
                      anywhere.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!--service end-->


      <!--team start-->

      <section class="pt-0">
        <div class="container">
          <div class="row justify-content-center text-center">
            <div class="col-12 col-md-12 col-lg-8">
              <div class="mb-5">
                <h2><span class="font-w-4">Why Choose</span> STAFO?</h2>
                <p class="lead mb-0">We're your digital HR partner, empowering startups, SMEs, and growing businesses to
                  streamline operations, reduce manual errors, and enhance team productivity.</p>
              </div>
            </div>
          </div>
          <!-- / .row -->
          <div class="row">
            <div class="col-12 col-lg-3 col-md-6 mb-4 mb-lg-0">
              <div class="border text-center py-5 px-3">
                <div class="mb-4">
                  <img class="img-fluid shadow overflow-hidden" src="assets/images/about/mobile.svg" alt="" width="90"
                    height="50">
                </div>
                <div>
                  <h5 class="mb-1">Mobile-First Design</h5>
                  <small class="text-muted mb-3 d-block">Smart Dashboard</small>
                  <p class="mb-0">Access your HR tools anytime, anywhere with our intuitive mobile app and responsive
                    dashboard.</p>
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-3 col-md-6 mb-4 mb-lg-0">
              <div class="text-center border py-5 px-3">
                <div class="mb-4">
                  <img class="img-fluid shadow overflow-hidden" src="assets/images/about/security.svg" alt=""
                    width="225">
                </div>
                <div>
                  <h5 class="mb-1">Advanced Security</h5>
                  <small class="text-muted mb-3 d-block">Face ID & QR Code</small>
                  <p class="mb-0">Secure attendance tracking with facial recognition and QR code technology for your
                    workforce.</p>
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-3 col-md-6 mb-4 mb-md-0">
              <div class="border text-center py-5 px-3">
                <div class="mb-4">
                  <img class="img-fluid shadow overflow-hidden" src="assets/images/about/location.svg" alt=""
                    width="160">
                </div>
                <div>
                  <h5 class="mb-1">Real-time Tracking</h5>
                  <small class="text-muted mb-3 d-block">GPS & Location</small>
                  <p class="mb-0">Monitor field employees with GPS tracking and get live updates on team locations.</p>
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-3 col-md-6">
              <div class="border text-center py-5 px-3">
                <div class="mb-4">
                  <img class="img-fluid  shadow overflow-hidden" src="assets/images/about/payroll.svg" alt=""
                    width="202">
                </div>
                <div>
                  <h5 class="mb-1">Automated Payroll</h5>
                  <small class="text-muted mb-3 d-block">Smart Processing</small>
                  <p class="mb-0">Streamline salary processing with automatic attendance integration and compliance.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!--team end-->

@endsection