@extends('frontend.layouts.master')
@section('title', 'Contact Us | STAFO - Leading HRMS Solution Provider in India')
@section('heading', 'Contact Us')

@section('css')
<style>
  :root {
    --stafo-primary: #1d274b;
    --stafo-accent: #4361ee;
    --stafo-accent-light: #eef2ff;
    --stafo-dark: #0f172a;
    --stafo-muted: #64748b;
    --stafo-border: #e2e8f0;
    --stafo-bg-soft: #f8fafc;
    --stafo-radius: 20px;
  }

  .stafo-contact-section {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #ffffff;
    padding: 50px 0 90px;
    color: var(--stafo-dark);
  }

  /* Eyebrow badge */
  .stafo-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 18px;
    border-radius: 999px;
    background: var(--stafo-accent-light);
    color: var(--stafo-accent);
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 15px;
  }

  .contact-main-heading {
    font-size: clamp(2rem, 3.8vw, 2.75rem);
    font-weight: 800;
    line-height: 1.25;
    color: var(--stafo-primary);
    margin-bottom: 12px;
  }

  .contact-main-heading .highlight {
    color: var(--stafo-accent);
    position: relative;
    display: inline-block;
  }

  .contact-lead-sub {
    font-size: 1.05rem;
    color: var(--stafo-muted);
    line-height: 1.65;
    max-width: 620px;
  }

  /* Form Card Styling */
  .stafo-form-card {
    background: #ffffff;
    border: 1px solid var(--stafo-border);
    border-radius: var(--stafo-radius);
    padding: 40px 36px;
    box-shadow: 0 20px 45px rgba(29, 39, 75, 0.07);
    position: relative;
    transition: box-shadow 0.3s ease;
  }

  .stafo-form-card:hover {
    box-shadow: 0 25px 60px rgba(29, 39, 75, 0.11);
  }

  .stafo-field-group {
    margin-bottom: 20px;
  }

  .stafo-field-label {
    display: block;
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--stafo-dark);
    margin-bottom: 8px;
  }

  .stafo-field-wrap {
    position: relative;
    display: flex;
    align-items: center;
  }

  .stafo-field-icon {
    position: absolute;
    left: 16px;
    color: #94a3b8;
    font-size: 1.05rem;
    pointer-events: none;
    transition: color 0.2s ease;
  }

  .stafo-control-input {
    width: 100%;
    height: 52px;
    border-radius: 12px;
    border: 1.5px solid var(--stafo-border);
    padding: 12px 16px 12px 46px;
    font-size: 0.95rem;
    color: var(--stafo-dark);
    background: var(--stafo-bg-soft);
    transition: all 0.25s ease;
  }

  .stafo-control-input:focus,
  .stafo-control-textarea:focus {
    background: #ffffff;
    border-color: var(--stafo-accent);
    outline: none;
    box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.12);
  }

  .stafo-control-input:focus + .stafo-field-icon,
  .stafo-field-wrap:focus-within .stafo-field-icon {
    color: var(--stafo-accent);
  }

  .stafo-control-textarea {
    width: 100%;
    border-radius: 12px;
    border: 1.5px solid var(--stafo-border);
    padding: 14px 16px 14px 46px;
    font-size: 0.95rem;
    color: var(--stafo-dark);
    background: var(--stafo-bg-soft);
    transition: all 0.25s ease;
    resize: vertical;
    min-height: 120px;
  }

  .stafo-submit-btn {
    width: 100%;
    height: 54px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1d274b 0%, #4361ee 100%);
    color: #ffffff;
    border: none;
    font-size: 1.02rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
    box-shadow: 0 10px 25px rgba(67, 97, 238, 0.25);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .stafo-submit-btn:hover {
    background: linear-gradient(135deg, #141c38 0%, #304ecc 100%);
    transform: translateY(-2px);
    box-shadow: 0 15px 35px rgba(67, 97, 238, 0.35);
    color: #ffffff;
  }

  .stafo-submit-btn:active {
    transform: translateY(0);
  }

  /* Right Side Info Cards */
  .stafo-info-card {
    background: #ffffff;
    border: 1px solid var(--stafo-border);
    border-radius: 16px;
    padding: 22px 24px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 18px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    transition: all 0.25s ease;
  }

  .stafo-info-card:hover {
    transform: translateY(-3px);
    border-color: rgba(67, 97, 238, 0.3);
    box-shadow: 0 12px 25px rgba(67, 97, 238, 0.08);
  }

  .stafo-info-icon-box {
    width: 52px;
    height: 52px;
    min-width: 52px;
    border-radius: 14px;
    background: var(--stafo-accent-light);
    color: var(--stafo-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
  }

  .stafo-info-label {
    font-size: 0.76rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--stafo-muted);
    margin-bottom: 3px;
  }

  .stafo-info-value {
    font-size: 0.98rem;
    font-weight: 700;
    color: var(--stafo-dark);
    margin: 0;
    text-decoration: none;
    line-height: 1.4;
  }

  a.stafo-info-value:hover {
    color: var(--stafo-accent);
  }

  /* Map Container */
  .stafo-map-card {
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--stafo-border);
    box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    height: 240px;
    margin-top: 18px;
  }

  .stafo-map-card iframe {
    width: 100%;
    height: 100%;
    border: none;
  }

  @media (max-width: 767.98px) {
    .stafo-form-card {
      padding: 28px 20px;
    }
  }
</style>
@endsection

@section('content')
<div class="stafo-contact-section">
  <div class="container">
    
    <!-- Top Header -->
    <div class="row justify-content-center text-center mb-5">
      <div class="col-lg-8">
        <div class="stafo-badge-pill">
          <i class="fa-solid fa-headset"></i> Connect With Us
        </div>
        <h1 class="contact-main-heading">
          Get in Touch with <span class="highlight">STAFO Experts</span>
        </h1>
        <p class="contact-lead-sub mx-auto">
          Looking to streamline your payroll, track live field attendance, or need a personalized product demo? We’re here to help you scale your workforce operations.
        </p>
      </div>
    </div>

    <!-- Main 2-Column Section -->
    <div class="row g-4 g-lg-5 align-items-start">
      
      <!-- Left: Contact Form Card -->
      <div class="col-12 col-lg-7">
        <div class="stafo-form-card">
          
          <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
              <h4 class="fw-bold mb-1" style="color: var(--stafo-primary);">Send Us a Message</h4>
              <p class="text-muted small mb-0">Fill in the form below and we'll connect with you shortly.</p>
            </div>
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill small fw-semibold">
              <i class="fa-solid fa-bolt me-1"></i> Quick Response
            </span>
          </div>

          <!-- Success Alert (Hidden by default) -->
          <div id="contactSuccessAlert" class="alert alert-success align-items-center gap-2 mb-4 p-3 rounded-3 shadow-sm border-0 d-none">
            <i class="fa-solid fa-circle-check fs-4 text-success"></i>
            <div>
              <strong>Message Sent!</strong>
              <div id="contactSuccessText" class="small">Your inquiry has been submitted successfully. Our team will contact you shortly.</div>
            </div>
          </div>

          <form id="stafoContactForm" method="post" action="{{ route('contact.submit') }}">
            @csrf
            <div class="row g-3">
              
              <!-- Full Name -->
              <div class="col-12">
                <div class="stafo-field-group mb-0">
                  <label class="stafo-field-label" for="contact_name">
                    Full Name <span class="text-danger">*</span>
                  </label>
                  <div class="stafo-field-wrap">
                    <input type="text" class="stafo-control-input" id="contact_name" name="full_name" required placeholder="e.g. Rahul Sharma">
                    <i class="fa-solid fa-user stafo-field-icon"></i>
                  </div>
                  <span id="error-full_name" class="text-danger small mt-1 d-none"></span>
                </div>
              </div>

              <!-- Corporate Email -->
              <div class="col-12 col-md-6">
                <div class="stafo-field-group mb-0">
                  <label class="stafo-field-label" for="contact_email">
                    Corporate Email <span class="text-danger">*</span>
                  </label>
                  <div class="stafo-field-wrap">
                    <input type="email" class="stafo-control-input" id="contact_email" name="email" required placeholder="rahul@company.com">
                    <i class="fa-solid fa-envelope stafo-field-icon"></i>
                  </div>
                  <span id="error-email" class="text-danger small mt-1 d-none"></span>
                </div>
              </div>

              <!-- Phone Number -->
              <div class="col-12 col-md-6">
                <div class="stafo-field-group mb-0">
                  <label class="stafo-field-label" for="contact_phone">
                    Phone Number <span class="text-danger">*</span>
                  </label>
                  <div class="stafo-field-wrap">
                    <input type="tel" class="stafo-control-input" id="contact_phone" name="phone" required
                           placeholder="10-digit mobile number"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                           maxlength="10">
                    <i class="fa-solid fa-phone stafo-field-icon"></i>
                  </div>
                  <span id="error-phone" class="text-danger small mt-1 d-none"></span>
                </div>
              </div>

              <!-- Message -->
              <div class="col-12">
                <div class="stafo-field-group mb-0">
                  <label class="stafo-field-label" for="contact_message">
                    How Can We Help You?
                  </label>
                  <div class="stafo-field-wrap">
                    <textarea class="stafo-control-textarea" id="contact_message" name="message" rows="4" 
                              placeholder="Tell us about your organization size, payroll needs, attendance tracking, or request a custom demo..."></textarea>
                    <i class="fa-solid fa-comment-dots stafo-field-icon" style="top: 18px;"></i>
                  </div>
                  <span id="error-message" class="text-danger small mt-1 d-none"></span>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="col-12 mt-3">
                <button type="submit" class="stafo-submit-btn" id="btnStafoSubmit">
                  <span id="btnSubmitText">Send Message</span>
                  <i class="fa-solid fa-paper-plane" id="btnSubmitIcon"></i>
                </button>
              </div>

            </div>
          </form>

          <!-- Privacy & Trust Footer -->
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-4 pt-3 border-top text-muted" style="font-size: 0.84rem;">
            <div class="d-flex align-items-center gap-2">
              <i class="fa-solid fa-shield-halved text-success"></i>
              <span>100% Data Confidentiality & ISO Privacy Standards</span>
            </div>
            <div class="text-primary fw-semibold">
              <i class="fa-regular fa-clock me-1"></i> Responds within 2 hrs
            </div>
          </div>

        </div>
      </div>

      <!-- Right: Contact Information & Google Map -->
      <div class="col-12 col-lg-5">
        
        <!-- Headquarters Card -->
        <div class="stafo-info-card">
          <div class="stafo-info-icon-box">
            <i class="fa-solid fa-location-dot"></i>
          </div>
          <div>
            <div class="stafo-info-label">HEADQUARTERS</div>
            <div class="stafo-info-value">Jadavpur, Kolkata – 700032, West Bengal, India</div>
          </div>
        </div>

        <!-- Email Card -->
        <div class="stafo-info-card">
          <div class="stafo-info-icon-box">
            <i class="fa-solid fa-envelope"></i>
          </div>
          <div>
            <div class="stafo-info-label">EMAIL SUPPORT</div>
            <a href="mailto:sales@stafo.in" class="stafo-info-value">sales@stafo.in</a>
            <div class="text-muted small mt-1">24/7 Dedicated Email Assistance</div>
          </div>
        </div>

        <!-- Phone Card -->
        <div class="stafo-info-card">
          <div class="stafo-info-icon-box">
            <i class="fa-solid fa-phone"></i>
          </div>
          <div>
            <div class="stafo-info-label">CALL SALES & SUPPORT</div>
            <a href="tel:+918389039361" class="stafo-info-value">+91 8389039361</a>
            <div class="text-muted small mt-1">Mon – Sat: 9:30 AM – 7:00 PM IST</div>
          </div>
        </div>

        <!-- Live Google Map -->
        <div class="stafo-map-card">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3686.1309313124902!2d88.35932567590132!3d22.499270235652848!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a0270d8ba2c5a91%3A0x4bc4e2ceb3d31cb4!2sF%2F28%2F1A%2C%20Katju%20Nagar%2C%20Jadavpur%2C%20Kolkata%2C%20West%20Bengal%20700032!5e0!3m2!1sen!2sin!4v1749197584620!5m2!1sen!2sin"
            allowfullscreen="" 
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="STAFO Headquarters Location">
          </iframe>
        </div>

      </div>

    </div>

    <!-- Bottom Help Banner -->
    <div class="card border-0 rounded-4 p-4 mt-5 shadow-sm" style="background: linear-gradient(135deg, #1d274b 0%, #304179 100%); color: #ffffff;">
      <div class="row align-items-center g-3">
        <div class="col-md-8 text-center text-md-start">
          <h4 class="fw-bold mb-1" style="color: #ffffff !important;">Looking for Instant Answers or Free Trial?</h4>
          <p class="mb-0 small" style="color: rgba(255, 255, 255, 0.85) !important;">Explore our interactive FAQ guide or start your 15-day risk-free trial today.</p>
        </div>
        <div class="col-md-4 text-center text-md-end">
          <a href="{{ route('faq') }}" class="btn btn-outline-light rounded-pill px-4 me-2 btn-sm" style="border-color: rgba(255, 255, 255, 0.6); color: #ffffff !important;">
            <i class="fa-solid fa-circle-question me-1"></i> View FAQs
          </a>
          <a href="{{ route('price') }}" class="btn btn-primary rounded-pill px-4 btn-sm" style="background-color: #4361ee; border-color: #4361ee; color: #ffffff !important;">
            Pricing Plans
          </a>
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

        $('#stafoContactForm').on('submit', function (e) {
            e.preventDefault();

            let $btn = $('#btnStafoSubmit');
            let $btnText = $('#btnSubmitText');
            let $btnIcon = $('#btnSubmitIcon');

            // Set loading state
            $btn.prop('disabled', true);
            $btnText.text('Sending inquiry...');
            $btnIcon.attr('class', 'fa-solid fa-spinner fa-spin');

            // Clear previous errors
            $('span[id^="error-"]').text('').addClass('d-none');

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ route('contact.submit') }}',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function (response) {
                    $btn.prop('disabled', false);
                    $btnText.text('Send Message');
                    $btnIcon.attr('class', 'fa-solid fa-paper-plane');

                    // Show success message
                    $('#contactSuccessText').text(response.success || 'Your inquiry has been submitted successfully! Our team will contact you shortly.');
                    $('#contactSuccessAlert').removeClass('d-none').addClass('d-flex').hide().fadeIn();

                    // SweetAlert confirmation
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Inquiry Submitted!',
                            text: response.success || 'Thank you for contacting STAFO. We will get back to you shortly.',
                            confirmButtonColor: '#4361ee',
                            confirmButtonText: 'Great, thank you!'
                        });
                    }

                    // Reset form
                    $('#stafoContactForm')[0].reset();

                    setTimeout(function () {
                        $('#contactSuccessAlert').fadeOut(function() {
                            $(this).removeClass('d-flex').addClass('d-none');
                        });
                    }, 8000);
                },
                error: function (xhr) {
                    $btn.prop('disabled', false);
                    $btnText.text('Send Message');
                    $btnIcon.attr('class', 'fa-solid fa-paper-plane');

                    let errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : {};
                    
                    if (errors.full_name) {
                        $('#error-full_name').text(errors.full_name[0]).removeClass('d-none');
                    }
                    if (errors.email) {
                        $('#error-email').text(errors.email[0]).removeClass('d-none');
                    }
                    if (errors.phone) {
                        $('#error-phone').text(errors.phone[0]).removeClass('d-none');
                    }
                    if (errors.message) {
                        $('#error-message').text(errors.message[0]).removeClass('d-none');
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Error',
                            text: 'Please check the highlighted fields and try again.',
                            confirmButtonColor: '#d33'
                        });
                    }
                }
            });
        });
    });
</script>
@endsection