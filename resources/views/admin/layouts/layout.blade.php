<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Super Admin') | STAFO HRMS</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link href="{{ asset('main/images/favicon_io (1)/favicon-32x32.png') }}" rel="icon">

    <!-- Google Web Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="{{ asset('main/css/bootstrap.min-5.3.css') }}" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tom-select.css') }}" rel="stylesheet">

    <!-- Modern Admin Design System -->
    <link href="{{ asset('css/admin-modern.css') }}?v={{ file_exists(public_path('css/admin-modern.css')) ? filemtime(public_path('css/admin-modern.css')) : time() }}" rel="stylesheet">

    @yield('css')
</head>

<body class="admin-body">
    <!-- Admin Wrapper -->
    <div class="admin-wrapper" id="adminWrapper">

        <!-- Sidebar Navigation -->
        @include('admin.layouts.sidebar')

        <!-- Main Content Column -->
        <div class="admin-main">
            <!-- Topbar Header -->
            @include('admin.layouts.header')

            <!-- Body View Area -->
            <main class="admin-content-body">
                @yield('content')
            </main>

            <!-- Footer -->
            @include('admin.layouts.footer')
        </div>

        <!-- Back to Top Button -->
        <button type="button" id="backToTopBtn" aria-label="Back to Top" title="Back to Top">
            <i class="fa-solid fa-arrow-up"></i>
        </button>
    </div>

    <!-- SweetAlert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('sweetalert::alert')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('main/js/bootstrap.bundle.min5.3.js') }}"></script>
    <script src="{{ asset('lib/chart/chart.min.js') }}"></script>
    <script src="{{ asset('js/tom-select.complete.min.js') }}"></script>

    <script>
        // Responsive Sidebar Drawer & Desktop Toggle
        document.addEventListener('DOMContentLoaded', function () {
            const wrapper = document.getElementById('adminWrapper');
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');

            function toggleSidebar() {
                if (window.innerWidth < 992) {
                    sidebar.classList.toggle('show');
                    backdrop.classList.toggle('show');
                } else {
                    wrapper.classList.toggle('sidebar-collapsed');
                }
            }

            function closeMobileSidebar() {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeMobileSidebar);
            if (backdrop) backdrop.addEventListener('click', closeMobileSidebar);

            // Back to Top Functionality
            const backToTop = document.getElementById("backToTopBtn");
            window.addEventListener('scroll', function () {
                if (window.scrollY > 300) {
                    backToTop.style.display = "inline-flex";
                } else {
                    backToTop.style.display = "none";
                }
            });

            if (backToTop) {
                backToTop.addEventListener('click', function () {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }

            // Initialize TomSelect if elements exist
            if (document.querySelector('.tomselect')) {
                new TomSelect('.tomselect', {
                    create: false,
                    plugins: ['remove_button'],
                    persist: false,
                    sortField: { field: "text", direction: "asc" }
                });
            }

            if (document.querySelector('.tomselect2')) {
                new TomSelect('.tomselect2', {
                    create: false,
                    plugins: ['remove_button'],
                    persist: false,
                    sortField: { field: "text", direction: "asc" }
                });
            }
        });
    </script>

    @yield('scripts')
</body>

</html>