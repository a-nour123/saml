@extends('admin/layouts/contentLayoutMaster')

@section('title', __('configure.User Management'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('fonts/fontawesome-6.2.1/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-chat.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-chat-list.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/wizard/bs-stepper.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/jquery.rateyo.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/plyr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
    <link rel="stylesheet" href="{{ asset('cdn/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('css/core.css')) }}" />
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/vendors.min.css')) }}" />


@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
    <link rel="stylesheet" href="{{ asset('cdn/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/fontawesome-6.2.1/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-chat.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-chat-list.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/wizard/bs-stepper.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/jquery.rateyo.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/plyr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
    <link rel="stylesheet" href="{{ asset('cdn/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('css/core.css')) }}" />
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/vendors.min.css')) }}" />

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
                    <div class="col-sm-6 pe-0" style="text-align: end;">

                        <div class="action-content">


                                        @if (auth()->user()->hasPermission('user_management.create'))
                                    {{-- @if (checkUsersCount(12)) --}}
                                        <a class="btn btn-primary"
                                            href="{{ route('admin.configure.user.create') }}" type="button">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    {{-- @endif --}}
                                @endif


                                    <!-- <a  href="{{ route('admin.hierarchy.department.notificationsSettingsDepartement') }}"
                                        class=" btn btn-primary" target="_self">
                                        <i class="fa fa-regular fa-bell"></i>
                                    </a> -->

                             <!-- <div class="btn-group dropdown dropdown-icon-wrapper me-1">
                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 8px !important;
                                        width: 40px;
                                        text-align: center;
                                        color: #FFF !important;
                                        height: 32px;
                                        line-height: 19px;">
                                        <i class="fa fa-solid fa-gear"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end export-types  ">

                                        <span class="dropdown-item" data-type="excel" >
                                        <i class="fa fa-solid fa-gear"></i>
                                            <span class="px-1 text-start"><a href="{{route("admin.hierarchy.department.configuretion")}}">{{ __('configuretion') }}</a></span>

                                        </span>

                                    </div>
                                </div> -->
                                <x-export-import name=" {{ __('configure.User') }}"
                                    createPermissionKey='user_management.create'
                                    exportPermissionKey='user_management.export'
                                    exportRouteKey='admin.configure.user.ajax.export' importRouteKey='will-added-TODO' />
                                      <div class="btn-group dropdown dropdown-icon-wrapper">
                                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                                data-bs-toggle="dropdown" aria-expanded="false" style=" border-radius: 8px !important;
                                                width: 40px;
                                                text-align: center;
                                                color: #fff !important;
                                                height: 32px;
                                                line-height: 19px;">
                                             <i class="fa-solid fa-file-import"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end export-types  ">


                                                <span class="dropdown-item" data-type="excel" >
                                                    <i class="fa-solid fa-file-import"></i>
                                                        <span class="px-1 text-start">

                                                            <a href="{{route('admin.configure.user.ldapImport')}}">{{ __('user.ldap_import') }}</a>
                                                        </span>

                                                    </span>

                                            </div>
                                        </div>


                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<div id="quill-service-content" class="d-none"></div>
</div>
    <!-- Advanced Search -->
    <section id="advanced-search-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header border-bottom p-1">
                        <div class="head-label">
                            <h4 class="card-title">{{ __('configure.ManageUsers') }}</h4>
                        </div>

                        </div>

                    </div>

                    <!--Search Form -->
                    <div class="card-body mt-2">
                        <form class="dt_adv_search" method="POST">
                            <div class="row g-1 mb-md-1">
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('configure.Type') }}</label>
                                    <select class="form-control dt-input dt-select select2" name="filter_type"
                                        id="type" data-column="1" data-column-index="0">
                                        <option value="">{{ __('locale.select-option') }}</option>
                                        <option value="grc" data-id="grc">Grc</option>
                                        <option value="ldap" data-id="ldap">Ldap</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">{{ __('configure.Role') }}</label>
                                    <select class="form-control dt-input dt-select select2" name="filter_role"
                                        data-column="5" data-column-index="4">
                                        <option value="">{{ __('locale.select-option') }}</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('configure.Department') }}</label>
                                    <select class="form-control dt-input dt-select select2" name="filter_department"
                                        id="control" data-column="8" data-column-index="2">
                                        <option value="">{{ __('locale.select-option') }}</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->name }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('configure.Username') }}</label>
                                    <input class="form-control dt-input" name="filter_username" data-column="2"
                                        data-column-index="1" type="text">
                                </div>
                            </div>
                        </form>
                    </div>
                    <hr class="my-0" />

                    <div class="card-datatable">
                        <table class="dt-advanced-server-search table" id="TableUser">
                            <thead>
                                <tr>
                                    <th>{{ __('locale.#') }}</th>
                                    <th>{{ __('configure.Type') }}</th>
                                    <th>{{ __('configure.Username') }}</th>
                                    <th>{{ __('configure.Name') }}</th>
                                    <th>{{ __('configure.Email') }}</th>
                                    <th>{{ __('configure.Role') }}</th>
                                    <th>{{ __('locale.Admin') }}</th>
                                    <th>{{ __('locale.Active') }}</th>
                                    <th>{{ __('configure.Department') }}</th>
                                    <th>{{ __('locale.Actions') }}</th>
                                </tr>
                            </thead>
                            <!-- <tfoot>
                                <tr>
                                    <th>{{ __('locale.#') }}</th>
                                    <th>{{ __('configure.Type') }}</th>
                                    <th>{{ __('configure.Username') }}</th>
                                    <th>{{ __('configure.Name') }}</th>
                                    <th>{{ __('configure.Email') }}</th>
                                    <th>{{ __('configure.Role') }}</th>
                                    <th>{{ __('locale.Admin') }}</th>
                                    <th>{{ __('locale.Active') }}</th>
                                    <th>{{ __('configure.Department') }}</th>
                                    <th>{{ __('locale.Actions') }}</th>

                                </tr>
                            </tfoot> -->
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <!--/ Advanced Search -->
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>


