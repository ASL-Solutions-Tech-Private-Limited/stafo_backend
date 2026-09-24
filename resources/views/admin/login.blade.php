<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Super Admin Login | STAFO HRMS</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="STAFO Super Admin Portal" name="description">

    <!-- Favicon -->
    <link href="{{ asset('main/images/favicon_io (1)/favicon-32x32.png') }}" rel="icon">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="{{ asset('main/css/bootstrap.min-5.3.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #1d4ed8;
            --accent: #10b981;
            --dark: #0f172a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #090d16;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            color: #1e293b;
            padding: 1.5rem 1rem;
        }

        /* Ambient Glowing Background Orbs */
        .ambient-glow-1 {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(59, 130, 246, 0) 70%);
            top: -150px;
            left: -100px;
            pointer-events: none;
            filter: blur(60px);
        }

        .ambient-glow-2 {
            position: absolute;
            width: 550px;
            height: 550px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.18) 0%, rgba(16, 185, 129, 0) 70%);
            bottom: -150px;
            right: -100px;
            pointer-events: none;
            filter: blur(70px);
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 2.5rem 2.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            font-size: 0.72rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .login-brand-logo {
            height: 56px;
            width: auto;
            object-fit: contain;
            transition: transform 0.25s ease;
        }

        .login-brand-logo:hover {
            transform: scale(1.05);
        }

        .form-floating-custom {
            position: relative;
            margin-bottom: 1.2rem;
        }

        .form-floating-custom .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            transition: color 0.2s ease;
            pointer-events: none;
        }

        .form-floating-custom .form-control {
            padding-left: 44px;
            padding-right: 44px;
            height: 50px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            font-size: 0.925rem;
            font-weight: 500;
            transition: all 0.2s ease;
            background-color: #f8fafc;
        }

        .form-floating-custom .form-control:focus {
            background-color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        }

        .form-floating-custom .form-control:focus + .input-icon {
            color: var(--primary);
        }

        .password-toggle-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0;
            font-size: 0.95rem;
            transition: color 0.2s ease;
        }

        .password-toggle-btn:hover {
            color: #334155;
        }

        .btn-submit-login {
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit-login:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.45);
            color: #ffffff;
        }

        .btn-submit-login:active {
            transform: translateY(0);
        }

        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 1.5rem;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Ambient Light Background Orbs -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="login-wrapper">
        <div class="login-card">
            <!-- Header Brand & Badge -->
            <div class="text-center mb-4">
                <div class="mb-3">
                    <img src="{{ asset('main/images/logo.png') }}" alt="STAFO Logo" class="login-brand-logo">
                </div>
                <div class="mb-2">
                    <span class="brand-badge">
                        <i class="fa-solid fa-shield-halved text-primary"></i> Super Admin Portal
                    </span>
                </div>
                <h4 class="fw-bold text-dark mb-1">Welcome Back</h4>
                <p class="text-muted small mb-0">Enter your administrative credentials to continue</p>
            </div>

            <!-- Error Alerts -->
            @if (session('error'))
                <div class="alert alert-danger border-0 rounded-3 shadow-sm d-flex align-items-center gap-2 p-3 mb-3" role="alert">
                    <i class="fa-solid fa-triangle-exclamation fs-5 flex-shrink-0 text-danger"></i>
                    <div class="small fw-semibold text-danger">{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Login Form -->
            <form action="" method="post" id="adminLoginForm">
                @csrf
                
                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold text-dark mb-1">Email Address</label>
                    <div class="form-floating-custom">
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email') }}" placeholder="admin@stafo.in" required autocomplete="email" autofocus>
                        <i class="fa-regular fa-envelope input-icon"></i>
                    </div>
                    @if (isset($errors) && $errors->has('email'))
                        <div class="text-danger small mt-1">
                            <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first('email') }}
                        </div>
                    @endif
                </div>

                <!-- Password Field -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label small fw-semibold text-dark mb-0">Password</label>
                    </div>
                    <div class="form-floating-custom position-relative">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn" aria-label="Toggle password visibility">
                            <i class="fa-regular fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    @if (isset($errors) && $errors->has('password'))
                        <div class="text-danger small mt-1">
                            <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first('password') }}
                        </div>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-submit-login" id="submitBtn">
                    <span>Sign In to Super Admin</span>
                    <i class="fa-solid fa-arrow-right-long"></i>
                </button>
            </form>

            <!-- Security Footer -->
            <div class="security-badge">
                <i class="fa-solid fa-lock text-success"></i>
                <span>256-Bit SSL Encrypted Admin Gateway</span>
            </div>
        </div>
        
        <!-- Platform Copyright -->
        <div class="text-center mt-3">
            <small class="text-white-50" style="font-size: 0.75rem;">&copy; {{ date('Y') }} STAFO HRMS. All rights reserved.</small>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('main/js/bootstrap.bundle.min5.3.js') }}"></script>
    <script>
        // Password Visibility Toggle
        document.getElementById('togglePasswordBtn').addEventListener('click', function() {
            const pwdInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwdInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form Submit Loading Feedback
        document.getElementById('adminLoginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-2"></i> Signing In...';
        });
    </script>
</body>

</html>
