@extends('admin.layouts.layout')
@section('title', 'Edit User')
@section('content')
    <div class="app-main__outer">

        <div class="app-main__inner">
            <div class="d-flex justify-content-between user-access">
              <div class="user-welcome">
                <h3>Edit User</h3>
              </div>
              <!-- <button class="btn primary-bg"><i class="fa-solid fa-plus"></i> Add New User</button> -->
            </div>
            <form action="{{route('users.update',$user->id)}}" method="post">
                @csrf
                @method('put')

           <div class="new-user-form mt-4 p-4">
            <div class="row">
                @php
              $roleuser =0;

              @endphp
                @if(!empty($user->getRoleNames()))
                @foreach($user->getRoleNames() as $v)
              @php
              $roleuser =$v;

              @endphp
                @endforeach
              @endif
              <div class="col-md-4 mb-3">
                <label for="state">Role<span>*</span></label>
                <select class="form-select" aria-label="Default select example" id="state" name="roles">
                  @foreach($roles as $role)
                  <option value="{{$role}}" {{$roleuser == $role ? 'selected': ''}} >{{$role}}</option>
                  @endforeach
                </select>
                <span class="error error-roles"><span>
                
              </div>
              <div class="col-md-4 mb-3">
                <label for="company">First Name<span>*</span></label>
                <input type="text" name="fname" id="company" class="form-control fname"  value={{$user->f_name}}>
                <span class="error error-fname"><span>
                
              </div>
              <div class="col-md-4 mb-3">
                <label for="company">Last Name<span>*</span></label>
                <input type="text" name="lname" id="company" class="form-control lname"  value={{$user->l_name}}>
                <span class="error error-lname"><span>
              </div>
              <div class="col-md-4 mb-3">
                <label for="company">Email Address<span>*</span></label>
                <input type="text" name="email" id="company" class="form-control"  value={{$user->email}}>
                <span class="error error-email"><span>
                
              </div>
              
              
              <div class="d-flex justify-content-end gap-2"><a href="{{ route('users.index') }}"><button type="button" class="btn bg-secondary text-white">Cancel</button></a><button class="btn btn-primary text-white update" type="submit">Update</button></div>

            </div>
           </div>
            </form>
          </div>


    </div>

    <script>
        $(document).ready(function() {
            var phone = "{{$user->phone}}"
           $('#phonenumber').val(phone)
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
	if(!isNumericInput(event) && !isModifierKey(event)){
		event.preventDefault();
	}
};

const formatToPhone = (event) => {
	if(isModifierKey(event)) {return;}

	// I am lazy and don't like to type things more than once
	const target = event.target;
	const input = event.target.value.replace(/\D/g,'').substring(0,10); // First ten digits of input only
	const zip = input.substring(0,3);
	const middle = input.substring(3,6);
	const last = input.substring(6,10);

	if(input.length > 6){target.value = `(${zip}) ${middle} - ${last}`;}
	else if(input.length > 3){target.value = `(${zip}) ${middle}`;}
	else if(input.length > 0){target.value = `(${zip}`;}
};

const inputElement = document.getElementById('phonenumber');
inputElement.addEventListener('keydown',enforceFormat);
inputElement.addEventListener('keyup',formatToPhone);


            $("#zip-code").on("keypress keyup", function(event) {
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

                if (phoneNumber.length >15) {
                    return true;
                } else {
                    return false;
                }
            }
            //check email
            function ValidateEmail(input) {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if(regex.test(input)){
                    return true;
                }else{
                    return false;
                }
            }

            //for form validation

            //submit form
            $(document).on('click', '.update', function(event) {
                $("select[name='roles']").map(function() {
                    console.log($(this).val())
                    if (typeof $(this).val() === "undefined" ||$(this).val() == '' || $(this).val() === null || $(this).val() === null) {
                        $(this).next('.error-roles').html('Please Select Role');
                        formValidator['roles'] = false;
                    } else {
                        $(this).next('.error-roles').html('');
                        formValidator['roles'] = true;
                    }
                });
                $("input[name='fname']").map(function() {
                    if (typeof $(this).val() === "undefined" ||$(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-fname').html('Please Enter First Name');
                        formValidator['fname'] = false;
                    } else {
                        $(this).next('.error-fname').html('');
                        formValidator['fname'] = true;
                    }
                });

                $("input[name='lname']").map(function() {
                    if (typeof $(this).val() === "undefined" ||$(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-lname').html('Please Enter Last Name');
                        formValidator['lname'] = false;
                    } else {
                        $(this).next('.error-lname').html('');
                        formValidator['lname'] = true;
                    }
                });

                $("input[name='email']").map(function() {
                    if (typeof $(this).val() === "undefined" ||$(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-email').html('Please Enter Email');
                        formValidator['email'] = false;
                    } else {
                        if(ValidateEmail($(this).val())){
                            $(this).next('.error-email').html('');
                                formValidator['email'] = true;
                        }else{
                            $(this).next('.error-email').html('Plese Enter Valid Email');
                                formValidator['email'] = false;
                        }
                    }
                });

                $("input[name='phone']").map(function() {
                    if (typeof $(this).val() === "undefined" ||$(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-phone').html('Please Enter Phone Number');
                        formValidator['phone'] = false;
                    } else {

                        if(validatePhoneNumber($(this).val())){
                            $(this).next('.error-phone').html('');
                                formValidator['phone'] = true;
                        }else{
                            $(this).next('.error-phone').html('Please Enter Valid Phone');
                                formValidator['phone'] = false;
                        }

                    }
                });

                $("input[name='Address1']").map(function() {
                    if (typeof $(this).val() === "undefined" ||$(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-Address1').html('Please Enter Address');
                        formValidator['Address1'] = false;
                    } else {
                        $(this).next('.error-Address1').html('');
                        formValidator['Address1'] = true;
                    }
                });

                $("input[name='city']").map(function() {
                    if (typeof $(this).val() === "undefined" ||$(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-city').html('Please Enter City');
                        formValidator['city'] = false;
                    } else {
                        $(this).next('.error-city').html('');
                        formValidator['city'] = true;
                    }
                });

                $("select[name='state']").map(function() {
                    if (typeof $(this).val() === "undefined" ||$(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-state').html('Please Select State');
                        formValidator['state'] = false;
                    } else {
                        $(this).next('.error-state').html('');
                        formValidator['state'] = true;
                    }
                });

                $("input[name='zip']").map(function() {
                    if (typeof $(this).val() === "undefined" ||$(this).val() == '' || $(this).val() === null) {
                        $(this).next('.error-zip').html('Please Enter Zip-Code');
                        formValidator['zip'] = false;
                    } else {
                        $(this).next('.error-zip').html('');
                        formValidator['zip'] = true;
                    }
                });

                $("select[name='country']").map(function() {
                    if (typeof $(this).val() === "undefined" ||$(this).val() == '' || $(this).val() === null) {
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



@endsection
