@extends('admin/layouts/contentLayoutMaster')

@section('title', __('governance.Define Controls'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('fonts/fontawesome-6.2.1/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">

    {{--
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}"> --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-chat.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-chat-list.css')) }}">
@endsection


<style>
    .gov_btn {
        border-color: #44225c !important;
        background-color: #44225c !important;
        color: #fff !important;
        /* padding: 7px; */
        border: 1px solid transparent;
        padding: 0.786rem 1.5rem;
        line-height: 1;
        border-radius: 0.358rem;
        font-weight: 500;
        font-size: 1rem;
    }

    .gov_check {
        padding: 0.786rem 0.7rem;
        line-height: 1;
        font-weight: 500;
        font-size: 1.2rem;
    }

    .gov_err {

        color: red;
    }

    .gov_btn {
        border-color: #44225c;
        background-color: #44225c;
        color: #fff !important;
        /* padding: 7px; */
        border: 1px solid transparent;
        padding: 0.786rem 1.5rem;
        line-height: 1;
        border-radius: 0.358rem;
        font-weight: 500;
        font-size: 1rem;
    }

    .gov_btn_edit {
        border-color: #5388B4 !important;
        background-color: #5388B4 !important;
        color: #fff !important;
        border: 1px solid transparent;
        padding: 0.786rem 1.5rem;
        line-height: 1;
        border-radius: 0.358rem;
        font-weight: 500;
        font-size: 1rem;
    }

    .gov_btn_map {
        border-color: #6c757d !important;
        background-color: #6c757d !important;
        color: #fff !important;
        border: 1px solid transparent;
        padding: 0.786rem 1.5rem;
        line-height: 1;
        border-radius: 0.358rem;
        font-weight: 500;
        font-size: 1rem;
    }

    .gov_btn_delete {
        border-color: red !important;
        background-color: red !important;
        color: #fff !important;
        border: 1px solid transparent;
        padding: 0.786rem 1.5rem;
        line-height: 1;
        border-radius: 0.358rem;
        font-weight: 500;
        font-size: 1rem;
    }

    #control_supplemental_guidance {
        height: 90px;
    }

    #control-guide-value {
        width: 364px;
        /* Set the width as per your requirement */
        height: 200px;
    }

    .ql-toolbar.ql-snow {
        width: 364px;
        /* Set the width as per your requirement */
    }

    .pie {
        display: block;
        margin: 25px auto;
    }

    .data-text__value {
        font-size: 45px !important;
    }

    .data-text__value {
        font-size: 45px !important;
    }

    table.dataTable.table td,
    table.dataTable.table th {
        max-width: 150px !important;
    }
</style>
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

                                @if (auth()->user()->hasPermission('control.create'))
                                    <button class=" btn btn-primary " type="button" data-bs-toggle="modal"
                                        data-bs-target="#add_control">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                    <a href="{{ route('admin.governance.notificationsSettingscontrol') }}"
                                        class=" btn btn-primary" target="_self">
                                        <i class="fa fa-regular fa-bell"></i>
                                    </a>
                                @endif

                                {{-- @if (auth()->user()->hasPermission('audits.create'))
                                    <button class="dt-button  btn btn-info  me-1" type="button"
                                        onclick="showModalInitiateAuditsForFrameworkControls()">
                                        {{ __('locale.Initiate Audits') }}
                                    </button>
                                @endif --}}
                                @if (auth()->user()->hasPermission('control.configuration'))
                                    <div class="btn-group dropdown dropdown-icon-wrapper ">
                                        <button type="button"
                                            class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                            data-bs-toggle="dropdown" aria-expanded="false"
                                            style="border-radius: 8px !important;
                                        width: 40px;
                                        text-align: center;
                                        color: #FFF !important;
                                        height: 28px;
                                        line-height: 19px;">
                                            <i class="fa fa-solid fa-gear"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end export-types  ">
                                            <span class="dropdown-item" data-type="excel">
                                                <i class="fa fa-solid fa-gear"></i>
                                                <span class="px-1 text-start"><a
                                                        href="{{ route('admin.governance.control.configuretion') }}">{{ __('configuretion') }}</a></span>

                                            </span>


                                        </div>
                                    </div>
                                @endif
                                <x-export-import name="{{ __('governance.Control') }}"
                                    createPermissionKey='control.create' exportPermissionKey='control.export'
                                    exportRouteKey='admin.governance.control.ajax.export'
                                    importRouteKey='admin.governance.control.import' />


                                <!-- <a class="btn btn-primary" href="http://"> <i class="fa-solid fa-file-invoice"></i></a> -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div id="quill-service-content" class="d-none"></div>

</div>

{{-- <div class="card">
    <div class="card-header border-bottom p-1">
        <div class="head-label">
            <h4 class="card-title">{{ __('locale.StatusOverView') }}</h4>
        </div>
    </div>

    <div class="card-body mt-2">
        <div class="row">
            <!-- Framework Select Dropdown -->
            <div class="col-12">
                <div class="form-group">
                    <label for="frameworkSelect">{{ __('locale.Framework') }}</label>
                    <select id="frameworkSelect" class="form-control">
                        <option value="" selected>{{ __('locale.Select') }}</option>
                        @foreach ($frameworks as $framework)
                            <option value="{{ $framework['id'] }}" data-id="{{ $framework['id'] }}">
                                {{ $framework['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Graph Display Area -->
            <div id="chartContainer" class="col-12"></div>
        </div>
    </div>
</div> --}}



<!-- Advanced Search -->
<section id="advanced-search-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header border-bottom p-1">
                    <div class="head-label">
                        <h4 class="card-title">{{ __('locale.FilterBy') }}</h4>
                    </div>
                </div>
                <!--Search Form -->
                <div class="card-body mt-2">
                    <form class="dt_adv_search" method="POST">
                        <div class="row g-1 mb-md-1">
                            <!-- Name -->
                            <div class="col-md-2">
                                <label class="form-label">{{ __('Name') }}</label>
                                <input class="form-control dt-input " name="filter_short_name" data-column="2"
                                    data-column-index="1" type="text">
                            </div>

                            <!-- framework -->
                            <div class="col-md-2">
                                <label class="form-label">{{ __('governance.Framework') }}</label>
                                <select class="form-control dt-input dt-select select2 " name="filter_Frameworks"
                                    id="framework" data-column="4" data-column-index="3">
                                    <option value="">{{ __('locale.select-option') }}</option>
                                    @foreach ($frameworks as $framework)
                                        <option value="{{ $framework['name'] }}" data-id="{{ $framework['id'] }}">
                                            {{ $framework['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!--  families -->
                            <div class="col-md-2 family-container">
                                <label class="form-label">{{ __('governance.Domain') }}</label>
                                <select class="form-control dt-input dt-select select2 domain_select_filter"
                                    no_datatable_draw="true" name="filter_family_name" data-column="5"
                                    data-column-index="4">
                                    <option value="">{{ __('locale.select-option') }}</option>
                                     
                                </select>
                            </div>

                            {{-- sub families --}}
                            <div class="col-md-2">
                                <label class="form-label">{{ __('governance.sub_domain') }}</label>
                                <select class="form-control dt-input dt-select select2"
                                    name="filter_family_with_parent" data-column="6" data-column-index="5">
                                    <option value="" selected>{{ __('locale.select-option') }}</option>

                                </select>
                            </div>
                            <!--  families -->
                            <!-- //Control Status -->
                            <div class="col-md-2">
                                <label class="form-label ">{{ __('locale.ControlStatus') }}</label>
                                <select class="select2 form-select" name="filter_control_status" data-column="7"
                                    data-column-index="6">
                                    <option value="" selected>{{ __('locale.select-option') }}</option>
                                    <option value="Not Applicable"> {{ __('locale.Not Applicable') }}</option>
                                    <option value="Not Implemented"> {{ __('locale.Not Implemented') }}</option>
                                    <option value="Partially Implemented"> {{ __('locale.Partially Implemented') }}
                                    </option>
                                    <option value="Implemented"> {{ __('locale.Implemented') }}</option>
                                </select>
                                <span class="error error-filter_control_status"></span>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="card-datatable table-responsive pb-4">
                    <table class="dt-advanced-server-search table">
                        <thead>
                            <tr>
                                <th>{{ __('locale.#') }}</th>
                                <th class="all">{{ __('locale.Name') }}</th>
                                <th class="all">{{ __('locale.Description') }}</th>
                                <th class="all">{{ __('governance.Framework') }}</th>
                                <th class="all">{{ __('governance.Domain') }}</th>
                                <th class="all">{{ __('governance.sub_domain') }}</th>
                                <th class="all">{{ __('governance.ControlStatus') }}</th>
                                <th class="all">{{ __('locale.Actions') }}</th>
                            </tr>
                        </thead>

                        <!-- <tfoot>
                            <tr>
                                <th>{{ __('locale.#') }}</th>
                                <th class="all">{{ __('locale.Name') }}</th>
                                <th class="all">{{ __('locale.Description') }}</th>
                                <th class="all">{{ __('governance.Framework') }}</th>
                                <th class="all">{{ __('governance.Domain') }}</th>
                                <th class="all">{{ __('governance.sub_domain') }}</th>
                                <th class="all">{{ __('governance.ControlStatus') }}</th>
                                <th class="all">{{ __('locale.Actions') }}</th>
                            </tr>
                        </tfoot> -->

                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- // add control modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModal"
    aria-hidden="true" id="add_control">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('locale.Add') }} {{ __('governance.Control') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- <div class="modal modal-slide-in sidebar-todo-modal fade" id="add_control">
    <div class="modal-dialog sidebar-lg"> -->
            <div class="modal-content p-0">
                <form id="form-add_control" class="form-add_control todo-modal" novalidate method="POST"
                    action="{{ route('admin.governance.control.store2') }}">
                    @csrf

                    <!-- <div class="modal-header align-items-center mb-1">
                    <h5 class="modal-title">{{ __('locale.Add') }} {{ __('governance.Control') }}</h5>
                    <div class="todo-item-action d-flex align-items-center justify-content-between ms-auto">
                        <span class="todo-item-favorite cursor-pointer me-75"><i data-feather="star"
                                class="font-medium-2"></i></span>
                        <i data-feather="x" class="cursor-pointer" data-bs-dismiss="modal" stroke-width="3"></i>
                    </div>
                </div> -->
                    <div class="modal-body flex-grow-1 pb-sm-0 pb-3">
                        <div class="action-tags">
                            <div class="mb-1">
                                <label for="title" class="form-label">{{ __('locale.Name') }}</label>
                                <input type="text" name="name" class=" form-control" placeholder=""
                                    required />
                                <span class="error error-name "></span>

                            </div>

                            <div class="mb-1">
                                <label for="desc" class="form-label">{{ __('locale.Description') }}</label>
                                <textarea class="form-control" name="description"></textarea>
                                <span class="error error-description"></span>

                            </div>
                            <div class="mb-1">
                                <label for="title" class="form-label">{{ __('governance.ControlNumber') }}</label>
                                <input type="text" name="number" class=" form-control" placeholder="" />
                                <span class="error error-number "></span>

                            </div>

                            <!--  long_name -->
                            <div class="mb-1">
                                <label class="form-label"
                                    for="long_name">{{ __('governance.ControlLongName') }}</label>
                                <input class="form-control" type="text" name="long_name">
                            </div>

                            <!--  framework -->
                            <div class="mb-1 framework-container">
                                <label class="form-label">{{ __('governance.Framework') }}</label>
                                <select class="select2 form-select  add-control-framework-select" name="framework"
                                    required>
                                    <option value="" disabled selected>{{ __('locale.select-option') }}</option>
                                    @foreach ($frameworks as $framework)
                                        <option value="{{ $framework['id'] }}"
                                            data-domains="{{ json_encode($framework['domains']) }}"
                                            data-controls="{{ json_encode($framework['controls']) }}">
                                            {{ $framework['name'] }}</option>
                                    @endforeach
                                </select>
                                <span class="error error-framework"></span>
                            </div>

                            <!--  families -->
                            <div class="mb-1 family-container">
                                <label class="form-label" for="family">{{ __('governance.Domain') }}</label>

                                <select class="select2 form-select domain_select" name="family" required>
                                    <option value="" disabled selected>{{ __('locale.select-option') }}</option>
                                </select>
                                <span class="error error-family"></span>
                            </div>

                            {{-- sub families --}}
                            <div class="mb-1">
                                <label class="form-label ">{{ __('governance.sub_domain') }}</label>

                                <select class="select2 form-select" name="sub_family" required>
                                    <option value="" disabled selected>{{ __('locale.select-option') }}</option>
                                </select>
                                <span class="error error-sub_family"></span>
                            </div>

                            {{-- Parent control --}}
                            <div class="mb-1">
                                <label class="form-label ">{{ __('governance.ParentControlFramework') }}</label>
                                <select class="select2 form-select" name="parent_id">
                                    <option value="" selected>{{ __('locale.select-option') }}</option>
                                </select>
                                <span class="error error-parent_id"></span>
                            </div>

                            <!--  mitigation_guidance -->
                            <div class="mb-1">
                                <label class="form-label"
                                    for="mitigation_percent">{{ __('governance.mitigationpercent') }} </label>
                                <input class="form-control" type="text" name="mitigation_percent">
                            </div>

                            <!--  supplemental_guidance -->

                            <div class="mb-1">
                                <label class="form-label">{{ __('locale.ControlGuideImplementation') }}</label>
                                <div id="control_supplemental_guidance">
                                </div>
                            </div>

                            {{-- <div class="mb-1">
                                <label class="form-label" for="supplemental_guidance">
                                    {{ __('locale.ControlGuideImplementation') }} </label> --}}
                            {{-- <input class="form-control" type="text" name="supplemental_guidance"> --}}
                            {{-- <textarea class="form-control" name="supplemental_guidance" rows="7"></textarea>

                            </div> --}}

                            <div class="mb-1">
                                <label class="form-label" for="priority"> {{ __('governance.ControlPriority') }}
                                </label>

                                <select class="select2 form-select" id="task-assigned" name="priority">
                                    <option value="">
                                        {{ __('governance.selectpriority') }}
                                    </option>
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}">
                                            {{ $priority->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-1">
                                <label class="form-label" for="phase">{{ __('governance.ControlPhase') }} </label>

                                <select class="select2 form-select" id="task-assigned" name="phase">
                                    <option value="">
                                        {{ __('governance.selectphase') }}
                                    </option>
                                    @foreach ($phases as $phase)
                                        <option value="{{ $phase->id }}">
                                            {{ $phase->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-1">
                                <label class="form-label" for="type">{{ __('governance.ControlType') }} </label>

                                <select class="select2 form-select" id="task-assigned" name="type">
                                    <option value="">
                                        {{ __('governance.selectType') }}
                                    </option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}">
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="mb-1">
                                <label class="form-label" for="maturity"> {{ __('governance.ControlMaturity') }}
                                </label>

                                <select class="select2 form-select" id="task-assigned" name="maturity">
                                    <option value="">
                                        {{ __('governance.selectmaturity') }}
                                    </option>
                                    @foreach ($maturities as $maturity)
                                        <option value="{{ $maturity->id }}">
                                            {{ $maturity->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="mb-1">
                                <label class="form-label" for="class"> {{ __('governance.ControlClass') }}
                                </label>

                                <select class="select2 form-select" id="task-assigned" name="class">
                                    <option value="">
                                        {{ __('governance.selectClass') }}
                                    </option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="mb-1">

                                <label class="form-label" for="desired_maturity">
                                    {{ __('governance.ControlDesiredMaturity') }} </label>
                                <select class="select2 form-select" id="task-assigned" name="desired_maturity">
                                    <option value=""> {{ __('governance.selectDesiredMaturity') }} </option>
                                    @foreach ($desiredMaturities as $desiredMaturity)
                                        <option value="{{ $desiredMaturity->id }}"> {{ $desiredMaturity->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <!-- //Control Status -->
                            {{-- <div class="mb-1">
                            <label class="form-label ">Control Status</label>
                            <select class="select2 form-select" name="control_status">
                                <option value="Not Applicable"> {{ __('locale.Not Applicable') }}</option>
                                <option value="Not Implemented"> {{ __('locale.Not Implemented') }}</option>
                                <option value="Partially Implemented"> {{ __('locale.Partially Implemented') }}</option>
                                <option value="Implemented"> {{ __('locale.Implemented') }}</option>
                            </select>
                            <span class="error error-control_status"></span>
                        </div> --}}

                            <!-- //owner -->
                            <div class="mb-1">

                                <label class="form-label" for="owner"> {{ __('governance.ControlOwner') }}
                                </label>
                                <select class="select2 form-select" id="task-assigned" name="owner">
                                    <option value="">{{ __('governance.selectOwner') }} </option>
                                    @foreach ($owners as $owner)
                                        <option value="{{ $owner->id }}"> {{ $owner->name }} </option>
                                    @endforeach

                                </select>
                            </div>



                            <!-- //add test start-->

                            <div class="mb-1">
                                <label class="form-label " for="select2-basic1">{{ __('locale.Tester') }}</label>
                                <select class="select2 form-select" name="tester">
                                    <option value="" disabled selected>{{ __('locale.select-option') }}
                                    </option>
                                    @foreach ($testers as $tester)
                                        <option value="{{ $tester->id }}">{{ $tester->name }}</option>
                                    @endforeach

                                </select>
                                <span class="error error-tester "></span>
                            </div>

                            {{-- <div class="mb-1">
                                <label class="form-label"
                                    for="basic-icon-default-post">{{ __('locale.TestName') }}</label>
                                <input type="text" name="test_name" id="basic-icon-default-post"
                                    class="form-control dt-post" aria-label="Web Developer" required />
                                <span class="error error-test_name "></span>
                            </div> --}}

                            <!-- <div class="mb-1">
                                                                                                                                                                                                                                        <label class="form-label" for="additional_stakeholders"> AdditionalStakeholders </label>
                                                                                                                                                                                                                                        <select name="additional_stakeholders[]" class="form-select multiple-select2" id="additional_stakeholders" multiple="multiple">
                                                                                                                                                                                                                                          <option value=""> select-option </option>
                                                                                                                                                                                                                                           @foreach ($testers as $tester)
<option value="{{ $tester->id }}">{{ $tester->name }}</option>
@endforeach

                                                                                                                                                                                                                                        </select>
                                                                                                                                                                                                                                        <span class="error error-additional_stakeholders" ></span>
                                                                                                                                                                                                                                      </div>
                                                                                                                                                                                                                                      <div class="mb-1">
                                                                                                                                                                                                                                        <label class="form-label" for="teams">  Teams </label>
                                                                                                                                                                                                                                        <select name="teams[]" class="form-select multiple-select2" id="teams" multiple="multiple">
                                                                                                                                                                                                                                          <option value="" >select teams </option>
                                                                                                                                                                                                                                           @foreach ($teams as $team)
<option value="{{ $team->id }}">{{ $team->name }}</option>
@endforeach
                                                                                                                                                                                                                                        </select>
                                                                                                                                                                                                                                        <span class="error error-teams " ></span>
                                                                                                                                                                                                                                      </div> -->
                            <div class="mb-1">
                                <label class="form-label" for="normalMultiSelect1">{{ __('locale.TestFrequency') }}
                                    ({{ __('locale.days') }})</label>
                                <input name="test_frequency" type="number" min="0" class="form-control " />
                                <span class="error error-test_frequency "></span>
                            </div>
                            {{--
                            <div class=" mb-1">
                                <label class="form-label" for="fp-default"> {{ __('locale.LastTestDate') }}</label>
                                <input type="text" data-i="0" name="last_date" placeholder="YYYY-MM-DD "
                                    class="form-control js-datepicker">

                            </div> --}}
                            <div class="mb-1">
                                <label class="form-label"
                                    for="exampleFormControlTextarea1">{{ __('locale.TestSteps') }}</label>
                                <textarea class="form-control" name="test_steps" id="exampleFormControlTextarea1" rows="3"></textarea>
                                <span class="error error-test_steps "></span>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="normalMultiSelect1">
                                    {{ __('locale.ApproximateTime') }}
                                    ({{ __('locale.minutes') }})</label>
                                <input name="approximate_time" type="number" min="0"
                                    id="basic-icon-default-post" class="form-control dt-post"
                                    aria-label="Web Developer" />
                                <span class="error error-approximate_time "></span>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="exampleFormControlTextarea1">
                                    {{ __('locale.ExpectedResults') }}</label>
                                <textarea class="form-control" name="expected_results" id="exampleFormControlTextarea1" rows="3"></textarea>
                                <span class="error error-expected_results"></span>
                            </div>

                            <!--add test end -->

                        </div>
                        <div class="my-1">
                            <button type="submit" class="btn btn-primary   add-todo-item me-1">Add</button>
                            <!-- <button type="button" class="btn btn-outline-secondary add-todo-item "
                            data-bs-dismiss="modal">
                            Cancel
                        </button> -->

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- // edit control modal -->

<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModal"
    aria-hidden="true" id="edit_contModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myExtraLargeModal">{{ __('governance.UpdateControl') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- <div class="modal modal-slide-in sidebar-todo-modal fade" id="edit_contModal" role="dialog">
    <div class="modal-dialog sidebar-lg"> -->
            <div class="modal-content p-0">


                <!-- <div class="modal-header align-items-center mb-1">
                <h5 class="modal-title">{{ __('governance.UpdateControl') }}</h5>
                <div class="todo-item-action d-flex align-items-center justify-content-between ms-auto">
                    <span class="todo-item-favorite cursor-pointer me-75"><i data-feather="star"
                            class="font-medium-2"></i></span>
                    <i data-feather="x" class="cursor-pointer" data-bs-dismiss="modal" stroke-width="3"></i>
                </div>
            </div> -->

                <form id="update_form" class="todo-modal needs-validation" novalidate method="POST"
                    action="{{ route('admin.governance.control.update') }}">
                    @csrf

                    <div class="modal-body flex-grow-1 pb-sm-0 pb-3" id="form-modal-edit">

                    </div>


                </form>


            </div>
        </div>
    </div>
</div>

<!-- // map control modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModal"
    aria-hidden="true" id="empModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myExtraLargeModal">{{ __('governance.Mapping') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- <div class="modal modal-slide-in sidebar-todo-modal fade" id="empModal" role="dialog">
    <div class="modal-dialog sidebar-lg"> -->
            <div class="modal-content p-0">
                <!-- <div class="modal-header align-items-center mb-1">
                <h5 class="modal-title">{{ __('governance.Mapping') }}</h5>
                <div class="todo-item-action d-flex align-items-center justify-content-between ms-auto">
                    <span class="todo-item-favorite cursor-pointer me-75"><i data-feather="star"
                            class="font-medium-2"></i></span>
                    <i data-feather="x" class="cursor-pointer" data-bs-dismiss="modal" stroke-width="3"></i>
                </div>
            </div> -->
                <button type="button" class="btn btn-primary"
                    id="openSecondModal">{{ __('Mapping Controls') }}</button>

                <div style="margin-top: 23px;" class="modal-body flex-grow-1 pb-sm-0 pb-3" id="form-modal-map"></div>
            </div>
        </div>
    </div>
</div>

<!-- Second Modal -->
<div class="modal fade" id="secondModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('governance.Mapping') }}</h5>
                <div class="todo-item-action d-flex align-items-center justify-content-between ms-auto">
                    <span class="todo-item-favorite cursor-pointer me-75"><i data-feather="star"
                            class="font-medium-2"></i></span>
                    <i data-feather="x" class="cursor-pointer" data-bs-dismiss="modal" stroke-width="3"></i>
                </div>
            </div>
            <div class="modal-body">
                <form id="secondModalForm">
                    <input type="hidden" name="control_id" id="control_id">
                    <div class="form-group">
                        <label for="framework">{{ __('locale.Framework') }}</label>
                        <select class="form-control select2" id="frameworkControlsFilter">
                            <option disabled selected value="">{{ __('locale.select-option') }}</option>
                            @foreach ($frameworks as $framework)
                                <option value="{{ $framework['id'] }}">{{ $framework['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="controls">{{ __('locale.Control') }}</label>
                        <select class="form-control select2" id="controlsMapping">
                            <option disabled value="">{{ __('locale.select-option') }}</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" form="secondModalForm"
                    class="btn btn-primary">{{ __('Save changes') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- // List Objectives Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModal"
    aria-hidden="true" id="objectiveModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('governance.Requirements') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- <div class="modal modal-slide-in sidebar-todo-modal fade" id="objectiveModal" role="dialog">
    <div class="modal-dialog sidebar-lg" style="width:1200px"> -->
            <div class="modal-content p-0">


                <!-- <div class="modal-header align-items-center mb-1">
                <h5 class="modal-title">{{ __('governance.Requirements') }}</h5>
                <div class="todo-item-action d-flex align-items-center justify-content-between ms-auto">
                    <i data-feather="x" class="cursor-pointer" data-bs-dismiss="modal" stroke-width="3"></i>
                </div>
            </div> -->

                <div class="modal-body flex-grow-1 pb-sm-0 pb-3">
                    <div>
                        <h3 style="display: inline-block">{{ __('governance.Control') }} :</h3>
                        <h3 style="display: inline-block" id="controlName"></h3>

                    </div>
                    <br>
                    <div id="objectivesList">

                    </div>
                    <br>
                    <div class="row">


                        @if (auth()->user()->hasPermission('control.add_objectives'))
                            <div class="text-center col-md-6">
                                <button class="btn btn-success"
                                    id="addObjective">{{ __('governance.AddRequirement') }}</button>
                            </div>
                        @endif
                        <div class="text-center col-md-6">
                            <button class="btn btn-primary"
                                id="controlguide">{{ __('locale.ControlGuideImplementation') }}</button>
                        </div>
                    </div> <br>
                    <div id ="control-guide-value">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- // Add Objective Modal -->

<div class="modal fade" tabindex="-1" aria-hidden="true" id="addObjectiveModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-2 px-md-5 pb-3">
                <div class="text-center mb-4">
                    <h1 class="role-title">{{ __('governance.AddRequirement') }}</h1>
                </div>
                <!-- Evidence form -->
                <form class="row addObjectiveToControlForm" onsubmit="return false" enctype="multipart/form-data">
                    <input type="hidden" name="control_id">
                    <input type="hidden" name="objective_adding_type" value="existing">
                    @csrf
                    <div class="col-12 objective_id_container">
                        {{-- objective id --}}
                        <div class="mb-1">
                            <label class="form-label ">{{ __('governance.Requirement') }}</label>
                            <a href="javascript:;"
                                onclick="showAddNewObjectiveInputs()">{{ __('governance.AddNewRequirement') }}?</a>
                            <select class="select2 form-select" name="objective_id">
                                <option value="" selected>{{ __('locale.select-option') }}</option>
                            </select>
                            <span class="error error-objective_id"></span>
                            <span class="error error-control_id"></span>
                        </div>
                    </div>
                    <div class="col-12  objective_name_container" style="display: none;">
                        {{-- objective Name --}}
                        <div class="mb-1">
                            <label class="form-label ">{{ __('governance.RequirementName') }}</label>
                            <a onclick="showSelectExistingObjectiveInputs()"
                                href="javascript:;">{{ __('locale.SelectExistingRequirement') }}?</a>
                            <input type="text" class="form-control" name="objective_name" />
                            <span class="error error-objective_name"></span>
                            <span class="error error-control_id"></span>
                        </div>
                    </div>
                    <div class="col-12 objective_description_container" style="display: none;">
                        {{-- objective Descriotion --}}
                        <div class="mb-1">
                            <label class="form-label ">{{ __('governance.RequirementDescription') }}</label>
                            <textarea name="objective_description" class="form-control"></textarea>
                            <span class="error error-objective_description"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        {{-- Responsible Type --}}
                        <div class="mb-1">
                            <label for="title" class="form-label">{{ __('governance.ResponsibleType') }}</label>
                            <div class="demo-inline-spacing">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="responsible_type"
                                        id="user" value="user" checked />
                                    <label class="form-check-label" for="user">{{ __('locale.User') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="responsible_type"
                                        id="manager" value="manager" />
                                    <label class="form-check-label"
                                        for="manager">{{ __('locale.DepartmentManager') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="responsible_type"
                                        id="team" value="team" />
                                    <label class="form-check-label" for="team">{{ __('locale.Team') }}</label>
                                </div>
                            </div>
                            <span class="error error-responsible_type"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        {{-- Responsible --}}
                        <div class="mb-1">
                            <label class="form-label ">{{ __('locale.Responsible') }}
                                <small>({{ __('governance.ControlOwnerWillBeResponsibleIfYouDidntSelectOne') }})</small></label>
                            <select class="select2 form-select" name="responsible_id">
                                <option value="" selected>{{ __('locale.select-option') }}</option>
                            </select>
                            <span class="error error-responsible_id"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        {{-- due date --}}
                        <div class="mb-1">
                            <label class="form-label ">{{ __('locale.DueDate') }}</label>
                            <input name="due_date" class="form-control flatpickr-date-time-compliance"
                                placeholder="YYYY-MM-DD" />
                            <span class="error error-due_date"></span>
                        </div>
                    </div>

                    <div class="col-12 text-center mt-2">
                        <button type="Submit" class="btn btn-primary me-1"> {{ __('locale.Submit') }}</button>
                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('locale.Cancel') }}</button>
                    </div>
                </form>
                <!--/ Evidence form -->
            </div>
        </div>
    </div>
</div>
<!-- // edit objective Modal -->

<div class="modal fade" tabindex="-1" aria-hidden="true" id="editObjectiveModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-2 px-md-5 pb-3">
                <div class="text-center mb-4">
                    <h1 class="role-title">{{ __('locale.EditRequirement') }}</h1>
                </div>
                <!-- edit objective form -->
                <form class="row editObjectiveForm" onsubmit="return false" enctype="multipart/form-data">
                    <input type="hidden" name="control_control_objective_id">
                    @csrf
                    <div class="col-12">
                        {{-- Responsible Type --}}
                        <div class="mb-1">
                            <label for="title" class="form-label">{{ __('governance.ResponsibleType') }}</label>
                            <div class="demo-inline-spacing">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="edited_responsible_type"
                                        id="edited_user" value="user" checked />
                                    <label class="form-check-label" for="user">{{ __('locale.User') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="edited_responsible_type"
                                        id="edited_manager" value="manager" />
                                    <label class="form-check-label"
                                        for="manager">{{ __('locale.DepartmentManager') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="edited_responsible_type"
                                        id="edited_team" value="team" />
                                    <label class="form-check-label" for="team">{{ __('locale.Team') }}</label>
                                </div>
                            </div>
                            <span class="error error-edited_responsible_type"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        {{-- Responsible --}}
                        <div class="mb-1">
                            <label class="form-label ">{{ __('locale.Responsible') }}
                                <small>({{ __('governance.ControlOwnerWillBeResponsibleIfYouDidntSelectOne') }})</small></label>
                            <select class="select2 form-select" name="edited_responsible_id">
                                <option value="" selected>{{ __('locale.select-option') }}</option>
                            </select>
                            <span class="error error-edited_responsible_id"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        {{-- due date --}}
                        <div class="mb-1">
                            <label class="form-label ">{{ __('locale.DueDate') }}</label>
                            <input name="edited_due_date" class="form-control flatpickr-date-time-compliance"
                                placeholder="YYYY-MM-DD" />
                            <span class="error error-edited_due_date"></span>
                        </div>
                    </div>

                    <div class="col-12 text-center mt-2">
                        <button type="Submit" class="btn btn-primary me-1"> {{ __('locale.Submit') }}</button>
                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('locale.Cancel') }}</button>
                    </div>
                </form>
                <!--/ Edit Objective form -->
            </div>
        </div>
    </div>
</div>

<!-- // Add Evidence Modal -->

<div class="modal fade" tabindex="-1" aria-hidden="true" id="addEvidenceModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-2 px-md-5 pb-3">
                <div class="text-center mb-4">
                    <h1 class="role-title">{{ __('governance.AddEvidence') }}</h1>
                </div>
                <!-- Evidence form -->
                <form class="row addEvidenceToObjectiveForm" onsubmit="return false" enctype="multipart/form-data">
                    <input type="hidden" name="control_control_objective_id">
                    @csrf
                    <div class="col-12">
                        {{-- Evidence Description --}}
                        <div class="mb-1">
                            <label class="form-label ">{{ __('governance.EvidenceDescription') }}</label>
                            <input class="form-control" type="text" name="evidence_description">
                            <span class="error error-evidence_description"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        {{-- File Attachment --}}
                        <div class="mb-1">
                            <label class="form-label">{{ __('governance.EvidenceFile') }}</label>
                            <input type="file" name="evidence_file" class="form-control dt-post"
                                aria-label="{{ __('locale.file') }}" />
                            <span class="error error-evidence_file "></span>
                        </div>
                    </div>

                    <div class="col-12 text-center mt-2">
                        <button type="Submit" class="btn btn-primary me-1"> {{ __('locale.Submit') }}</button>
                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('locale.Cancel') }}</button>
                    </div>
                </form>
                <!--/ Evidence form -->
            </div>
        </div>
    </div>
</div>

<!-- // List Evidences Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModal"
    aria-hidden="true" id="evidencesModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myExtraLargeModal">{{ __('locale.Evidences') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- <div class="modal modal-slide-in sidebar-todo-modal fade" id="evidencesModal" role="dialog">
    <div class="modal-dialog sidebar-lg" style="width:1200px"> -->
            <div class="modal-content p-0">


                <!-- <div class="modal-header align-items-center mb-1">
                <h5 class="modal-title">{{ __('locale.Evidences') }}</h5>
                <div class="todo-item-action d-flex align-items-center justify-content-between ms-auto">
                    <i data-feather="x" class="cursor-pointer" data-bs-dismiss="modal" stroke-width="3"></i>
                </div>
            </div> -->

                <div class="modal-body flex-grow-1 pb-sm-0 pb-3">
                    <div>
                        <h3 style="display: inline-block">{{ __('governance.Control') }} :</h3>
                        <h3 style="display: inline-block" id="evidenceControlName"> </h3>
                        <h3 style="display: inline-block"> / {{ __('governance.Requirement') }} :</h3>
                        <h3 style="display: inline-block" id="evidenceObjectiveName"></h3>

                    </div>
                    <br>
                    <div id="evidencesList">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- // Edit Evidence Modal -->

<div class="modal fade" tabindex="-1" aria-hidden="true" id="editEvidenceModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-2 px-md-5 pb-3">
                <div class="text-center mb-4">
                    <h1 class="role-title">{{ __('governance.EditEvidence') }}</h1>
                </div>
                <!-- Evidence form -->
                <form class="row editEvidenceForm" onsubmit="return false" enctype="multipart/form-data">
                    @csrf
                    <input class="form-control" type="hidden" name="evidence_id">
                    <div class="col-12">
                        {{-- Evidence Description --}}
                        <div class="mb-1">
                            <label class="form-label ">{{ __('governance.EvidenceDescription') }}</label>
                            <input type="text" class="form-control" name="edited_evidence_description">
                            <span class="error error-edited_evidence_description"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        {{-- File Attachment --}}
                        <div class="mb-1">
                            <label class="form-label">{{ __('governance.EvidenceFile') }}</label>

                            <input type="file" name="edited_evidence_file" class="form-control dt-post"
                                aria-label="{{ __('locale.file') }}" />
                            <span class="error error-edited_evidence_file "></span>
                        </div>
                        <div class="mb-1 last_uploaded_file_container" style="display: hidden;">
                            <label class="form-label">{{ __('locale.LastUploadedFile') }}</label>
                            <a class="badge bg-secondary last_uploaded_file cursor-pointer text-light"></a>
                        </div>
                    </div>

                    <div class="col-12 text-center mt-2">
                        <button type="Submit" class="btn btn-primary me-1"> {{ __('locale.Submit') }}</button>
                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('locale.Cancel') }}</button>
                    </div>
                </form>
                <!--/ Evidence form -->
            </div>
        </div>
    </div>
</div>
<!-- // View Evidence Modal -->

<div class="modal fade" tabindex="-1" aria-hidden="true" id="viewEvidenceModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-2 px-md-5 pb-3">
                <div class="text-center mb-4">
                    <h1 class="role-title">{{ __('locale.ViewEvidence') }}</h1>
                </div>
                <div class="row">
                    <div class="col-12">
                        {{-- Evidence Description --}}
                        <div class="mb-1">
                            <label class="form-label ">{{ __('governance.EvidenceDescription') }}</label>
                            <input class="form-control view_evidence_description" disabled>
                        </div>
                    </div>
                    <div class="col-12 view_evidence_file_container">
                        {{-- File Attachment --}}
                        <div class="mb-1">
                            <label class="form-label">{{ __('governance.EvidenceFile') }}</label>
                            <a class="badge bg-secondary view_evidence_file cursor-pointer text-light"></a>
                        </div>
                    </div>

                    <div class="col-12">
                        {{-- Evidence Description --}}
                        <div class="mb-1">
                            <label class="form-label ">{{ __('locale.CreatedBy') }}</label>
                            <input class="form-control view_evidence_created_by" disabled>

                        </div>
                    </div>
                    <div class="col-12">
                        {{-- Evidence Description --}}
                        <div class="mb-1">
                            <label class="form-label ">{{ __('locale.CreatedAt') }} </label>
                            <input class="form-control view_evidence_created_at" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal HTML -->
<div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog"
aria-labelledby="descriptionModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="descriptionModalLabel">{{ __('locale.Description') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="modalDescriptionContent">
            <!-- Description content will be injected here -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary"
                data-bs-dismiss="modal">{{ __('locale.Close') }}</button>
        </div>
    </div>
</div>
</div>

<!-- // Objective Comments Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModal"
    aria-hidden="true" id="objectiveCommentsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myExtraLargeModal">{{ __('locale.Comments') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- <div class="modal modal-slide-in sidebar-todo-modal fade" id="objectiveCommentsModal" role="dialog">
    <div class="modal-dialog sidebar-lg"> -->
            <div class="modal-content p-0">


                <!-- <div class="modal-header align-items-center mb-1">
                <h5 class="modal-title">{{ __('locale.Comments') }}</h5>
                <div class="todo-item-action d-flex align-items-center justify-content-between ms-auto">
                    <i data-feather="x" class="cursor-pointer" data-bs-dismiss="modal" stroke-width="3"></i>
                </div>
            </div> -->

                <div class="modal-body flex-grow-1 pb-sm-0 pb-3">
                    <div id="chat-container">
                        <!-- Main chat area -->
                        <section class="chat-app-window">
                            <!-- To load Conversation -->

                            <!--/ To load Conversation -->
                            <!-- Active Chat -->
                            @if (auth()->user()->role->name == 'Administrator')
                                <div class="text-center mb-1">
                                    <a href="javascript:" class="btn btn-danger clearCommentsBtn"
                                        title="Clear Comments">
                                        {{ __('governance.ClearComments') }}
                                    </a>
                                </div>
                            @endif
                            <div class="active-chat">
                                <!-- User Chat messages -->
                                <div class="user-chats">
                                    <div class="chats">
                                    </div>
                                </div>
                                <!-- User Chat messages -->
                                <p class="my-0 mx-2 file-name"
                                    data-content="{{ __('locale.FileName', ['name' => '']) }}">
                                </p>
                                <!-- Submit Chat form -->
                                <form class="chat-app-form" id="chat-app-form" action="javascript:void(0);"
                                    onsubmit="enterChat('#objectiveCommentsModal');">
                                    @csrf
                                    <input type="hidden" name="control_control_objective_id" />
                                    <div class="input-group input-group-merge me-1 form-send-message">
                                        <input type="text" class="form-control message" name ="comment"
                                            placeholder="{{ __('locale.TypeYourComment') }}" />
                                        <span class="input-group-text" title="hhhh">
                                            <label for="attach-doc" class="attachment-icon form-label mb-0">
                                                <i data-feather="file" class="cursor-pointer text-secondary"></i>
                                                <input name="comment_file" type="file" class="attach-doc"
                                                    id="attach-doc" hidden /> </label></span>
                                    </div>
                                    <button type="submit" class="btn btn-primary send">
                                        {{-- <i data-feather="send" class="d-lg-none"></i> --}}
                                        <i data-feather="send"></i>
                                        {{-- <span class="d-none d-lg-block">Send</span> --}}
                                    </button>
                                </form>
                                <!--/ Submit Chat form -->
                            </div>
                            <!--/ Active Chat -->
                        </section>
                        <!--/ Main chat area -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- // Group Audit Modal -->

<div class="modal fade" tabindex="-1" aria-hidden="true" id="initiateAuditsForFrameworkControlsModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-2 px-md-5 pb-3">
                <div class="text-center mb-4">
                    <h1 class="role-title">{{ __('locale.Initiate Audits') }}</h1>
                </div>
                <!-- Evidence form -->
                <form class="row initiateAuditsForFrameworkControlsForm" onsubmit="return false"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="col-12">
                        {{-- Evidence Description --}}
                        <div class="mb-1">
                            <label class="form-label">{{ __('governance.Framework') }}</label>
                            <select class="form-control dt-input dt-select select2 " name="audits_framework_id">
                                <option value="">{{ __('locale.select-option') }}</option>
                                @foreach ($frameworks as $framework)
                                    <option value="{{ $framework['id'] }}">
                                        {{ $framework['name'] }}</option>
                                @endforeach
                            </select>
                            <span class="error error-audits_framework_id"></span>
                        </div>
                    </div>

                    <div class="col-12 text-center mt-2">
                        <button type="Submit" class="btn btn-primary me-1"> {{ __('locale.Submit') }}</button>
                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('locale.Cancel') }}</button>
                    </div>
                </form>
                <!--/ Evidence form -->
            </div>
        </div>
    </div>
</div>
@endsection
@section('vendor-script')
<script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
{{-- <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script> --}}
<script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
<script src="{{ asset('cdn/picker.js') }}"></script>
<script src="{{ asset('cdn/picker.date.js') }}"></script>


@endsection

@section('page-script')
<script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
<script>
    var permission = [],
        lang = [],
        URLs = [],
        user_id = {{ auth()->id() }},
        customUserName =
        "{{ getFirstChartacterOfEachWord(auth()->user()->name, 2) }}";
    userName = "{{ auth()->user()->name }}";
    user_id = {{ auth()->id() }}, customUserName =
        "{{ getFirstChartacterOfEachWord(auth()->user()->name, 2) }}";
    userName = "{{ auth()->user()->name }}";
    URLs['ajax_list'] = "{{ route('admin.governance.ajax.get-list-control') }}";
    URLs['sendObjectiveComment'] = "{{ route('admin.governance.control.ajax.objective.sendComment') }}";
    URLs['downloadObjectiveCommentFile'] =
        "{{ route('admin.governance.control.ajax.objective.downloadCommentFile', '') }}";
    permission['edit'] = {{ auth()->user()->hasPermission('control.update') ? 1 : 0 }};
    permission['delete'] = {{ auth()->user()->hasPermission('control.delete') ? 1 : 0 }};
    permission['audits.create'] = {{ auth()->user()->hasPermission('audits.create') ? 1 : 0 }};
    permission['list_objectives'] = {{ auth()->user()->hasPermission('control.list_objectives') ? 1 : 0 }};

    lang['DetailsOfItem'] = "{{ __('locale.DetailsOfItem', ['item' => __('locale.department')]) }}";
    lang['Edit'] = "{{ __('locale.Edit') }}";
    lang['Objective'] = "{{ __('locale.Requirements') }}";
    lang['Mapping'] = "{{ __('governance.Mapping') }}";
    lang['Delete'] = "{{ __('locale.Delete') }}";
    lang['Audit'] = "{{ __('governance.Audit') }}";
    lang['user'] = "{{ __('locale.User') }}"



    // edit control
    function editControl(data) {
        var url = "{{ route('admin.governance.ajax.edit_control', '') }}" + "/" + data;

        // AJAX request
        $.ajax({
            url: url,
            type: "GET",
            data: {},
            success: function(response) {
                $('.dtr-bs-modal').modal('hide');
                $('#edit_contModal').modal('show');
                $('#form-modal-edit').html(response);

                // Check if the element exists before creating the Quill instance
                var quillContainer = document.getElementById('control_guide_implementation');
                if (quillContainer) {
                    var quill = new Quill(quillContainer, {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{
                                    'header': [1, 2, 3, 4, 5, 6, false]
                                }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{
                                    'list': 'ordered'
                                }, {
                                    'list': 'bullet'
                                }],
                                [{
                                    'indent': '-1'
                                }, {
                                    'indent': '+1'
                                }],
                                [{
                                    'direction': 'rtl'
                                }], // Right-to-left direction
                                ['clean'],
                            ],
                        },
                    });

                    // Retrieve Quill content and set it in the hidden input


                    var quillContent = quill.root.innerHTML;
                    $('#supplemental_guidance_input').val(quillContent);
                }

                $('#form-modal-edit').find('.select2').select2();
            }
        });
    }

    // mapping


    function deleteControl(data) {
        var url = "{{ route('admin.governance.control.destroy', '') }}" + "/" + data;
        // AJAX request
        $.ajax({
            url: url,
            type: "GET",
            data: {},
            success: function(data) {
                if (data.status) {
                    makeAlert('success', data.message, "{{ __('locale.Success') }}");
                    $('.dtr-bs-modal').modal('hide');
                    redrawDatatable();
                }
            },
            error: function(response, data) {
                responseData = response.responseJSON;
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
            }
        });
    }


    function mapControl(data) {
        var url = "{{ route('admin.governance.ajax.get-list_control-map', '') }}" + "/" + data;
        // AJAX request
        $.ajax({
            url: url,
            type: "GET",
            data: {},
            success: function(response) {
                // Insert the id of control at input hidden to make the user can select the control in it 
                $('#secondModal input[name="control_id"]').val(data);

                // Prepare HTML for the table
                var tableHtml = `
                <table style="margin-top:23px font" class="table table-responsive-md table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Control</th>
                            <th scope="col">Framework</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                // Populate table rows
                response.controls.forEach(function(item) {
                    tableHtml += `
                    <tr class="fw-bold" style="font-size: 11px;">
                        <td>${item.control_name}</td>
                        <td>${item.framework_name}</td>
                        <td>
                            <a href="javascript:;" onclick="deleteControlmap(${item.control_id}, ${response.id})" data-toggle="tooltip" data-original-title="Delete" class="btn btn-outline-danger btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </a>
                        </td>
                    </tr>
                `;
                });

                tableHtml += `
                    </tbody>
                </table>
            `;
                // Show the modal and insert the table into the modal body
                $('#empModal').modal('show');
                $('#form-modal-map').html(tableHtml);

                // Attach delete event handler
                $('.delete-control').click(function() {
                    var controlId = $(this).data('control-id');
                    deleteControlmap(controlId, response
                        .id); // Pass the controlId and requestId to the delete function
                });
            },
            error: function(xhr, status, error) {
                // Handle errors if necessary
                console.error('An error occurred:', status, error);
            }
        });
    }

    function deleteControlmap(controlId, requestId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX request to delete the control
                $.ajax({
                    url: "{{ route('admin.governance.control.ajax.objective.deleteMappingControl') }}",
                    type: "POST",
                    data: {
                        control_id: controlId,
                        child_control_id: requestId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.status) {
                            Swal.fire(
                                'Deleted!',
                                data.message,
                                'success'
                            );
                            // Refresh the table
                            var controlId = $('#secondModal input[name="control_id"]').val();
                            mapControl(controlId);
                        } else {
                            Swal.fire(
                                'Error!',
                                data.message,
                                'error'
                            );
                        }
                    },
                    error: function(response) {
                        var responseData = response.responseJSON;
                        Swal.fire(
                            'Error!',
                            responseData.message,
                            'error'
                        );
                    }
                });
            }
        });
    }



    function showControlObjectives(data) {
        var url = "{{ route('admin.governance.control.ajax.objective.get', '') }}" + "/" + data;

        // AJAX request
        $.ajax({
            url: url,
            type: "GET",
            data: {},
            success: function(response) {
                control = response.control;
                objectives = response.objectives;
                $('#objectivesList').empty();
                $('#controlName').html(control.short_name)
                $('#addObjective').attr('onClick', 'showAddObjectiveForm(' + control.id + ');')
                $('#controlguide').attr('onClick', 'getControlGuide(' + control.id + ');')
                $('#control-guide-value').html('');
                if (objectives.length) {
                    publishTableWithObjectives(objectives)
                } else {
                    html = '<h4 style="text-align:center; color:red">No Requirements Yet<h4>'
                    $('#objectivesList').html(html);
                }
                $('#objectiveModal').modal('show');
            }
        });

    }

    function showEditObjectiveForm(controlControlObjectiveId) {
        var url = "{{ route('admin.governance.control.ajax.objective.editObjective', '') }}" + "/" +
            controlControlObjectiveId;
        $('[name="control_control_objective_id"]').val(controlControlObjectiveId)
        
        // AJAX request
        $.ajax({
            url: url,
            type: "GET",
            data: {},
            success: function(response) {
                objective = response.objective;
                responsibles = response.responsibles;
                
                // Initialize responsibleId as null
                var responsibleId = null;
                
                // Handle responsible type with null checks
                if (objective.responsible_type == 'user') {
                    $("input[name='edited_responsible_type'][value='user']").prop("checked", true);
                    responsibleId = objective.responsible_id || null;
                } else if (objective.responsible_type == 'manager') {
                    $("input[name='edited_responsible_type'][value='manager']").prop("checked", true);
                    responsibleId = objective.responsible_id || null;
                } else if (objective.responsible_type == 'team') {
                    $("input[name='edited_responsible_type'][value='team']").prop("checked", true);
                    responsibleId = objective.responsible_team_id || null;
                } else {
                    // Handle unexpected responsible_type or unset it
                    $("input[name='edited_responsible_type']").prop("checked", false);
                }
                
                // Build options with safe handling
                var responsiblesOptions = '<option value="" selected>{{ __('locale.select-option') }}</option>';
                
                if (responsibles && responsibles.length) {
                    $.each(responsibles, function(index, responsible) {
                        // Only add selected if responsibleId is not null and matches
                        const isSelected = (responsibleId !== null && responsible.id == responsibleId);
                        responsiblesOptions += `<option value="${responsible.id}" ${isSelected ? 'selected' : ''}>${responsible.name}</option>`;
                    });
                }
                
                $('[name="edited_responsible_id"]').html(responsiblesOptions);
                $('[name="edited_due_date"]').val(objective.due_date || '');
                $('#editObjectiveModal').modal('show');
            },
            error: function(xhr) {
                // Handle AJAX errors
                console.error("Error loading objective data:", xhr.responseText);
                alert("Failed to load objective data. Please try again.");
            }
        });
    }

    $('.editObjectiveForm').submit(function(e) {
        e.preventDefault();
        $('.error').empty();
        var url = "{{ route('admin.governance.control.ajax.objective.updateObjective') }}";
        $.ajax({
            url: url,
            type: 'POST',
            data: $('.editObjectiveForm').serialize(),
            success: function(data) {
                if (data.status) {
                    objectives = data.data;
                    makeAlert('success', data.message, "{{ __('locale.Success') }}");
                    publishTableWithObjectives(objectives);
                    $('#editObjectiveModal').modal('hide');
                } else {
                    showError(data['errors']);
                }
            },
            error: function(response, data) {
                responseData = response.responseJSON;
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
                showError(responseData.errors);
            }


        });

    });

    function showAddObjectiveForm(control_id) {

        showSelectExistingObjectiveInputs();
        var url = "{{ route('admin.governance.control.ajax.objective.getAll', '') }}" + "/" + control_id;
        $('[name="control_id"]').val(control_id);
        $("input[name='responsible_type'][value='user']").prop("checked", true);
        $("input[name='due_date']").val('');

        $('.error').empty();
        // AJAX request
        $.ajax({
            url: url,
            type: "GET",
            data: {},
            success: function(response) {

                objectives = response.objectives;
                users = response.users;
                if (objectives.length) {
                    var objectivesOptions =
                        '<option value="" selected>{{ __('locale.select-option') }}</option>';
                    $.each(objectives, function(index, objective) {
                        objectivesOptions += '<option value="' + objective.id + '"' + (objective
                                .disabled ? 'disabled' : '') + '>' + objective
                            .name + '</option>'
                    });
                    $('[name="objective_id"]').html(objectivesOptions);

                }
                if (users.length) {
                    var usersOptions =
                        '<option value="" selected>{{ __('locale.select-option') }}</option>';
                    $.each(users, function(index, user) {
                        usersOptions += '<option value="' + user.id + '">' + user
                            .name + '</option>'
                    });
                    $('[name="responsible_id"]').html(usersOptions);

                }
                $('#addObjectiveModal').modal('show');
            }
        });
    }


    function getControlGuide(control_id) {

        var url = "{{ route('admin.governance.control.ajax.objective.getControlGuide', '') }}" + "/" + control_id;
        var elementText = $('#control-guide-value').text();

        if ($.trim(elementText).length === 0) {


            $.ajax({
                url: url,
                type: "GET",
                data: {},
                success: function(response) {
                    $('#control-guide-value').html(response);
                }
            });
        } else {
            // Element contains text
            $('#control-guide-value').html('');
        }
    }



    $('.addObjectiveToControlForm').submit(function(e) {
        e.preventDefault();
        $('.error').empty();
        var url = "{{ route('admin.governance.control.ajax.objective.addObjectiveToControl') }}";
        $.ajax({
            url: url,
            type: 'POST',
            data: $('.addObjectiveToControlForm').serialize(),
            success: function(data) {
                if (data.status) {
                    objectives = data.data;
                    makeAlert('success', data.message, "{{ __('locale.Success') }}");
                    publishTableWithObjectives(objectives);
                    $('#addObjectiveModal').modal('hide');
                } else {
                    showError(data['errors']);
                }
            },
            error: function(response, data) {
                responseData = response.responseJSON;
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
                showError(responseData.errors);
            }


        });

    });



    function showAddEvidenceForm(controlControlObjectiveId) {
        $('[name="control_control_objective_id"]').val(controlControlObjectiveId);
        $('#addEvidenceModal').modal('show');
    }

    $('.addEvidenceToObjectiveForm').submit(function(e) {
        var formData = new FormData(document.querySelector('.addEvidenceToObjectiveForm'));
        e.preventDefault();
        $('.error').empty();
        var url = "{{ route('admin.governance.control.ajax.objective.storeEvidence') }}";
        $.ajax({
            url: url,
            type: 'POST',
            contentType: false,
            processData: false,
            data: formData,
            success: function(data) {
                if (data.status) {
                    makeAlert('success', data.message, "{{ __('locale.Success') }}");
                    $('#addEvidenceModal').modal('hide');
                    $('[name="control_control_objective_id"]').val('');
                    $('[name="evidence_description"]').val('');
                    $('[name="evidence_file"]').val('');

                } else {
                    showError(data['errors']);
                }
            },
            error: function(response, data) {
                responseData = response.responseJSON;
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
                showError(responseData.errors);
            }


        });

    });

    function showEvidencesList(objectiveControlId) {
        var url = "{{ route('admin.governance.control.ajax.objective.getEvidences', '') }}" + "/" +
            objectiveControlId;

        // AJAX request
        $.ajax({
            url: url,
            type: "GET",
            data: {},
            success: function(response) {
                controlName = response.control_name;
                objectiveName = response.objective_name;
                evidences = response.evidences;
                canEditEvidences = response.can_edit_evidences
                $('#evidencesList').empty();
                $('#evidenceControlName').html(controlName)
                $('#evidenceObjectiveName').html(objectiveName)
                if (evidences.length) {
                    publishTableWithEvidences(evidences, canEditEvidences)
                } else {
                    html = '<h4 style="text-align:center; color:red">No Evidences Yet<h4>'
                    $('#evidencesList').html(html);
                }
                $('#evidencesModal').modal('show');
            }
        });



    }

    function showEvidenceData(evidenceId) {
        var url = "{{ route('admin.governance.control.ajax.objective.getEvidence', '') }}" + "/" + evidenceId;

        // AJAX request
        $.ajax({
            url: url,
            type: "GET",
            data: {},
            success: function(response) {
                evidence = response;

                const date = new Date(evidence.created_at);
                // Convert to local timezone
                date.setTime(date.getTime() + date.getTimezoneOffset() * 60 * 1000);

                // Format date
                const dateFormatted = date.toISOString().split('T')[0];
                $('.view_evidence_description').val(evidence.description);
                $('.view_evidence_created_by').val(evidence.created_by);
                $('.view_evidence_created_at').val(dateFormatted);

                if (evidence.file_name) {
                    $('.view_evidence_file').html(evidence.file_name);
                    // Change the onclick event to open the file in a new view
                    $('.view_evidence_file').attr('onclick', 'viewEvidenceFile(' + evidence.id + ')');
                    $('.view_evidence_file_container').show();
                } else {
                    $('.view_evidence_file').html('');
                    $('.view_evidence_file').attr('onclick', '');
                    $('.view_evidence_file_container').hide();
                }

                $('#viewEvidenceModal').modal('show');
            }
        });
    }

    function viewEvidenceFile(evidenceId) {
        // Open the new view in a new tab to display the file
        var url = "{{ route('admin.compliance.ajax.download_evidence_file', '') }}" + "/" + evidenceId;
        window.open(url, '_blank', 'noopener,noreferrer');
    }

    function showEditEvidenceForm(evidenceId) {
        var url = "{{ route('admin.governance.control.ajax.objective.getEvidence', '') }}" + "/" +
            evidenceId;

        // AJAX request
        $.ajax({
            url: url,
            type: "GET",
            data: {},
            success: function(response) {
                evidence = response
                $('[name="evidence_id"]').val(evidence.id);
                $('[name="edited_evidence_description"]').val(evidence.description)
                if (evidence.file_name) {
                    $('a.last_uploaded_file').html(evidence.file_name);
                    $('a.last_uploaded_file').attr('onclick', 'downloadEvidenceFile(' + evidence.id + ')');
                    $('.last_uploaded_file_container').show();
                } else {
                    $('a.last_uploaded_file').html('');
                    $('a.last_uploaded_file').attr('onclick', '');
                    $('.last_uploaded_file_container').hide();

                }
                $('#editEvidenceModal').modal('show');
            }
        });
    }

    $('.editEvidenceForm').submit(function(e) {
        var formData = new FormData(document.querySelector('.editEvidenceForm'));
        e.preventDefault();

        $('.error').empty();
        var url = "{{ route('admin.governance.control.ajax.objective.updateEvidence') }}";
        $.ajax({
            url: url,
            type: 'POST',
            contentType: false,
            processData: false,
            data: formData,
            success: function(data) {
                if (data.status) {
                    makeAlert('success', data.message, "{{ __('locale.Success') }}");
                    $('#editEvidenceModal').modal('hide');
                    $('[name="edited_evidence_description"]').val('');
                    $('[name="edited_evidence_file"]').val('');

                } else {
                    showError(data['errors']);
                }
            },
            error: function(response, data) {
                responseData = response.responseJSON;
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
                showError(responseData.errors);
            }


        });

    });

    function downloadEvidenceFile(evidenceId) {
        var url = "{{ route('admin.governance.control.ajax.objective.downloadEvidenceFile', '') }}" + "/" +
            evidenceId;
        var link = document.createElement("a");
        link.href = url;
        link.style.display = "none";
        document.body.appendChild(link);

        link.click();

        // Cleanup
        document.body.removeChild(link);
    }
</script>

<script>
    function publishTableWithObjectives(objectives) {
        let table = '';
        table += "<table width=100% class='table'>";
        table += "<tbody><tr>";
        table += "<th>#</th>";
        table += "<th>Requirement Name</th>";
        table += "<th>Requirement Description</th>";
        table += "<th>Responsible</th>";
        table += "<th>Due Date</th>";
        table += "<th style='width:25%;'>Actions</th>";
        table += "</tr>";
        $.each(objectives, function(index, objective) {
            let dropdownMenu = `
            <div class="dropdown">
                <a class="pe-1 dropdown-toggle hide-arrow text-primary" href="#" role="button" id="actionsDropdown${objective.pivot.id}" data-bs-toggle="dropdown" aria-expanded="false">
                    ${feather.icons["more-vertical"].toSvg({ class: "font-small-4" })}
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown${objective.pivot.id}">
        `;

            // List Evidences
            dropdownMenu += `
            <li>
                <a href="javascript:;" class="dropdown-item item-list" title="List Evidences" onclick="showEvidencesList(${objective.pivot.id})">
                    ${feather.icons["list"].toSvg({ class: "me-50 font-small-4" })} List Evidences
                </a>
            </li>
        `;

            // Add Evidence
            if (objective.canAddEvidence) {
                dropdownMenu += `
                <li>
                    <a href="javascript:;" class="dropdown-item item-edit" title="Add Evidence" onClick="showAddEvidenceForm(${objective.pivot.id})">
                        ${feather.icons["plus"].toSvg({ class: "me-50 font-small-4" })} Add Evidence
                    </a>
                </li>
            `;
            }

            // Edit Requirement
            if ({{ auth()->user()->hasPermission('control.add_objectives') ? 1 : 0 }}) {
                dropdownMenu += `
                <li>
                    <a href="javascript:;" class="dropdown-item item-edit" title="Edit Requirement" onClick="showEditObjectiveForm(${objective.pivot.id})">
                        ${feather.icons["edit"].toSvg({ class: "me-50 font-small-4" })} Edit Requirement
                    </a>
                </li>
            `;
            }

            // Delete Requirement
            if ({{ auth()->user()->hasPermission('control.add_objectives') ? 1 : 0 }}) {
                dropdownMenu += `
                <li>
                    <a href="javascript:;" class="dropdown-item item-edit" title="Delete Requirement" onClick="ShowModalDeleteObjective(${objective.pivot.id})">
                        ${feather.icons["trash-2"].toSvg({ class: "me-50 font-small-4" })} Delete Requirement
                    </a>
                </li>
            `;
            }

            // Comments
            dropdownMenu += `
            <li>
                <a href="javascript:;" class="dropdown-item item-edit" title="Comments" onClick="showModalObjectiveComments(${objective.pivot.id})">
                    ${feather.icons["message-square"].toSvg({ class: "me-50 font-small-4" })} Comments
                </a>
            </li>
        `;

            dropdownMenu += `</ul></div>`;

            const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${objective.name}</td>
                <td>${objective.description}</td>
                <td>${objective.responsible}</td>
                <td>${objective.due_date}</td>
                <td>${dropdownMenu}</td>
            </tr>
        `;
            table += row;
        });
        table += "</tbody></table>";
        $('#objectivesList').html(table);
    }


    function publishTableWithEvidences(evidences, canEditEvidences = false) {
        table = ''
        table += "<table width=100% class='table' >";
        table += "<tbody><tr> ";
        table += "<th>#</th> ";
        table += "<th>Created By</th> ";
        table += "<th>Created At</th> ";
        table += "<th>actions</th> ";
        table += "</tr>";
        $.each(evidences, function(index, evidence) {
            showEvidencesButton =
                '<a href="javascript:;" class="item-list " title="Show Evidence" onclick="showEvidenceData(' +
                evidence.id + ')">' +
                feather.icons["eye"].toSvg({
                    class: "me-1 font-small-4",
                }) +
                "</a>";
            if (canEditEvidences) {
                editEvidenceButton =
                    '<a  href="javascript:;" class="item-edit "title="Edit Evidence" onClick="showEditEvidenceForm(' +
                    evidence.id + ')">' +
                    feather.icons["edit"].toSvg({
                        class: "me-50 font-small-4",
                    }) +
                    "</a>";
            } else {
                editEvidenceButton = '';
            }

            if (canEditEvidences) {
                deleteEvidenceButton =
                    '<a  href="javascript:;" class="item-edit "title=Delete Evidence" onClick="ShowModalDeleteEvidence(' +
                    evidence.id + ')">' +
                    feather.icons["trash-2"].toSvg({
                        class: "me-50 font-small-4",
                    }) +
                    "</a>";
            } else {
                deleteEvidenceButton = '';
            }
            const date = new Date(evidence.created_at);

            // convert to local timezone
            date.setTime(date.getTime() + date.getTimezoneOffset() * 60 * 1000);

            // format date
            const dateFormatted = date.toISOString().split('T')[0];

            row = '<tr><td>' + (index + 1) + '</td><td>' + evidence.created_by +
                '</td><td>' + dateFormatted +
                '</td><td>' + showEvidencesButton + editEvidenceButton + deleteEvidenceButton + '</td></tr>';
            table += row;
        });
        $('#evidencesList').html(table);
    }

    function showAddNewObjectiveInputs() {
        $('.objective_name_container, .objective_description_container').show();
        $('.objective_id_container').hide();
        $('[name="objective_id"]').val('');
        $('[name="objective_adding_type"]').val('new');

    }

    function showSelectExistingObjectiveInputs() {
        $('.objective_id_container').show();
        $('.objective_name_container, .objective_description_container').hide();
        $('[name="objective_name"], [name="objective_description"]').val('');
        $('[name="objective_adding_type"]').val('existing');
    }


    $('[name="responsible_type"]').change(function(e) {
        var url = "{{ route('admin.governance.control.ajax.objective.getResponsibles') }}"
        var responsibleType = $('[name="responsible_type"]:checked').val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Retrieve CSRF token from meta tag

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                responsible_type: responsibleType,
                _token: csrfToken,
            },
            success: function(response) {
                var responsibles = response;
                var responsiblesOptions =
                    '<option value="" selected>{{ __('locale.select-option') }}</option>';
                $.each(responsibles, function(index, responsible) {
                    responsiblesOptions += '<option value="' + responsible.id + '">' +
                        responsible
                        .name + '</option>'
                });
                $('[name="responsible_id"]').html(responsiblesOptions);

            },


        });

    });

    $('[name="edited_responsible_type"]').change(function(e) {
        var url = "{{ route('admin.governance.control.ajax.objective.getResponsibles') }}"
        var responsibleType = $('[name="edited_responsible_type"]:checked').val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Retrieve CSRF token from meta tag

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                responsible_type: responsibleType,
                _token: csrfToken,
            },
            success: function(response) {
                var responsibles = response;
                var responsiblesOptions =
                    '<option value="" selected>{{ __('locale.select-option') }}</option>';
                $.each(responsibles, function(index, responsible) {
                    responsiblesOptions += '<option value="' + responsible.id + '">' +
                        responsible
                        .name + '</option>'
                });
                $('[name="edited_responsible_id"]').html(responsiblesOptions);

            },


        });

    });
</script>
<script>
    function formReset() {
        var form = $('.form-add_control')[0];
        form.reset();

        // Reset select elements to their default option
        $('.form-add_control select').each(function() {
            $(this).val($(this).find('option:first').val()); // Reset to the first option
        });
    }
    $('.form-add_control').submit(function(e) {
        e.preventDefault();
        $('.error').empty();

        // Retrieve Quill content
        var supplementalGuidance = quill.root.innerHTML;

        // Create a FormData object for the form
        var formData = new FormData(this);

        // Append Quill content to the FormData object
        formData.append('supplemental_guidance', supplementalGuidance);

        // Make the AJAX request
        $.ajax({
            url: $('.form-add_control').attr('action'),
            type: 'POST',
            data: formData, // Use the FormData object
            processData: false, // Important! Don't process the data
            contentType: false, // Important! Don't set content type
            success: function(data) {
                if (data.status) {
                    makeAlert('success', data.message, "{{ __('locale.Success') }}");
                    redrawDatatable();
                    // Close the form after success
                    $('.form-add_control').closest('.modal').modal(
                        'hide'); // Assuming your form is within a modal
                    formReset();
                } else {
                    showError(data.errors);
                }
            },
            error: function(response, data) {
                responseData = response.responseJSON;
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
                showError(responseData.errors);
            }
        });
    });




    $('#update_form').submit(function(e) {
        e.preventDefault();

        var quillContainer = document.getElementById('control_guide_implementation');
        if (quillContainer) {
            var quill = new Quill(quillContainer, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        [{
                            'direction': 'rtl'
                        }], // Right-to-left direction
                        ['clean'],
                    ],
                },
            });

            // Retrieve Quill content and set it in the hidden input


            var quillContent = quill.root.innerHTML;
            $('#supplemental_guidance_input').val(quillContent);
        }

        $.ajax({
            url: $('#update_form').attr('action'),
            type: 'POST',
            data: $('#update_form').serialize(),
            success: function(data) {
                if (data.status) {
                    makeAlert('success', data.message, "{{ __('locale.Success') }}");
                    $('.dtr-bs-modal').modal('hide');
                    redrawDatatable();
                    $('#update_form').closest('.modal').modal(
                        'hide'); // Assuming your form is within a modal

                } else {
                    showError(data['errors']);
                }
            },
            error: function(response, data) {
                responseData = response.responseJSON;
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
                showError(responseData.errors);
            }
        });

    });


    function showError(data) {
        $('.error').empty();
        $.each(data, function(key, value) {
            $('.error-' + key).empty();
            $('.error-' + key).append(value);
        });
    }

    // status [warning, success, error]
    function makeAlert($status, message, title) {
        // On load Toast
        if (title == 'Success')
            title = '👋' + title;
        toastr[$status](message, title, {
            closeButton: true,
            tapToDismiss: false,
        });
    }

    $('.multiple-select2').select2();

    function showModalInitiateAuditsForFrameworkControls() {
        $('#initiateAuditsForFrameworkControlsModal').modal('show');
    }

    function CreateAuditSellectAll() {
        var groupTestIds = $('input[name="audits[]"]:checked');
        if (groupTestIds.length <= 0) {
            makeAlert('error', "{{ __('governance.PleaseSelectOneTestAtLeast') }}", ' Error!');
        } else {
            var groupTestIdsString = '';
            groupTestIds.each(function() {
                if ($(this).is(':checked')) {
                    groupTestIdsString = $(this).val() + ',' + groupTestIdsString;
                }
            });
            showModalCreateAudit(groupTestIdsString);
        }
    }
    $('.initiateAuditsForFrameworkControlsForm').submit(function() {
        let url = "{{ route('admin.governance.audit.getFrameworkTests') }}";
        frameworkId = $('[name=audits_framework_id]').val()
        $.ajax({
            url: url,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                audits_framework_id: frameworkId
            },
            success: function(data) {
                if (data.status) {
                    var groupTestIdsString = data.data
                    $('#initiateAuditsForFrameworkControlsModal').modal('hide');
                    showModalCreateAudit(groupTestIdsString);
                } else {
                    showError(data['errors']);
                }
            },
            error: function(response, data) {
                // Display error alert if deletion fails
                responseData = response.responseJSON;
                showError(responseData['errors']);
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
            }
        });

    })

    function showModalCreateAudit(id) {
        let idAsString = (typeof id === 'string') ? id : id.toString();
        let count = idAsString.split(",").length;
        $('.dtr-bs-modal').modal('hide');

        Swal.fire({
            title: "{{ __('governance.InitiateAudit') }}",
            text: "{{ __('governance.YouWillConfrimInitiateAudit') }}" + " " + count + " " +
                "{{ __('locale.Controls') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ __('locale.Confrim') }}",
            customClass: {
                confirmButton: 'btn btn-relief-success ms-1',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                // Show loading overlay
                $.blockUI({
                    message: '<div class="d-flex justify-content-center align-items-center"><p class="me-50 mb-0">{{ __('locale.PleaseWaitAction', ['action' => __('Initiate Audit')]) }}</p> <div class="spinner-grow spinner-grow-sm text-white" role="status"></div> </div>',
                    css: {
                        backgroundColor: 'transparent',
                        color: '#fff',
                        border: '0'
                    },
                    overlayCSS: {
                        opacity: 0.5
                    }
                });

                // Call CreateAuditTest
                CreateAuditTest(id).then(() => {
                    // Function completed successfully
                    Swal.fire({
                        icon: "success",
                        title: "{{ __('governance.InitiateAudit') }} ",
                        text: "{{ __('governance.InitiateAuditSuccessfully') }}",
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }).catch((error) => {
                    // Handle any errors from CreateAuditTest function
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an error initiating the audit'
                    });
                }).finally(() => {
                    // Hide loading overlay after function completes (whether success or failure)
                    $.unblockUI();
                });
            }
        });
    }

    // create  Audit for list of tests
    // Create Audit for list of tests
    function CreateAuditTest(id) {
        return new Promise((resolve, reject) => {
            let url = "{{ route('admin.governance.audit.store') }}";

            $.ajax({
                url: url,
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                },
                success: function(data) {
                    resolve(data); // Resolve the Promise with the data received
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    reject(errorThrown); // Reject the Promise with the error message
                }
            });
        });
    }




    // $(document).ready(function() {


    //     'use strict'

    //     // Fetch all the forms we want to apply custom Bootstrap validation styles to
    //     var forms = document.querySelectorAll('form')
    //     // Loop over them and prevent submission
    //     Array.prototype.slice.call(forms)
    //         .forEach(function(form) {
    //             form.addEventListener('submit', function(event) {
    //                 if (!form.checkValidity()) {
    //                     event.preventDefault()
    //                     event.stopPropagation()
    //                 } else if (form.checkValidity() == true) {
    //                     // makeAlert('success', "created successfuly", "{{ __('locale.Success') }}");
    //                     // location.reload();

    //                     // stop form submit only for demo
    //                     // event.preventDefault();
    //                 }

    //                 form.classList.add('was-validated')


    //             }, false)
    //         })
    // });

    // Load subdomains of domain
    $(document).on('change', '.domain_select', function() {
        const subDomains = $(this).find('option:selected').data('families');
        const subDomainSelect = $(this).parents('.family-container').next().find('select');
        subDomainSelect.find('option:not(:first)').remove();
        if (subDomains)
            subDomains.forEach(subDomains => {
                subDomainSelect.append(
                    `<option value="${subDomains.id}">${subDomains.name}</option>`
                );
            });
        subDomainSelect.find('option').attr('selected', false);
        subDomainSelect.find('option:first').attr('selected', true);
    });

    $(document).on('change', '.add-control-framework-select', function() {
        const domains = $(this).find('option:selected').data('domains');
        const controls = $(this).find('option:selected').data('controls');
        const domainSelect = $(this).parents('.framework-container').next().find('select');
        const subDomainSelect = $(this).parents('.framework-container').next().next().find('select');
        const parentControlsSelect = $(this).parents('.framework-container').next().next().next().find(
            'select');

        // Add domains
        domainSelect.find('option:not(:first)').remove();
        if (domains)
            domains.forEach(domain => {
                domainSelect.append(
                    `<option data-families='${JSON.stringify(domain.sub_domains)}' value="${domain.id}">${domain.name}</option>`
                );
            });
        domainSelect.find('option').attr('selected', false);
        domainSelect.find('option:first').attr('selected', true);
        subDomainSelect.find('option:not(:first)').remove();
        subDomainSelect.find('option:first').attr('selected', true);

        // Add parent controls
        parentControlsSelect.find('option:not(:first)').remove();
        parentControlsSelect.find('option:first').attr('selected', true);
        if (controls)
            controls.forEach(control => {
                parentControlsSelect.append(
                    `<option value="${control.id}">${control.name}</option>`
                );
            });

        // Enable domain and sub-domain selects
        $('[name="family"]').prop('disabled', false);
        $('[name="sub_family"]').prop('disabled', false);
    })

$(document).on('change', '#framework', function () {
    // Get the selected option
    let selectedOption = $(this).find('option:selected');
    
    // Get the framework ID from the data-id attribute
    let frameworkId = selectedOption.data('id');

    let url = "{{ route('admin.governance.framework.domain') }}"; // Ensure route is correct

    $.ajax({
        url: url,
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            frameworkId: frameworkId // Send the framework ID
        },
        success: function (response) {
            let familySelect = $(".domain_select_filter");
            familySelect.empty(); // Clear previous options
            familySelect.append('<option value="">{{ __('locale.select-option') }}</option>'); // Add default option

            // Loop through each family (domain)
            $.each(response, function (index, family) {
                // Append the parent domain
                familySelect.append(`
                    <option value="${family.name}" data-families='${JSON.stringify(family.families)}'>
                        ${family.name}
                    </option>
                `);
            });

            familySelect.trigger("change"); // Trigger change event if needed
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error fetching families:", errorThrown);
        }
    });
});
    // Load subdomains of domain
    $(document).on('change', '.domain_select_filter', function() {
        const subDomains = $(this).find('option:selected').data('families');
        const subDomainSelect = $(this).parents('.family-container').next().find('select');
        subDomainSelect.find('option:not(:first)').remove();
        subDomainSelect.val('');
        subDomainSelect.trigger('change');
        subDomainSelect.find('option:first').attr('selected', true)
        if (subDomains)
            subDomains.forEach(subDomains => {
                subDomainSelect.append(
                    `<option value="${subDomains.name}">${subDomains.name}</option>`
                );
            });
    });

    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
        //datepicker start

        var $input = $('.js-datepicker').pickadate({
            format: 'yyyy-mm-dd',
            firstDay: 1,
            formatSubmit: 'yyyy-mm-dd',
            hiddenName: true,
            editable: true
        });

        var picker = {};


        // $('button').on('click', function(e) {
        //     e.stopPropagation();
        //     picker[$(e.target).data('i')].open();
        // });

        //datepicker end
    });

    $(document).on('change', '[name="parent_id"]', function() {
        if ($(this).val()) {
            $('[name="family"]').val('').trigger('change').prop('disabled', true);
            $('[name="sub_family"]').val('').trigger('change').prop('disabled', true);
        } else {
            $('[name="family"]').prop('disabled', false);
            $('[name="sub_family"]').prop('disabled', false);
        }
    });
</script>

<script>
    // Function to delete an objective via AJAX
    function DeleteObjective(id) {
        // Construct the URL for deleting the objective
        let url = "{{ route('admin.governance.control.ajax.objective.deleteObjective', ':id') }}";
        url = url.replace(':id', id);

        // AJAX request to delete the objective
        $.ajax({
            url: url,
            type: "DELETE",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.status) {
                    // Display success alert and update objectives list
                    makeAlert('success', data.message, "{{ __('locale.Success') }}");
                    objectives = data.objectives;
                    $('#objectivesList').empty();
                    if (objectives.length) {
                        publishTableWithObjectives(objectives);
                    } else {
                        html = '<h4 style="text-align:center; color:red">No Objectives Yet<h4>';
                        $('#objectivesList').html(html);
                    }
                    $('.dtr-bs-modal').modal('hide');
                }
            },
            error: function(response, data) {
                // Display error alert if deletion fails
                responseData = response.responseJSON;
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
            }
        });
    }

    // Function to show delete confirmation modal for an objective
    function ShowModalDeleteObjective(id) {
        // Display confirmation modal using SweetAlert
        $('.dtr-bs-modal').modal('hide');
        Swal.fire({
            title: "{{ __('locale.AreYouSureToDeleteThisRecord') }}",
            text: '@lang('locale.YouWontBeAbleToRevertThis')',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "{{ __('locale.ConfirmDelete') }}",
            cancelButtonText: "{{ __('locale.Cancel') }}",
            customClass: {
                confirmButton: 'btn btn-relief-success ms-1',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                // If confirmed, call the DeleteObjective function
                DeleteObjective(id);
            }
        });
    }

    // Function to delete an evidence via AJAX
    function DeleteEvidence(id) {
        // Construct the URL for deleting the evidence
        let url = "{{ route('admin.governance.control.ajax.objective.deleteEvidence', ':id') }}";
        url = url.replace(':id', id);

        // AJAX request to delete the evidence
        $.ajax({
            url: url,
            type: "DELETE",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.status) {
                    // Display success alert and update evidences list
                    makeAlert('success', data.message, "{{ __('locale.Success') }}");
                    evidences = data.evidences;
                    canEditEvidences = data.can_edit_evidences;
                    $('#evidencesList').empty();
                    if (evidences.length) {
                        publishTableWithEvidences(evidences, canEditEvidences);
                    } else {
                        html = '<h4 style="text-align:center; color:red">No Evidences Yet<h4>';
                        $('#evidencesList').html(html);
                    }
                    $('.dtr-bs-modal').modal('hide');
                }
            },
            error: function(response, data) {
                // Display error alert if deletion fails
                responseData = response.responseJSON;
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
            }
        });
    }

    // Function to show delete confirmation modal for an evidence
    function ShowModalDeleteEvidence(id) {
        // Display confirmation modal using SweetAlert
        $('.dtr-bs-modal').modal('hide');
        Swal.fire({
            title: "{{ __('locale.AreYouSureToDeleteThisRecord') }}",
            text: '@lang('locale.YouWontBeAbleToRevertThis')',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "{{ __('locale.ConfirmDelete') }}",
            cancelButtonText: "{{ __('locale.Cancel') }}",
            customClass: {
                confirmButton: 'btn btn-relief-success ms-1',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                // If confirmed, call the DeleteEvidence function
                DeleteEvidence(id);
            }
        });
    }
</script>

<script src="{{ asset('ajax-files/governance/controls/index.js') }}"></script>
<script src="{{ asset('ajax-files/governance/controls/app-chat.js') }}"></script>

<script>
    function showModalObjectiveComments(controlControlObjectiveId) {
        var url = "{{ route('admin.governance.control.ajax.objective.showComments', '') }}" + "/" +
            controlControlObjectiveId;
        $('[name="control_control_objective_id"]').val(controlControlObjectiveId);
        // AJAX request
        $.ajax({
            url: url,
            type: "GET",
            data: {},
            success: function(response) {
                comments = response.data;
                addMessageToChat(comments);
                $('.clearCommentsBtn').attr('onclick', 'showModalClearComments(' +
                    controlControlObjectiveId + ')')
                $('#objectiveCommentsModal').modal('show');
            },
            error: function(response, data) {
                responseData = response.responseJSON;
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
                showError(responseData.errors);
            }
        });

    }

    // Function to show delete confirmation modal for an comments
    function showModalClearComments(id) {
        // Display confirmation modal using SweetAlert
        $('.dtr-bs-modal').modal('hide');
        Swal.fire({
            title: "{{ __('locale.AreYouSureToClearComments') }}",
            text: '@lang('locale.YouWontBeAbleToRevertThis')',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "{{ __('locale.ConfirmDelete') }}",
            cancelButtonText: "{{ __('locale.Cancel') }}",
            customClass: {
                confirmButton: 'btn btn-relief-success ms-1',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                // If confirmed, call the Delete Comments function
                clearComments(id);
            }
        });
    }

    function clearComments(id) {
        // Construct the URL for deleting the comments
        let url = "{{ route('admin.governance.control.ajax.objective.clearComments', ':id') }}";
        url = url.replace(':id', id);

        // AJAX request to delete the objective
        $.ajax({
            url: url,
            type: "DELETE",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.status) {
                    // Display success alert and update comments list
                    makeAlert('success', data.message, "{{ __('locale.Success') }}");
                    $('.chats').empty();
                }
            },
            error: function(response, data) {
                // Display error alert if deletion fails
                responseData = response.responseJSON;
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
            }
        });
    }

    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2();

        // Handle button click to open the second modal
        $('#openSecondModal').click(function() {
            $('#secondModal').modal('show');
        });

        // Handle framework filter change
        $('#frameworkControlsFilter').change(function() {
            var frameworkId = $(this).val();
            var controlId = $('#secondModal input[name="control_id"]').val();

            if (frameworkId) {
                $.ajax({
                    url: "{{ route('admin.governance.control.ajax.objective.get-controls-by-framework') }}",
                    type: "GET",
                    data: {
                        framework_id: frameworkId,
                        control_id: controlId
                    },
                    success: function(response) {
                        var controls = $('#controlsMapping');
                        controls.empty();
                        controls.append(
                            '<option disabled value="">{{ __('select') }}</option>');

                        $.each(response.controls, function(index, control) {
                            controls.append('<option value="' + control.id + '">' +
                                control.short_name + '</option>');
                        });

                        controls.select2({
                            placeholder: "{{ __('select') }}",
                            allowClear: true
                        });
                    }
                });
            } else {
                $('#controlsMapping').empty();
                $('#controlsMapping').append(
                    '<option disabled value="">{{ __('select') }}</option>');
                $('#controlsMapping').select2();
            }
        });

        // Handle form submission
        $('#secondModalForm').submit(function(e) {
            e.preventDefault();
            var controlId = $('#secondModal input[name="control_id"]').val();
            var extendControlId = $('#controlsMapping').val();

            $.ajax({
                url: "{{ route('admin.governance.control.ajax.objective.saveMappingControls') }}",
                type: "POST",
                data: {
                    control_id: controlId,
                    extend_control_id: extendControlId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.status) {
                        makeAlert('success', data.message, "{{ __('locale.Success') }}");
                        $('#secondModal').modal('hide');
                        mapControl(controlId);
                    } else {
                        makeAlert('error', data.message, "{{ __('locale.Error') }}");
                        showError(data.errors);
                    }
                },
                error: function(response) {
                    var responseData = response.responseJSON;
                    var errorMessage = responseData.message;
                    if (responseData.errors) {
                        errorMessage += '<ul>';
                        $.each(responseData.errors, function(key, messages) {
                            $.each(messages, function(index, message) {
                                errorMessage += '<li>' + message + '</li>';
                            });
                        });
                        errorMessage += '</ul>';
                    }
                    makeAlert('error', errorMessage, "{{ __('locale.Error') }}");
                    showError(responseData.errors);
                }
            });
        });


    });

    $(document).on('click', '.description-preview', function() {
        let description = $(this).data('description');
        $('#modalDescriptionContent').text(description);
        $('#descriptionModal').modal('show');
    });
