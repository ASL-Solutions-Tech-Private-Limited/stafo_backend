@extends('frontend.layouts.master')
@section('title', 'Contact Us | STAFO - Leading HRMS Solution Provider in India')
@section('heading', 'Contact Us')

@section('css')
<style>
  :root {
    --primary: #424096;
    --primary2: #5b57c7;
    --primary-soft: #f0efff;
    --ink: #17172a;
    --muted: #68687a;
    --soft: #f6f6fb;
    --line: #e7e7f0;
    --white: #ffffff;
    --green: #16a36a;
    --shadow-sm: 0 4px 20px rgba(66, 64, 150, 0.06);
    --shadow-md: 0 12px 35px rgba(66, 64, 150, 0.10);
    --shadow-lg: 0 24px 60px rgba(45, 42, 110, 0.14);
    --radius-sm: 12px;
    --radius-md: 18px;
    --radius-lg: 24px;
  }

  .stafo-contact-page {
    font-family: 'Plus Jakarta Sans', Inter, -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--ink);
    background: #ffffff;
    padding: 60px 0 80px;
  }

  /* Pill Eyebrow */
  .stafo-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 18px;
    border-radius: 999px;
    background: var(--primary-soft);
    color: var(--primary);
    border: 1px solid rgba(66, 64, 150, 0.16);
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    margin-bottom: 16px;
  }

  .contact-title {
    font-size: clamp(2rem, 3.5vw, 2.8rem);
    font-weight: 800;
    line-height: 1.2;
    color: var(--ink);
    margin-bottom: 14px;
    letter-spacing: -0.02em;
  }

  .contact-title .gradient-text {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary2) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .contact-sub {
    font-size: 1.05rem;
    color: var(--muted);
    line-height: 1.65;
    margin-bottom: 35px;
    max-width: 580px;
  }

  /* Left Form Card */
  .contact-form-card {
    background: #ffffff;
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    padding: 42px 38px;
    box-shadow: var(--shadow-md);
    position: relative;
  }

  .form-label-custom {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .input-icon-wrap {
    position: relative;
    display: flex;
    align-items: center;
  }

  .input-icon-wrap i {
    position: absolute;
    left: 16px;
    color: #94a3b8;
    font-size: 1rem;
    pointer-events: none;
    transition: color 0.2s ease;
  }

  .stafo-input {
    width: 100%;
    height: 52px;
    border-radius: 12px;
    border: 1.5px solid var(--line);
    padding: 12px 16px 12px 46px;
    font-size: 0.96rem;
    color: var(--ink);
    background: var(--soft);
    transition: all 0.25s ease;
  }

  .stafo-input:focus {
    background: #ffffff;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(66, 64, 150, 0.12);
    outline: none;
  }

  .stafo-input:focus ~ i {
    color: var(--primary);
  }

  .stafo-textarea {
    width: 100%;
    border-radius: 12px;
    border: 1.5px solid var(--line);
    padding: 14px 16px;
    font-size: 0.96rem;
    color: var(--ink);
    background: var(--soft);
    transition: all 0.25s ease;
    resize: vertical;
    min-height: 120px;
  }

  .stafo-textarea:focus {
    background: #ffffff;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(66, 64, 150, 0.12);
    outline: none;
  }

  .error-text {
    font-size: 0.8rem;
    color: #dc2626;
    margin-top: 5px;
    display: block;
    font-weight: 600;
  }

  .btn-submit-contact {
    height: 52px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary2) 100%);
    color: #ffffff;
    font-weight: 700;
    font-size: 1rem;
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(66, 64, 150, 0.3);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 0 32px;
    cursor: pointer;
    width: 100%;
  }

  .btn-submit-contact:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(66, 64, 150, 0.4);
    color: #ffffff;
  }

  /* Right Side Info Cards */
  .contact-info-card {
    background: #ffffff;
    border: 1px solid var(--line);
    border-radius: var(--radius-md);
    padding: 22px 20px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 18px;
    transition: all 0.25s ease;
  }

  .contact-info-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
    border-color: rgba(66, 64, 150, 0.3);
  }

  .contact-icon-circle {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
  }

  .contact-info-label {
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 700;
    color: var(--muted);
    margin-bottom: 3px;
  }

  .contact-info-val {
    font-size: 1rem;
    font-weight: 700;
    color: var(--ink);
    margin: 0;
    text-decoration: none;
  }

  a.contact-info-val:hover {
    color: var(--primary);
  }

  /* Map Container */
  .contact-map-wrap {
    border-radius: var(--radius-md);
    overflow: hidden;
    border: 1px solid var(--line);
    box-shadow: var(--shadow-sm);
    margin-top: 24px;
  }

  .contact-map-wrap iframe {
    width: 100%;
    height: 270px;
    border: 0;
    display: block;
  }

  @media (max-width: 991.98px) {
    .contact-form-card {
      padding: 30px 22px;
    }
  }
