@extends('admin/layouts/contentLayoutMaster')

@section('title', __('locale.Users'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('fonts/fontawesome-6.2.1/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">

    {{-- <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}"> --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.bubble.css')) }}">
@endsection

@section('page-style')
    {{-- <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}"> --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/selectize.bootstrap4.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">

    <style>
        ul,
        li {
            list-style-type: none;
        }

        label {
            font-size: 15px;

        }

        li input {
            cursor: pointer;
        }
/* ===== Tree Container ===== */
.department-tree {
    font-family: 'Segoe UI', sans-serif;
    line-height: 1.5;
    padding-left: 0;
}

/* ===== Tree Items ===== */
.tree-item {
    list-style: none;
    margin: 4px 0;
}

.tree-node {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 3px 10px;
    border-radius: 4px;
    margin-left: calc(var(--depth, 0) * 20px);
    transition: background 0.2s;
}

.tree-node:hover {
    background: #f5f5f5;
}

/* ===== Toggle Arrows ===== */
.tree-toggle {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.tree-arrow {
    transition: transform 0.2s;
    fill: #666;
}

.tree-item.collapsed .tree-arrow {
    transform: rotate(-90deg);
}

.tree-spacer {
    width: 24px; /* Matches arrow button width */
}

/* ===== Checkbox & Label ===== */
.tree-checkbox {
    accent-color: #4a6baf;
    margin-top: -7px;
    width: 16px;
    height: 16px;
}

.tree-label {
    cursor: pointer;
    font-weight: 500;
    color: #333;
    flex-grow: 1;
}

/* ===== Children List ===== */
.tree-children {
    padding-left: 24px;
    overflow: hidden;
    transition: max-height 0.3s ease, opacity 0.2s ease;
}

.tree-item.collapsed .tree-children {
    max-height: 0 !important;
    opacity: 0;
}
.layout-departments
{
    max-height: 60vh;
    overflow: auto;
    border-bottom: 1px solid #DDD;
}
    </style>
@endsection
@section('content')

<div class="content-header row">
    <div class="content-header-left col-12 mb-2">

        <div class="row breadcrumbs-top  widget-grid mt-2">
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



                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<div id="quill-service-content" class="d-none"></div>

</div>


    <div class="card">

        <div class="card-body">
            @if (!isset($ldapMessage))


            <form id="department-ajax-form" action="{{ route('admin.configure.user.import.ldap.store') }}"
                method="POST">
                <div class="layout-departments">

                    @foreach ($tree as $parent => $children)
                        @include('admin.content.configure.user_management.ldap_tree', [
                            'node' => $parent,
                            'children' => $children,
                            'level' => 0,
                        ])
                    @endforeach
                </div>
                <br>
                <div class="col-md-6">
                    <div class="mb-1">
                        <label class="form-label">{{ __('locale.Roles') }}</label>
                        <select class="select2 form-select role_id" name="role_id" required>
                            <option value="" disabled hidden selected>{{ __('locale.select-option') }}
                            </option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <span class="error error-role_id"></span>
                    </div>
                </div>

                <br>
                 <button type="submit" class="btn btn-primary ">
                    {{ __('locale.Import') }}
                </button>
            </form>
            @else
            <div class="alert alert-danger py-2 px-3" role="alert">
               {{ $ldapMessage }}
              </div>
            @endif

        </div>
    </div>





    <div id="quill-content" class="d-none"></div>

@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    {{--  <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>  --}}
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
@endsection


@section('page-script')
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>

    <script>
        function checkParent(childCheckbox) {
            let parentLi = childCheckbox.closest('li').parentElement.closest('li');
            if (parentLi) {
                let parentCheckbox = parentLi.querySelector('input[type="checkbox"]');
                if (childCheckbox.checked) {
                    parentCheckbox.checked = true;
                    checkParent(parentCheckbox); // Recursively check parents
                }
            }
        }

        function uncheckChildren(parentCheckbox) {
            let childUl = parentCheckbox.closest('li').querySelector('ul');
            if (childUl) {
                let childCheckboxes = childUl.querySelectorAll('input[type="checkbox"]');
                childCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = false;
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.parent-checkbox').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    if (!this.checked) {
                        uncheckChildren(this);
                    } else {
                        checkParent(this);
                    }
                });
            });
        });



    </script>


    <script>
  $(document).ready(function() {
    $('#department-ajax-form').on('submit', function(event) {
        event.preventDefault();

        // Get the CSRF token from the meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Get the selected group and its OU path
        var selectedGroup = $('input[type="radio"].group-radio:checked');

        if (selectedGroup.length === 0) {
            toastr.error('Please select a group to import', 'Error');
            return;
        }

        var groupData = {
            group_name: selectedGroup.val(),
            group_ou_path: selectedGroup.data('ou-path'),
            role_id: $('.role_id').val(),
            _token: csrfToken
        };

        // AJAX request with CSRF token
        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: groupData,
            success: function(response) {
                toastr.success(response.message || 'Users imported successfully!', 'Success');

                // Redirect after successful submission
                setTimeout(function() {
                    window.location.href = response.redirect || window.location.pathname;
                }, 1500);
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON?.message || 'An error occurred while importing users';
                toastr.error(errorMsg, 'Error');
                console.error(xhr);
            }
        });
    });
});
    </script>
    <script>
        var permission = [],
            lang = [],
            URLs = [];
        permission['show'] = {{ auth()->user()->hasPermission('department.list') ? 1 : 0 }};
        permission['edit'] = {{ auth()->user()->hasPermission('department.update') ? 1 : 0 }};
        permission['delete'] = {{ auth()->user()->hasPermission('department.delete') ? 1 : 0 }};

        lang['DetailsOfItem'] = "{{ __('locale.DetailsOfItem', ['item' => __('locale.department')]) }}";
        URLs['ajax_list'] = "{{ route('admin.hierarchy.department.ajax.index') }}";
    </script>
    <script src="{{ asset('ajax-files/hierarchy/department/index.js') }}"></script>
    <script src="{{ asset('js/scripts/selectize.min.js') }}"></script>
    {{-- <script src="{{ asset(mix('js/scripts/components/components-modals.js')) }}"></script> --}}
    <script>
        $('.modal').on('hidden.bs.modal', function() {
            resetFormData($(this).find('form'));
        })
    </script>

    <script src="{{ asset('js/scripts/forms/department-quill-editor.js') }}"></script>

@endsection
