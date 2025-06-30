@extends('admin.layouts.contentLayoutMaster')

@section('title', __('configure.User Management'))
<!-- @section('title', __('configure.User Management')) -->

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
{{-- Page css files --}}
<link rel="stylesheet" href="{{ asset('intl-tel-input/build/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">

    <style>
        <style>.iti__flag {
            background-image: url("{{ asset('intl-tel-input/build/img/flags.png') }}");
        }

        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {
            .iti__flag {
                background-image: url("{{ asset('intl-tel-input/build/img/flags@2x.png') }}");
            }
        }
    </style>
    </style>
@endsection

@section('content')
<div class="content-header row">
    <div class="content-header-left col-12 mb-2">

        <div class="row breadcrumbs-top  widget-grid">
            <div class="col-12">
                <div class="page-title mt-2">
                    <div class="row">
                        <div class="col-sm-6 ps-0">
                            @if (@isset($breadcrumbs))
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                            style="display: flex;">
                                            <svg class="stroke-icon">
                                                <use href="{{ asset('fonts/icons/icon-sprite.svg#stroke-home') }}">
                                                </use>
                                            </svg></a></li>
                                    @foreach ($breadcrumbs as $breadcrumb)
                                        <li class="breadcrumb-item">
                                            @if (isset($breadcrumb['link']))
                                                <a
                                                    href="{{ $breadcrumb['link'] == 'javascript:void(0)' ? $breadcrumb['link'] : url($breadcrumb['link']) }}">
                                            @endif
                                            {{ $breadcrumb['name'] }}
                                            @if (isset($breadcrumb['link']))
                                                </a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            @endisset
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<div id="quill-service-content" class="d-none"></div>
</div>
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('configure.AddANewUser') }}</h4>

                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.configure.user.store') }}" method="POST" id="form-Add-User">
                            @CSRF
                            <div class="row">

                                <div class="col-xl-6 col-md-6 col-12 grc-ldap-field">
                                    <div class="mb-1">
                                        <label class="form-label" for="type">{{ __('locale.Type') }}</label>
                                        <select class="form-control select2" name="type" value="{{ old('type') }}"
                                            id="type">
                                            <option value="">{{ __('locale.select-option') }}</option>
                                            <option value="grc">GRC</option>
                                            <option value="ldap">Ldap</option>

                                        </select>
                                        @error('type')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12 grc-field">
                                    <div class="mb-1">
                                        <label class="form-label" for="role_id">{{ __('configure.Role') }}</label>
                                        <select class="form-control select2" name="role_id" value="{{ old('role_id') }}"
                                            id="role_id">
                                            <option value="">{{ __('locale.select-option') }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12 grc-field">
                                    <div class="mb-1">
                                        <label class="form-label" for="name">{{ __('configure.FullName') }}</label>
                                        <input type="text" class="form-control " name="name"
                                            value="{{ old('name') }}" id="name">
                                        @error('name')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12 grc-ldap-field">
                                    <div class="mb-1">
                                        <label class="form-label" for="username">{{ __('configure.Username') }}</label>
                                        <input type="text" class="form-control " name="username"
                                            value="{{ old('username') }}" id="username">
                                        @error('username')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12 grc-field">
                                    <div class="mb-1">
                                        <label class="form-label" for="email">{{ __('configure.EmailAddress') }}</label>
                                        <input type="email" class="form-control " name="email"
                                            value="{{ old('email') }}" id="email">
                                        @error('email')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- <div class="col-xl-6 col-md-6 col-12 grc-field">
                                    <div class="mb-1">
                                        <label class="form-label" for="phone_number">{{ __('configure.PhoneNumber') }}</label>
                                        <input type="tel" class="form-control " name="phone_number"
                                            value="{{ old('phone_number') }}" id="phone_number">
                                        @error('phone_number')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div> --}}

                                <div class="col-xl-6 col-md-6 col-12 grc-field">
                                    <div class="mb-1 row">
                                        <div class="col-xl-6 col-md-6 col-12">
                                            <label class="form-label"  >{{ __('configure.Ext') }}</label>
                                            <input type="text" class="form-control" name="mobile" id="mobile" >
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-12">
                                            <label class="form-label" for="phone_number">{{ __('configure.PhoneNumber') }}</label>
                                            <input type="tel" class="form-control " name="phone_number"
                                                value="{{ old('phone_number') }}" id="phone_number">
                                            @error('phone_number')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>


                                    </div>
                                </div>



                                {{--  <div class="col-xl-6 col-md-6 col-12 grc-ldap-field" id="LDAP_Dapartment"
                                    style="display: none;">
                                    <div class="mb-1">
                                        <label class="form-label"
                                            for="dapartment">{{ __('configure.LDAP_Dapartment') }}</label>
                                        <input type="text" class="form-control " name="dapartment"
                                            value="{{ old('dapartment') }}" id="department">
                                        @error('dapartment')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>  --}}


                                <div class="col-xl-6 col-md-6 col-12 grc-field ldap-without-pass">
                                    <div class="mb-1">
                                        <label class="form-label" for="password">{{ __('configure.Password') }}</label>
                                        <input type="password" class="form-control " name="password"
                                            value="{{ old('password') }}" id="password">

                                        @error('password')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12 grc-field ldap-without-pass">
                                    <div class="mb-1">
                                        <label class="form-label"
                                            for="password_confirmation">{{ __('configure.RepeatPassword') }}</label>
                                        <input type="password" class="form-control " name="password_confirmation"
                                            value="{{ old('password_confirmation') }}" id="password_confirmation">

                                        @error('password_confirmation')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-xl-6 col-md-6 col-12 grc-field">
                                    <div class="mb-1">
                                        <label class="form-label" for="role_id">{{ __('configure.Manager') }}</label>
                                        <select class="form-control select2" name="manager_id"
                                            value="{{ old('manager_id') }}" id="manager_id">
                                            <option value="">{{ __('locale.select-option') }}</option>
                                            @foreach ($managers as $manager)
                                                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('manager_id')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-12 grc-field">
                                    <div class="mb-1">
                                        <label class="form-label" for="role_id">{{ __('configure.Department') }}</label>
                                        <select class="form-control select2" name="department_id"
                                            value="{{ old('department_id') }}" id="department_id">
                                            <option value="">{{ __('locale.select-option') }}</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-12 grc-field">
                                    <div class="mb-1">
                                        <label class="form-label" for="role_id">{{ __('configure.Job') }}</label>
                                        <select class="form-control select2" name="job_id" value="{{ old('job_id') }}"
                                            id="job_id">
                                            <option value="">{{ __('locale.select-option') }}</option>
                                            @foreach ($jobs as $job)
                                                <option value="{{ $job->id }}">{{ $job->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('job_id')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-12 grc-field">
                                    <div class="mb-1">
                                        <label class="form-label" for="teams">{{ __('locale.Teams') }}</label>
                                        <select class="form-control select2" name="teams[]" value="{{ old('teams') }}"
                                            id="teams" multiple="multiple">
                                            <option value="">{{ __('locale.select-option') }}</option>
                                            @foreach ($teams as $team)
                                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('teams')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>



                                <div class="col-xl-6 col-md-6 col-12 grc-field">
                                    <div class="mb-1">
                                        <label class="form-check-label mb-50"
                                            for="admin">{{ __('configure.GrantAdmin') }}</label>
                                        <div class="form-check form-switch form-check-primary">
                                            <input type="checkbox" class="form-check-input" name="admin"
                                                id="admin" value="1" id="admin-toggle" />
                                            <label class="form-check-label" for="admin">
                                                <span class="switch-icon-left"><i data-feather="check"></i></span>
                                                <span class="switch-icon-right"><i data-feather="x"></i></span>
                                            </label>
                                        </div>
                                        @error('admin')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-xl-12 mt-2 col-md-6 col-12 grc-field">
                                    <button class="btn btn-primary" type="submit">{{ __('locale.Save') }}</button>
                                </div>

                                <div class="col-xl-12 mt-2 col-md-6 col-12 ldap-field">
                                    <button class="btn btn-primary" type="button"
                                        onclick="CheckUserLdap()">{{ __('locale.synchronization') }}</button>
                                </div>


                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
@endsection
@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset('ajax-files/compliance/general-compliance.js') }}"></script>
    <script src="{{ asset('intl-tel-input/build/js/intlTelInput.js') }}"></script>
    <script src="{{ asset('intl-tel-input/build/js/utils.js') }}"></script>
    <script>
        var input = document.querySelector("#phone_number");
        var number = window.intlTelInput(input, {
            separateDialCode: true,
            utilsScript: "{{ asset('intl-tel-input/build/js/utils.js') }}",
            preferredCountries: ['sa', 'eg'],
            hiddenInput: "full_number",
        });
    </script>
    <script>
        // here, the index maps to the error code returned from getValidationError - see readme
        const errorMap = ["Invalid phone number", "Invalid country code", "Too short phone number", "Too long phone number",
            "Invalid phone number"
        ];


        $("#form-Add-User").submit(function(e) {
            var checkPhoneNumber = 1;
            e.preventDefault();
            if (input.value.trim()) {
                if (!number.isValidNumber()) {
                    const errorCode = number.getValidationError();
                    var errors = [errorMap[errorCode]];
                    showPopUpError(errors);
                    checkPhoneNumber = 0
                } else {
                    var full_number = number.getNumber(intlTelInputUtils.numberFormat.E164);
                    $("input[name='phone_number[full_number]'").val(full_number);
                }
            }
            if (checkPhoneNumber == 1) {
                var formData = $(this).serialize();
                var url = $(this).attr("action");
                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    success: function(data) {
                        if (data['status'] == 1) {
                            makeAlert('success', data['message']);

                            resetForm('#form-Add-User');
                        } else {
                            var errors = data['errors'];
                            showPopUpError(errors);

                        }

                    }
                });

            }
        });
    </script>
    <script>
        $('.selectAllPermission').on('click', function() {
            if ($(this).is(':checked')) {
                var data_id = $(this).data('id');
                $('.checkboxType-' + data_id).prop('checked', true);
            } else {
                var data_id = $(this).data('id');
                $('.checkboxType-' + data_id).prop('checked', false);
            }
        });
    </script>
    <script>
        $('#admin').change(function() {
            if ($(this).is(':checked')) {
                $("#teams > option").prop("selected", "selected");
                $("#teams").trigger("change");

            }

        });
    </script>
    <script>
        $(".ldap-field").hide();
        $('#type').change(function() {
            if ($(this).val() == 'ldap') {
                $(".grc-field").hide('slow');
                $(".ldap-field").show('slow');


            } else {
                $(".grc-field").show('slow');
                $(".ldap-field").hide('slow');
                $(".grc-ldap-field input").removeAttr('readonly');
                $("#email").removeAttr('readonly');

            }

        });
    </script>
    <script>
        function CheckUserLdap() {

            var username = $('#username').val();
            var email = $('#email').val();
            var url = "{{ route('admin.configure.user.check-user-ldap') }}";
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'username': username,
                    'email': email
                },
                success: function(data) {
                    if (data['status'] == 1) {
                        if (data['check']) {

                            $(".grc-field").show('slow');
                            $(".ldap-without-pass").hide();
                            $(".ldap-field").hide('slow');
                            $('#LDAP_Dapartment').show('slow');


                            $('#department').val(data['user']['department']);
                            $('#name').val(data['user']['name']);
                            $('#email').val(data['user']['email']);
                           $('#mobile').val(data['user']['phone']);
                            $('#password').val(data['user']['password']);
                            $('#password_confirmation').val(data['user']['password']);
                            $(".grc-ldap-field input").attr('readonly', 'readonly');
                             $("#email").attr('readonly', 'readonly');

                        } else {
                            makeAlert('error', data['message']);
                        }
                    } else {
                        var errors = data['errors'];
                        showPopUpError(errors);

                    }

                }
            });
        }
    </script>
@endsection
