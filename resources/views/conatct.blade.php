@extends('frontend.layouts.master')
@section('title', 'Contact Us | STAFO - Leading HRMS Solution Provider in India')
@section('heading', 'Contact Us')

@section('content')
<section class="p-0">
        <div class="container">
          <div class="row">
            <div class="col-12 col-lg-7">
              <div>
                <div>
                  <h2><span class="font-w-4 d-block">Get in Touch</span> with STAFO Support</h2>
                  <p class="lead">Have questions about our HRMS solution? We're here to help!</p>
                </div>
                <div id="successMessage" style="display:none;" class="alert alert-success">
                                Your message has been sent successfully!
                            </div>
                <form id="contactForm" class="row" method="post" action="#">
                  <div class="messages"></div>
                  <div class="form-group col-md-12">
                    
                    <input type="text" class="form-control" id="name" name="full_name" required placeholder="Your full name">
                    <div class="help-block with-errors"></div>
                  </div>
                  <div class="form-group col-md-6">
                    <input type="email" class="form-control" id="email" name="email" required
                                        placeholder="Your email address">
                    <div class="help-block with-errors"></div>
                  </div>
                  <div class="form-group col-md-6">
                    <input type="text" class="form-control" id="phone" name="phone" required
                                        placeholder="Your phone number"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                        maxlength="10">
                    <div class="help-block with-errors"></div>
                  </div>
                  
                  <div class="form-group col-md-12">
                    <textarea class="form-control" id="message" name="message" rows="4"
                                        placeholder="Your message"></textarea>
                    <div class="help-block with-errors"></div>
                  </div>
                  <div class="col mt-4">
                    <button class="btn btn-primary">Send Message</button>
                  </div>
                </form>
                <div class="mt-4">
                  <p class="text-muted small">* Our support team typically responds within 24 hours during business
                    days.</p>
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-4 ms-auto mt-5 mt-lg-0">
              <div class="d-flex align-items-center bg-white p-3 shadow-sm rounded mb-3">
                <div class="me-3">
                  <div class="f-icon-s p-3 rounded" data-bg-color="#d0faec"> <i class="flaticon-location"></i>
                  </div>
                </div>
                <div>
                  <h5 class="mb-1">Visit Us:</h5>
                  <span class="text-black">Jadavpur, Kolkata-700032, India</span>
                </div>
              </div>
              <div class="d-flex align-items-center bg-white p-3 shadow-sm rounded mb-3">
                <div class="me-3">
                  <div class="f-icon-s p-3 rounded" data-bg-color="#d0faec"> <i class="flaticon-mail"></i>
                  </div>
                </div>
                <div>
                  <h5 class="mb-1">Email Support:</h5>
                  <a class="btn-link" href="mailto:support@stafo.com">support@stafo.com</a>
                </div>
              </div>
              <div class="d-flex align-items-center bg-white p-3 shadow-sm rounded">
                <div class="me-3">
                  <div class="f-icon-s p-3 rounded" data-bg-color="#d0faec"> <i class="flaticon-telephone"></i>
                  </div>
                </div>
                <div>
                  <h5 class="mb-1">Call Us:</h5>
                  <a class="btn-link" href="tel:+916292252470">+91 6292252470</a>
                </div>
              </div>

              <div class="map h-50 mt-5">
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3686.1309313124902!2d88.35932567590132!3d22.499270235652848!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a0270d8ba2c5a91%3A0x4bc4e2ceb3d31cb4!2sF%2F28%2F1A%2C%20Katju%20Nagar%2C%20Jadavpur%2C%20Kolkata%2C%20West%20Bengal%20700032!5e0!3m2!1sen!2sin!4v1749197584620!5m2!1sen!2sin"
                  width="500" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"></iframe>
              </div>
            </div>
          </div>
        </div>
      </section>
@endsection

@section('js')
 
    <script>
        $(document).ready(function () {
            var token = $('meta[name="csrf-token"]').attr('content');

            $('#contactForm').on('submit', function (e) {
                e.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: '{{ route('contact.submit') }}',
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    success: function (response) {
                        $('#successMessage').text(response.success);
                        $('#successMessage').show();
                        setTimeout(function () {
                            $('#successMessage').fadeOut();
                        }, 5000);
                        $('#contactForm')[0].reset();
                        $('span[id$="-error"]').text('');
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        // Check and show/hide error messages for each field
                        if (errors.full_name) {
                            $('#name-error').text(errors.full_name[0]).addClass('error-show')
                                .removeClass('d-none');
                        } else {
                            $('#name-error').removeClass('error-show').addClass('d-none');
                        }

                        if (errors.email) {
                            $('#email-error').text(errors.email[0]).addClass('error-show')
                                .removeClass('d-none');
                        } else {
                            $('#email-error').removeClass('error-show').addClass('d-none');
                        }

                        if (errors.phone) {
                            $('#phone-error').text(errors.phone[0]).addClass('error-show')
                                .removeClass('d-none');
                        } else {
                            $('#phone-error').removeClass('error-show').addClass('d-none');
                        }

                        if (errors.message) {
                            $('#message-error').text(errors.message[0]).addClass('error-show')
                                .removeClass('d-none');
                        } else {
                            $('#message-error').removeClass('error-show').addClass('d-none');
                        }

                        // $('html, body').animate({
                        //     scrollTop: $('.text-danger').first().offset().top - 0
                        // }, 500);
                    }
                });
            });
        });
    </script>
@endsection