</script>
<script>
    var quill = new Quill('#control_supplemental_guidance', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{
                    'header': [1, 2, 3, 4, 5, 6, false]
                }],
                ['bold', 'italic', 'underline', 'strike'],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                [{
                    'indent': '-1'
                }, {
                    'indent': '+1'
                }],
                [{
                    'direction': 'rtl'
                }], // Right-to-left direction
                ['clean'],
            ],
        },
    });

    // You can further customize the toolbar based on your specific needs
</script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.16.0/d3.min.js"></script> --}}
{{-- <script src="{{ asset('cdn/jquery-3.6.0.min.js') }}"></script> --}}
<script src="{{ asset('cdn/d3.v6.min.js') }}"></script>

{{-- <script>
    $(document).ready(function() {
        // Initially set the height of chartContainer to 50px and hide it
        $('#chartContainer').css('height', '50px').hide();

        $('#frameworkSelect').change(function() {
            var frameworkId = $(this).val();

            if (frameworkId) {
                // Set height to 500px, slide down and fetch data if a value is selected
                $('#chartContainer').css('height', '351px').slideDown('fast');
                fetchGraphData(frameworkId);
            } else {
                // Set height to 50px, slide up if no value is selected
                $('#chartContainer').slideUp('fast', function() {
                    $('#chartContainer').css('height', '50px');
                });
            }
        });

        function fetchGraphData(frameworkId) {
            $.ajax({
                url: '{{ route('admin.governance.fetch.graphClosedControls', '') }}/' + frameworkId,
                method: 'GET',
                success: function(response) {
                    if (response.controlsIntiate && response.controlsIntiate.length === 0) {
                        // If `controlsIntiate` is empty, display "No audit yet" message
                        displayNoDataMessage();
                    } else if (response.data && response.data.labels.length > 0) {
                        // Data is available for the chart
                        drawChart(response.data);
                    } else {
                        // No relevant data, but `controlsIntiate` is not empty
                        displayNoDataMessage();
                    }
                },
                error: function() {
                    alert('An error occurred while fetching the data.');
                }
            });
        }

        function drawChart(data) {
            $('#chartContainer').empty(); // Clear existing chart

            const pieData = data.labels.map((label, index) => ({
                name: label,
                value: data.values[index],
                color: getColorByIndex(index)
            }));

            bakeDonut(pieData);
        }

        function displayNoDataMessage() {
            $('#chartContainer').empty().append('<p>No audit yet</p>');
        }

        function getColorByIndex(index) {
            const colors = ['#18FFFF', '#0288D1', '#BF360C', '#F4511E', '#F9A825'];
            return colors[index % colors.length];
        }

        function bakeDonut(d) {
            // Your existing D3.js code for drawing the donut chart
            let activeSegment;
            const data = d.sort((a, b) => b['value'] - a['value']),
                viewWidth = 500,
                viewHeight = 300,
                svgWidth = viewHeight,
                svgHeight = viewHeight,
                thickness = 40,
                colorArray = data.map(k => k.color),
                el = d3.select('#chartContainer'),
                radius = Math.min(svgWidth, svgHeight) / 2,
                color = d3.scaleOrdinal()
                .range(colorArray);

            const svg = el.append('svg')
                .attr('viewBox', `0 0 ${viewWidth + thickness} ${viewHeight + thickness}`)
                .attr('class', 'pie')
                .attr('width', viewWidth)
                .attr('height', svgHeight);

            const g = svg.append('g')
                .attr('transform',
                    `translate(${(svgWidth / 2) + (thickness / 2)}, ${(svgHeight / 2) + (thickness / 2)})`);

            const arc = d3.arc()
                .innerRadius(radius - thickness)
                .outerRadius(radius);

            const arcHover = d3.arc()
                .innerRadius(radius - (thickness + 5))
                .outerRadius(radius + 8);

            const pie = d3.pie()
                .value(function(pieData) {
                    return pieData.value;
                })
                .sort(null);

            const path = g.selectAll('path')
                .attr('class', 'data-path')
                .data(pie(data))
                .enter()
                .append('g')
                .attr('class', 'data-group')
                .each(function(pathData, i) {
                    const group = d3.select(this);

                    // Default text for center
                    if (i === 0) {
                        group.append('text')
                            .text(data[0].value)
                            .attr('class', 'data-text data-text__value')
                            .attr('text-anchor', 'middle')
                            .attr('dy', '1rem');

                        group.append('text')
                            .text(data[0].name)
                            .attr('class', 'data-text data-text__name')
                            .attr('text-anchor', 'middle')
                            .attr('dy', '3.5rem');
                    }
                })
                .append('path')
                .attr('d', arc)
                .attr('fill', (fillData, i) => color(fillData.data.name))
                .attr('class', 'data-path')
                .on('mouseover', function(event, d) {
                    const parentNode = this.parentNode;

                    // Clear existing center text
                    d3.selectAll('.data-text').remove();

                    // Update center text with the hovered segment data
                    g.append('text')
                        .text(d.data.value)
                        .attr('class', 'data-text data-text__value')
                        .attr('text-anchor', 'middle')
                        .attr('dy', '1rem');

                    g.append('text')
                        .text(d.data.name)
                        .attr('class', 'data-text data-text__name')
                        .attr('text-anchor', 'middle')
                        .attr('dy', '3.5rem');

                    // Highlight hovered segment
                    d3.selectAll('.data-path')
                        .transition()
                        .duration(250)
                        .attr('d', arc);

                    d3.select(this)
                        .transition()
                        .duration(250)
                        .attr('d', arcHover);
                })
                .on('mouseout', function() {
                    // Clear center text when mouse leaves the segment
                    d3.selectAll('.data-text').remove();

                    // Reset segment highlight
                    d3.selectAll('.data-path')
                        .transition()
                        .duration(250)
                        .attr('d', arc);
                })
                .each(function(v, i) {
                    if (i === 0) {
                        activeSegment = this;
                    }
                    this._current = i;
                });

            const legendRectSize = 15;
            const legendSpacing = 10;

            const legend = svg.selectAll('.legend')
                .data(color.domain())
                .enter()
                .append('g')
                .attr('class', 'legend')
                .attr('transform', function(legendData, i) {
                    const itemHeight = legendRectSize + legendSpacing;
                    const offset = legendRectSize * color.domain().length;
                    const horz = svgWidth + 80;
                    const vert = (i * itemHeight) + legendRectSize + (svgHeight - offset) / 2;
                    return `translate(${horz}, ${vert})`;
                });

            legend.append('circle')
                .attr('r', legendRectSize / 2)
                .style('fill', color);

            legend.append('text')
                .attr('x', legendRectSize + legendSpacing)
                .attr('y', legendRectSize - legendSpacing)
                .attr('class', 'legend-text')
                .text((legendData) => legendData);
        }
    });
</script> --}}
@endsection
