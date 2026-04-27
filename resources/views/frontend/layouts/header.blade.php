<header class="site-header">
      <div id="header-wrap" class="position-absolute w-100 z-index-1">
        <div class="container">
          <div class="row">
            <!--menu start-->
            <div class="col">
              <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand logo text-primary mb-0 font-w-7" href="{{ route('index') }}">
                  <img src="{{asset('assets/images/icon/c_logo.png')}}" width="80" height="80" alt="logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                  aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                  <ul class="navbar-nav mx-auto text">
                    <li class="nav-item dropdown "> <a class="nav-link  {{ request()->is('/') ? 'active' : 'text-muted' }}" href="{{ url('/') }}">Home</a> </li> 
                    <li class="nav-item dropdown"> <a class="nav-link {{ request()->is('about-us') ? 'active' : 'text-muted' }}" href="{{ route('aboutUs') }}">About</a></li>
                    <li class="nav-item dropdown"> <a class="nav-link  {{ request()->is('price') ? 'active' : 'text-muted' }}"
                        href="{{ route('price') }}">Pricing</a></li>
                    <li class="nav-item dropdown"> <a class="nav-link  {{ request()->is('contact-us') ? 'active' : 'text-muted' }}" href="{{ route('contactUs') }}">Contact Us</a>
                    </li>
                    <li class="nav-item dropdown"> <a class="nav-link {{ request()->is('blog') ? 'active' : 'text-muted' }}" href="{{ route('blog') }}">Blogs</a>
                    </li>
                  </ul>
                </div>
                <div class="d-sm-flex align-items-center justify-content-end"> <a
                    class="btn btn-primary text-muted btn-sm ms-3 fs-5 d-sm-inline-block d-none"
                    href="{{ route('login') }}">Sign In</a>
                </div>
              </nav>

            </div>
            <!--menu end-->
          </div>
        </div>
      </div>
    </header>