<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Super Admin Login - Salarybox</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="keywords" name="keywords">
    <meta content="description" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet"> -->

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to bottom, #003366, #006699);
            font-family: 'Heebo', sans-serif;
        }

        .container-xxl {
            padding: 0;
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .login-card h3 {
            font-weight: 700;
            color: #003366;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }

        .form-control:focus {
            border-color: #006699;
            box-shadow: 0 0 5px rgba(0, 102, 153, 0.5);
        }

        .btn-primary {
            background-color: #006699;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            width: 100%;
            font-size: 16px;
        }

        .btn-primary:hover {
            background-color: #004d66;
            transition: 0.3s;
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .alert i {
            font-size: 18px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container-xxl d-flex align-items-center justify-content-center vh-100">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="login-card">
                    <h3>Super Admin Login</h3>

                    <!-- Display error message if login fails -->
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password">
                            @if ($errors->has('password'))
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <input type="submit" class="btn btn-primary" value="Login">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>
