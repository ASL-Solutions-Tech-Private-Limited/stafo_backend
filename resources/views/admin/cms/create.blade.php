@extends('admin.layouts.layout')
@section('title', 'User Add')
@section('content')
    <div class="app-main__outer">

        <div class="app-main__inner">
            <div class="d-flex justify-content-between user-access">
                <div class="user-welcome mb-3">
                    <h3>Add New</h3>
                </div>
                <!-- <button class="btn primary-bg"><i class="fa-solid fa-plus"></i> Add New User</button> -->
            </div>
            <form action="{{ route('cms.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="new-user-form ">
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-6 mb-3">
                            <label for="title">Title<span class="red">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Enter Title"
                                required>
                            <span class="error error-title"><span>
                        </div>

                        <!-- Short Description -->
                        <div class="col-md-6 mb-3">
                            <label for="short_description">Short Description</label>
                            <textarea name="short_description" id="short_description" class="form-control"
                                placeholder="Enter Short Description" rows="1"></textarea>
                            <span class="error error-short-description"><span>
                        </div>

                        <!-- Meta Title -->
                        <div class="col-md-6 mb-3">
                            <label for="meta_title">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control"
                                placeholder="Enter Meta Title">
                            <span class="error error-meta-title"><span>
                        </div>

                        <!-- Meta Description -->
                        <div class="col-md-6 mb-3">
                            <label for="meta_description">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" class="form-control"
                                placeholder="Enter Meta Description" rows="1"></textarea>
                            <span class="error error-meta-description"><span>
                        </div>

                        <!-- Long Description -->
                        <div class="col-md-12 mb-3">
                            <label for="long_description">Long Description</label>
                            <textarea name="long_description" id="long_description" class="form-control"
                                placeholder="Enter Long Description" rows="5"></textarea>
                            <span class="error error-long-description"><span>
                        </div>



                        <!-- Image -->
                        <div class="col-md-12 mb-3">
                            <label for="image">Image</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            <span class="error error-image"><span>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('cms.list') }}">
                                <button type="button" class="btn bg-danger text-white">Cancel</button>
                            </a>
                            <button class="btn btn-primary text-white submit" type="submit">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            let formValidator = {}
            const isNumericInput = (event) => {
                const key = event.keyCode;
                return ((key >= 48 && key <= 57) || // Allow number line
                    (key >= 96 && key <= 105) // Allow number pad
                );
            };

            const isModifierKey = (event) => {
                const key = event.keyCode;
                return (event.shiftKey === true || key === 35 || key === 36) || // Allow Shift, Home, End
                    (key === 8 || key === 9 || key === 13 || key === 46) || // Allow Backspace, Tab, Enter, Delete
                    (key > 36 && key < 41) || // Allow left, up, right, down
                    (
                        // Allow Ctrl/Command + A,C,V,X,Z
                        (event.ctrlKey === true || event.metaKey === true) &&
                        (key === 65 || key === 67 || key === 86 || key === 88 || key === 90)
                    )
            };

            const enforceFormat = (event) => {
                // Input must be of a valid number format or a modifier key, and not longer than ten digits
                if (!isNumericInput(event) && !isModifierKey(event)) {
                    event.preventDefault();
                }
            };

            const formatToPhone = (event) => {
                if (isModifierKey(event)) { return; }

                // I am lazy and don't like to type things more than once
                const target = event.target;
                const input = event.target.value.replace(/\D/g, '').substring(0, 10); // First ten digits of input only
                const zip = input.substring(0, 3);
                const middle = input.substring(3, 6);
                const last = input.substring(6, 10);

                if (input.length > 6) { target.value = `(${zip}) ${middle} - ${last}`; }
                else if (input.length > 3) { target.value = `(${zip}) ${middle}`; }
                else if (input.length > 0) { target.value = `(${zip}`; }
            };

            const inputElement = document.getElementById('phonenumber');
            inputElement.addEventListener('keydown', enforceFormat);
            inputElement.addEventListener('keyup', formatToPhone);


            $("#zip-code").on("keypress keyup", function (event) {
                //    console.log('int = '+$(this).val());
                $(this).val($(this).val().replace(/[^\d].+/, ""));
                if (event.which != 8 && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });

            function formatPhoneNumber(number) {
                var formattedNumber = '';
                if (number.length >= 3) {
                    formattedNumber += '(' + number.substring(0, 3) + ')';
                }
                if (number.length >= 6) {
                    formattedNumber += ' ' + number.substring(3, 6);
                }
                if (number.length >= 10) {
                    formattedNumber += '-' + number.substring(6, 10);
                }
                return formattedNumber;
            }
            //validate phoneNumber
            function validatePhoneNumber(phoneNumber) {

                if (phoneNumber.length > 15) {
                    return true;
                } else {
                    return false;
                }
            }
            //check email
            function ValidateEmail(input) {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (regex.test(input)) {
                    return true;
                } else {
                    return false;
                }
            }

            //for form validation

            //submit form
            $(document).on('click', '.submit', function (event) {
                $("select[name='roles']").map(function () {
                    console.log($(this).val())
                    if (typeof $(this).val() === "undefined" || $(this).val() == '' || $(this).val() === null || $(this).val() === null) {
                        $(this).next('.error-roles').html('Please Select Role');
                        formValidator['roles'] = false;
                    } else {
                        $(this).next('.error-roles').html('');
                        formValidator['roles'] = true;
                    }
                });
                $("input[name='fname']").map(function () {
                    if (typeof $(this).val() === "undefined" || $(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-fname').html('Please Enter First Name');
                        formValidator['fname'] = false;
                    } else {
                        $(this).next('.error-fname').html('');
                        formValidator['fname'] = true;
                    }
                });

                $("input[name='lname']").map(function () {
                    if (typeof $(this).val() === "undefined" || $(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-lname').html('Please Enter Last Name');
                        formValidator['lname'] = false;
                    } else {
                        $(this).next('.error-lname').html('');
                        formValidator['lname'] = true;
                    }
                });

                $("input[name='email']").map(function () {
                    if (typeof $(this).val() === "undefined" || $(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-email').html('Please Enter Email');
                        formValidator['email'] = false;
                    } else {
                        if (ValidateEmail($(this).val())) {
                            $(this).next('.error-email').html('');
                            formValidator['email'] = true;
                        } else {
                            $(this).next('.error-email').html('Plese Enter Valid Email');
                            formValidator['email'] = false;
                        }
                    }
                });

                $("input[name='phone']").map(function () {
                    if (typeof $(this).val() === "undefined" || $(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-phone').html('Please Enter Phone Number');
                        formValidator['phone'] = false;
                    } else {

                        if (validatePhoneNumber($(this).val())) {
                            $(this).next('.error-phone').html('');
                            formValidator['phone'] = true;
                        } else {
                            $(this).next('.error-phone').html('Please Enter Valid Phone');
                            formValidator['phone'] = false;
                        }

                    }
                });

                $("input[name='Address1']").map(function () {
                    if (typeof $(this).val() === "undefined" || $(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-Address1').html('Please Enter Address');
                        formValidator['Address1'] = false;
                    } else {
                        $(this).next('.error-Address1').html('');
                        formValidator['Address1'] = true;
                    }
                });

                $("input[name='city']").map(function () {
                    if (typeof $(this).val() === "undefined" || $(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-city').html('Please Enter City');
                        formValidator['city'] = false;
                    } else {
                        $(this).next('.error-city').html('');
                        formValidator['city'] = true;
                    }
                });

                $("select[name='state']").map(function () {
                    if (typeof $(this).val() === "undefined" || $(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-state').html('Please Select State');
                        formValidator['state'] = false;
                    } else {
                        $(this).next('.error-state').html('');
                        formValidator['state'] = true;
                    }
                });

                $("input[name='zip']").map(function () {
                    if (typeof $(this).val() === "undefined" || $(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-zip').html('Please Enter Zip-Code');
                        formValidator['zip'] = false;
                    } else {
                        $(this).next('.error-zip').html('');
                        formValidator['zip'] = true;
                    }
                });

                $("select[name='country']").map(function () {
                    if (typeof $(this).val() === "undefined" || $(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-country').html('Please Select Country');
                        formValidator['country'] = false;
                    } else {
                        $(this).next('.error-country').html('');
                        formValidator['country'] = true;
                    }
                });
                console.log(formValidator)

                const validationCheck = Object.values(formValidator).every(Boolean)
                console.log(validationCheck)
                if (validationCheck) {
                    return true;
                } else {
                    event.preventDefault();
                }
            })

            $('.fname, .lname, .city').on('keypress keyup', function (event) {
                var key = String.fromCharCode(event.which || event.keyCode);
                if (event.which !== 32 && !key.match(/[a-zA-Z0-9]/)) {
                    event.preventDefault();
                    return false;
                }
            });

        });
    </script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>

    <script>
        ClassicEditor
            .create(document.querySelector('#long_description'))
            .catch(error => {
                console.error(error);
            });
           
    </script>


@endsection