@endsection

@section('page-script')
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script>
        const currentUser = {{ auth()->id() }};
        let permission = [],
            lang = [],
            URLs = [];
        permission['edit'] = {{ auth()->user()->hasPermission('user_management.update')? 1: 0 }};
        permission['delete'] = {{ auth()->user()->hasPermission('user_management.delete')? 1: 0 }};

        lang['DetailsOfItem'] = "{{ __('locale.DetailsOfItem', ['item' => __('locale.user')]) }}";

        URLs['ajax_list'] = "{{ route('admin.configure.user.ajax.get-users') }}";
    </script>
    <script src="{{ asset('ajax-files/user_management/index.js') }}"></script>

    <script>
        let swal_title = "{{ __('configure.AreYouSureToDeleteThisEmployee') }}";
        let swal_text = '@lang('locale.YouWontBeAbleToRevertThis')';
        let swal_confirmButtonText = "{{ __('locale.ConfirmDelete') }}";
        let swal_cancelButtonText = "{{ __('locale.Cancel') }}";
        let swal_success = "{{ __('locale.Success') }}";
        let swal_error = "{{ __('locale.Error') }}";

        function UserEdit(id) {
            var url = "{{ route('admin.configure.user.edit', ':id') }}";
            url = url.replace(':id', id);
            window.location.href = url;
        }

        function UserDelete(id) {
            var id = id;

            Swal.fire({
                title: swal_title,
                text: swal_text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: swal_confirmButtonText,
                cancelButtonText: swal_cancelButtonText,
                customClass: {
                    confirmButton: 'btn btn-relief-success ms-1',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('admin.configure.user.destroy', '') }}" + "/" + id,
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            makeAlert('success', response.message, 'Success');
                            var oTable = $('#TableUser').DataTable();
                            oTable.ajax.reload();
                        },
                        error: function(xhr) {
                            var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr
                                .responseJSON.error : 'Unknown error';
                            makeAlert('error', errorMessage, 'error');
                        }
                    });

                }
            });
        }

        function makeAlert($status, message, title) {
            // On load Toast
            if (title == 'Success')
                title = '👋' + title;
            toastr[$status](message, title, {
                closeButton: true,
                tapToDismiss: false,
            });
        }

        function ChangeAccountStutas(id) {
            let url = "{{ route('admin.configure.user.ajax.account-status', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                url: url,
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }
    </script>
@endsection
