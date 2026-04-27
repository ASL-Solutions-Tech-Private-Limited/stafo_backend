@extends('user.layouts.app')
@section('title', 'Package List')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <style>
        /* Custom Styling */
        body {
            font-family: 'Arial', sans-serif;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e0e0e0;
            padding: 15px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .card-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .table th {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 12px;
        }

        .table td {
            text-align: center;
            padding: 12px;
        }

        .badge-info {
            background-color: #17a2b8;
            font-size: 0.85rem;
        }

        .table-responsive {
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        .container {
            padding: 40px 20px;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .card-body {
            padding: 25px;
        }

        .arrow {
            font-weight: bold;
            font-size: 1.1rem;
            margin-right: 5px;
        }

        .btn-outline-dark {
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            border: 2px solid #343a40;
            color: #343a40;
            font-weight: bold;
            margin-top: 15px;
        }

        .btn-outline-dark:hover {
            background-color: #343a40;
            color: white;
            border: 2px solid transparent;
        }

        /* Owl Carousel Styling */
        .owl-carousel .item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .owl-carousel .item:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .owl-carousel .item .card {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-title {
                font-size: 1.5rem;
            }

            .container {
                padding: 20px;
            }

            .table th,
            .table td {
                padding: 8px;
            }

            .owl-carousel .item {
                margin: 10px 0;
            }
        }
    </style>
@endsection

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0 emp-data">
        <div class="border-bottom">
            <h3 class=" card-title">Package & Pricing </h3>
        </div>

        <div class="container py-4">
            <div class="row">
                <div class="col-md-12 carousel-packages">
                    <!-- Pricing Section -->
                    <div class="owl-carousel owl-theme text-center owl-custom">
                        @foreach ($packages as $package)
                            <div class="item">
                                <div class="card border-0 shadow">

                                    <div class="card-header bg-dark text-white rounded-top"></div>

                                    <div class="card-body">
                                        <h2 class="card-title h4">{{ $package->package_name }}</h2>
                                        <p class="card-text text-muted">{!! $package->description !!}</p>

                                        <p class="display-6 fw-bold">₹{{ number_format($package->discount_price, 2) }}</p>
                                        
                                            <p class="text-muted"> MRP: 
                                               <span style="text-decoration: line-through"> ₹{{ number_format($package->price, 2) }} </span></p>
                                        

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
                                        
                                        @php
                                        // PayU credentials
                                            $merchantKey = '1AJhSD'; // Replace with your merchant key
                                            $salt = 'tBjCq35cgf3f12ya0usuhEtH9IJ7pSyq'; // Replace with your salt key

                                            // Collect user inputs from the form
                                            $firstname = $company->company_name;  // Example first name
                                            $email = $company->email;
                                            $amount = $package->discount_price ? $package->discount_price : $package->price;  // Amount to be charged
                                            $phone = $company->mobile_no;  // User's phone number
                                            $productinfo = $package->package_name;  // Example product info
                                            $txnid = uniqid();  // Transaction ID
                                            $duration = $package->days;  // Duration in days
                                            $user_id = $company->id;  // User ID
                                            $package_id = $package->id;

                                            // Prepare the hash string for PayU
                                            $hash_string = $merchantKey . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' . $firstname . '|' . $email .'|'.$user_id.'|'.$duration.'|'.$package_id.'||||||||' . $salt;

                                            // Generate the hash using SHA-512
                                            $hash = hash('sha512', $hash_string);

                                            // Payment gateway URL (Test Environment)
                                            $payuUrl = "https://secure.payu.in/_payment"; // For live 

                                            //$payuUrl = "https://test.payu.in/_payment"; // For test

                                            // Prepare form data to send to PayU
                                            $formData = [
                                                'key' => $merchantKey,
                                                'txnid' => $txnid,
                                                'amount' => $amount,
                                                'productinfo' => $productinfo,
                                                'firstname' => $firstname,
                                                'email' => $email,
                                                'phone' => $phone,
                                                'udf1' => $user_id,
                                                'udf2' => $duration,
                                                'udf3' => $package_id,
                                                'surl' => url('success'),  // Success URL
                                                'furl' => url('failure'),  // Failure URL
                                                'hash' => $hash
                                            ];

                                        @endphp                                        
                                        <form action="{{ $payuUrl }}" method="post" name="payuForm">
                                            <?php
                                            foreach ($formData as $key => $value) {
                                                echo "<input type='hidden' name='$key' value='$value' />";
                                            }
                                            ?>
                                            <button type="submit" class="btn btn-outline-theme">Get it now</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Compare Features Section -->
                    <div class="mt-5">
                        <h2 class="text-center">Compare Features</h2>
                        <div class="table-responsive table-container mt-4 custom-table">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="sticky-column border-top">Core Features</th>
                                        @foreach ($packages as $package)
                                            <th>{{ $package->package_name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td class="sticky-column d-flex gap-1 align-items-baseline">
                                            <span class="arrow"> > </span><span>Price</span>
                                        </td>
                                        @foreach ($packages as $package)
                                            <td>₹{{ number_format($package->price, 2) }}</td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        <td class="sticky-column d-flex gap-1 align-items-baseline">
                                            <span class="arrow"> > </span><span>Duration</span>
                                        </td>
                                        @foreach ($packages as $package)
                                            <td>
                                                @if ($package->days < 30)
                                                    {{ $package->days }} days
                                                @else
                                                    {{ round($package->days / 30) }} months
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>

                                    <!-- Loop through features dynamically -->
                                    @foreach ($packages[0]->features as $feature)
                                        <tr>
                                            <td class="sticky-column d-flex gap-1 align-items-baseline">
                                                <span class="arrow"> > </span><span>{{ $feature->name }}</span>
                                            </td>

                                            @foreach ($packages as $package)
                                                <td>
                                                    @foreach ($package->features as $packageFeature)
                                                        @if ($packageFeature->id == $feature->id)
                                                            {{ $packageFeature->pivot->feature_value }}
                                                        @endif
                                                    @endforeach
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
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