</style>
@endsection

@section('content')
<div class="stafo-contact-page">
  <div class="container">
    
    <!-- Top Heading -->
    <div class="row justify-content-center text-center mb-5">
      <div class="col-lg-8">
        <div class="stafo-pill">
          <i class="fa-solid fa-headset"></i> We're Here For You
        </div>
        <h1 class="contact-title">
          Get in Touch with <span class="gradient-text">STAFO Support</span>
        </h1>
        <p class="contact-sub mx-auto">
          Have questions about our HRMS, pricing plans, biometrics integration, or need a personalized product walkthrough? Our team is always ready to assist.
        </p>
      </div>
    </div>

    <!-- Main 2-Column Section -->
    <div class="row g-5 align-items-start">
      
      <!-- Left: Contact Form Card -->
      <div class="col-12 col-lg-7">
        <div class="contact-form-card">
          
          <div id="successMessage" style="display:none;" class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="fa-solid fa-circle-check fs-5"></i>
            <span>Your message has been sent successfully! Our team will contact you shortly.</span>
          </div>

          <form id="contactForm" method="post" action="#">
            @csrf
            <div class="row g-3">
              
              <!-- Full Name -->
              <div class="col-12">
                <label class="form-label-custom" for="name">
                  <span>Full Name</span> <span class="text-danger">*</span>
                </label>
                <div class="input-icon-wrap">
                  <input type="text" class="stafo-input" id="name" name="full_name" required placeholder="e.g. Rahul Sharma">
                  <i class="fa-solid fa-user"></i>
                </div>
                <span id="name-error" class="error-text d-none"></span>
              </div>

              <!-- Corporate Email -->
              <div class="col-12 col-md-6">
                <label class="form-label-custom" for="email">
                  <span>Corporate Email</span> <span class="text-danger">*</span>
                </label>
                <div class="input-icon-wrap">
                  <input type="email" class="stafo-input" id="email" name="email" required placeholder="name@company.com">
                  <i class="fa-solid fa-envelope"></i>
                </div>
                <span id="email-error" class="error-text d-none"></span>
              </div>

              <!-- Phone Number -->
              <div class="col-12 col-md-6">
                <label class="form-label-custom" for="phone">
                  <span>Phone Number</span> <span class="text-danger">*</span>
                </label>
                <div class="input-icon-wrap">
                  <input type="text" class="stafo-input" id="phone" name="phone" required
                         placeholder="10-digit mobile"
                         oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                         maxlength="10">
                  <i class="fa-solid fa-phone"></i>
                </div>
                <span id="phone-error" class="error-text d-none"></span>
              </div>

              <!-- Message -->
              <div class="col-12">
                <label class="form-label-custom" for="message">
                  <span>Your Message / Query</span>
                </label>
                <textarea class="stafo-textarea" id="message" name="message" rows="4" placeholder="Tell us about your organization, team size, or specific HR features you're looking for..."></textarea>
                <span id="message-error" class="error-text d-none"></span>
              </div>

              <!-- Submit Button -->
              <div class="col-12 mt-4">
                <button type="submit" class="btn-submit-contact" id="btnContactSubmit">
                  <span>Send Message</span>
                  <i class="fa-solid fa-paper-plane"></i>
                </button>
              </div>

            </div>
          </form>

          <div class="d-flex align-items-center gap-2 mt-4 text-muted" style="font-size: 0.82rem;">
            <i class="fa-solid fa-shield-halved text-success"></i>
            <span>We value your privacy. No spam. Typical response time is under 2 business hours.</span>
          </div>

        </div>
      </div>

      <!-- Right: Contact Information & Google Map -->
      <div class="col-12 col-lg-5">
        
        <!-- Office Card -->
        <div class="contact-info-card">
          <div class="contact-icon-circle">
            <i class="fa-solid fa-location-dot"></i>
          </div>
          <div>
            <div class="contact-info-label">Headquarters</div>
            <div class="contact-info-val">Jadavpur, Kolkata - 700032, West Bengal, India</div>
          </div>
        </div>

        <!-- Email Card -->
        <div class="contact-info-card">
          <div class="contact-icon-circle">
            <i class="fa-solid fa-envelope-open-text"></i>
          </div>
          <div>
            <div class="contact-info-label">Email Support</div>
            <a href="mailto:support@stafo.com" class="contact-info-val">support@stafo.com</a>
          </div>
        </div>

        <!-- Phone Card -->
        <div class="contact-info-card">
          <div class="contact-icon-circle">
            <i class="fa-solid fa-phone-volume"></i>
          </div>
          <div>
            <div class="contact-info-label">Call Sales & Support</div>
            <a href="tel:+916292252470" class="contact-info-val">+91 6292252470</a>
          </div>
        </div>

        <!-- Live Google Map -->
        <div class="contact-map-wrap">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3686.1309313124902!2d88.35932567590132!3d22.499270235652848!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a0270d8ba2c5a91%3A0x4bc4e2ceb3d31cb4!2sF%2F28%2F1A%2C%20Katju%20Nagar%2C%20Jadavpur%2C%20Kolkata%2C%20West%20Bengal%20700032!5e0!3m2!1sen!2sin!4v1749197584620!5m2!1sen!2sin"
            allowfullscreen="" 
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>

      </div>

    </div>

  </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        var token = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';

        $('#contactForm').on('submit', function (e) {
            e.preventDefault();

            let $btn = $('#btnContactSubmit');
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Sending...');

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ route('contact.submit') }}',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function (response) {
                    $btn.prop('disabled', false).html('<span>Send Message</span> <i class="fa-solid fa-paper-plane"></i>');
                    $('#successMessage').text(response.success || 'Your message has been sent successfully! Our team will contact you shortly.');
                    $('#successMessage').fadeIn();
                    setTimeout(function () {
                        $('#successMessage').fadeOut();
                    }, 6000);
                    $('#contactForm')[0].reset();
                    $('span[id$="-error"]').text('').addClass('d-none');
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html('<span>Send Message</span> <i class="fa-solid fa-paper-plane"></i>');
                    let errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : {};
                    
                    if (errors.full_name) {
                        $('#name-error').text(errors.full_name[0]).removeClass('d-none');
                    } else {
                        $('#name-error').addClass('d-none');
                    }

                    if (errors.email) {
                        $('#email-error').text(errors.email[0]).removeClass('d-none');
                    } else {
                        $('#email-error').addClass('d-none');
                    }

                    if (errors.phone) {
                        $('#phone-error').text(errors.phone[0]).removeClass('d-none');
                    } else {
                        $('#phone-error').addClass('d-none');
                    }

                    if (errors.message) {
                        $('#message-error').text(errors.message[0]).removeClass('d-none');
                    } else {
                        $('#message-error').addClass('d-none');
                    }
                }
            });
        });
    });
</script>
@endsection