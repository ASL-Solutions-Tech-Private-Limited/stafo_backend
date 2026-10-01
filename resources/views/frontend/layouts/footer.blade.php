<footer class="py-7" style="background-color:#15152a !important; border-top: 1px solid #23233c;">
      <div class="container">
        <div class="row">
          <div class="col-12 col-lg-5 col-xl-4 me-auto mb-5 mb-lg-0">
            <a class="footer-logo d-inline-block text-decoration-none mb-3" href="{{ route('index') }}">
              <div class="bg-white p-1 rounded-3 shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 62px; height: 62px;">
                <img src="{{ asset('assets/images/icon/c_logo.png') }}" width="52" height="52" alt="STAFO Logo" class="img-fluid">
              </div>
            </a>
            <p class="my-3 text-light" style="color: #94a3b8 !important; font-size: 0.92rem;">Stay connected with STAFO <br /> India's trusted HRMS for automated attendance, payroll, and field workforce operations.</p>
            <div class="mt-4 pt-1">
              <div class="text-white-50 small mb-2" style="font-size: 0.78rem;"><i class="fa-solid fa-mobile-screen-button text-warning me-1"></i> Get the STAFO Mobile App:</div>
              <a href="https://play.google.com/store/apps/details?id=com.stafo.app&hl=en_IN" target="_blank" class="btn btn-outline-light rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2.5 text-decoration-none shadow-sm" style="border-color: rgba(255,255,255,0.25); background: rgba(255,255,255,0.06); transition: all 0.2s ease;">
                <i class="fa-brands fa-google-play fs-4 text-warning"></i>
                <div class="text-start lh-1">
                  <span class="d-block text-white-50" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">GET IT ON</span>
                  <span class="text-white fw-bold" style="font-size: 0.88rem;">Google Play</span>
                </div>
              </a>
            </div>
          </div>
          <div class="col-12 col-lg-6 col-xl-7">
            <div class="row">
              <div class="col-12 col-sm-4">
                <h5 class="mb-4 text-white">Pages</h5>
                <ul class="list-unstyled mb-0">
                  <li class="mb-3"><a class="list-group-item-action text-light" href="{{ route('aboutUs') }}">About</a>
                  </li>
                  <li class="mb-3"><a class="list-group-item-action text-light" href="{{ route('price') }}">Pricing</a>
                  </li>
                  <!-- <li class="mb-3"><a class="list-group-item-action text-light" href="blog-listing.html">Blogs</a>
                  </li> -->
                  <li><a class="list-group-item-action text-light" href="{{ route('contactUs') }}">Contact Us</a>
                  </li>
                </ul>
              </div>
              <div class="col-12 col-sm-4 mt-6 mt-sm-0">
                <h5 class="mb-4 text-white">Quick Links</h5>
                <ul class="list-unstyled mb-0">
                  <li class="mb-3"><a class="list-group-item-action text-light" href="https://play.google.com/store/apps/details?id=com.stafo.app&hl=en_IN" target="_blank"><i class="fa-brands fa-google-play text-warning me-1"></i> Download App</a>
                  </li>
                  <li class="mb-3"><a class="list-group-item-action text-light" href="{{ route('privacy') }}">Privacy
                      Policy</a>
                  </li>
                  <li class="mb-3"><a class="list-group-item-action text-light" href="{{ route('terms') }}">Terms &
                      Conditions</a>
                  </li>
                  <li class="mb-3"><a class="list-group-item-action text-light" href="{{ route('faq') }}">FAQs</a>
                  </li>
                  <li><a class="list-group-item-action text-light" href="{{ route('carrer') }}">Careers</a>
                  </li>
                </ul>
              </div>
              <div class="col-12 col-sm-4 mt-6 mt-sm-0">
                <h5 class="mb-4 text-white">Our Address</h5>
                <div class="mb-3">
                  <p class="mb-0 text-light">Jadavpur, Kolkata – 700032, West Bengal, India</p>
                </div>
                <div class="mb-3">
                  <a class="btn-link text-light" href="mailto:sales@stafo.in">sales@stafo.in</a>
                </div>
                <div>
                  <a class="btn-link text-light" href="tel:+918389039361">+91 8389039361</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row my-5">
          <div class="col">
            <hr class="m-0">
          </div>
        </div>
        <div class="row align-items-center">
          <div class="col-md-6 text-white">
            Copyright ©{{ date('Y') }} STAFO. All rights reserved | Powered by &nbsp; <i
              class="lar la-heart text-success links heartBeat2"></i>
            <a class="text-success fs-6 links" href="javascript:void(0)">ASL Solutions Tech Pvt Ltd</a>
          </div>
          <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <ul class="list-inline mb-0">
              <li class="me-3 list-inline-item"> <a class="list-group-item-action text-light"
                  href="{{ route('privacy') }}">
                  Privacy Policy
                </a>
              </li>
              <li class="list-inline-item"> <a class="list-group-item-action text-light"
                  href="{{ route('terms') }}">
                  Terms & Support
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>