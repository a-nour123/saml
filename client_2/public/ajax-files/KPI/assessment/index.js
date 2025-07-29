//select2 class
$(document).ready(function () {
  $('.multiple-select2').select2();
});
//filter Column 
function filterColumn(i, val) {

  $('.dt-advanced-search').DataTable().column(i).search(val, false, true).draw();

}
//function to create database
function createDatatable(JsonList) {

  var isRtl = $('html').attr('data-textdirection') === 'rtl';

  var dt_ajax_table = $('.datatables-ajax'),
    dt_filter_table = $('.dt-column-search'),
    dt_adv_filter_table = $('.dt-advanced-search'),
    dt_responsive_table = $('.dt-responsive'),
    assetPath = '../../../app-assets/';

  if ($('body').attr('data-framework') === 'laravel') {
    assetPath = $('body').attr('data-asset-path');
  }
  if (dt_adv_filter_table.length) {


    var dt_adv_filter = dt_adv_filter_table.DataTable({
      data: JsonList,
      responsive: true,
      autoWidth: true,
      searching: true,
      columns: [
        { data: 'responsive_id', orderable: false },
        { data: 'value', orderable: false },
        { data: 'kpi', orderable: false },
        { data: 'type', orderable: false },
        { data: 'description', orderable: false },
        { data: 'department', orderable: false },
        // { data: 'department_manager', orderable: false },
        { data: 'assigned_to', orderable: false },
        { data: 'createdBy', orderable: false },
        { data: 'created_at', orderable: false },
        { data: 'actionBy', orderable: false },
        { data: 'assessment_date', orderable: false },
        { data: 'Actions', orderable: false }
      ],


      columnDefs: [
        {
          className: 'index',
          orderable: false,
          targets: 0
        }

        , {
          // Actions
          targets: -1,
          orderable: false,
          render: function (data, type, full, meta) {

            let returnedString = '';
            if (full.enabled) {
              // Add the Set KPI Assessment link
              returnedString = '<a href="javascript:;" onclick="showModalToSetKPIAssessment(' + data + ')" class="item-edit dropdown-item">' +
                feather.icons['mouse-pointer'].toSvg({ class: 'me-50 font-small-4' }) + lang['SetKPIAssessment'] +
                '</a>';
              if (permission["ReassignAssessment"]) {
                // Append the Set Assigned User link
                returnedString += '<a href="javascript:;" onclick="showModalToSetAssignedUser(' + data + ')" class="item-edit dropdown-item">' +
                feather.icons['user-plus'].toSvg({ class: 'me-50 font-small-4' }) + lang['ReAssignKpiAssessment'] +
                '</a>';
            
              }
            }
            if (returnedString == '')
              return '------';

            return ('<div class="d-inline-flex">' +
              '<a class="pe-1 dropdown-toggle hide-arrow text-primary" data-bs-toggle="dropdown">' +
              feather.icons['more-vertical'].toSvg({
                class: 'font-small-4'
              }) +
              '</a>' +
              '<div class="dropdown-menu dropdown-menu-end">' +
              returnedString +
              '</div>' +
              '</div>');
          }
        }
        // , {
        //   // Label for department
        //   targets: -6,
        //   render: function (data, type, full, meta) {
        //     return '<span class="badge rounded-pill badge-light-info">' +
        //       data +
        //       '</span>'
        //   }
        // }
        // , {
        //   // Label for KPI
        //   targets: -9,
        //   render: function (data, type, full, meta) {
        //     return '<span class="badge rounded-pill badge-light-success">' +
        //       data +
        //       '</span>'
        //   }
        // }
        // , {
        //   // Label for assessment by
        //   targets: -5,
        //   render: function (data, type, full, meta) {
        //     return '<span class="badge rounded-pill badge-light-primary">' +
        //       data +
        //       '</span>'
        //   }
        // }
        // , {
        //   // Label for created by
        //   targets: -3,
        //   render: function (data, type, full, meta) {
        //     return '<span class="badge rounded-pill badge-light-primary">' +
        //       data +
        //       '</span>'
        //   }
        // }
      ],
      dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      orderCellsTop: true,

      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== ''
                ? '<tr data-dt-row="' +
                col.rowIndex +
                '" data-dt-column="' +
                col.columnIndex +
                '">' +
                '<td>' +
                col.title +
                ':' +
                '</td> ' +
                '<td>' +
                col.data +
                '</td>' +
                '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      },
      language: {
        paginate: {
          previous: '&nbsp;',
          next: '&nbsp;'
        }
      }
    });

    // Start index from 1 not get index from item recorded ID
    dt_adv_filter.on('order.dt search.dt', function () {
      dt_adv_filter.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
    }).draw();
  }

  // call function in input filter
  $('input.dt-input').on('keyup', function () {
    filterColumn($(this).attr('data-column'), $(this).val());
  });
  // call function in select filter
  $('select.dt-select').on('change', function () {
    filterColumn($(this).attr('data-column'), $(this).val());
  });

  $('.dataTables_filter .form-control').removeClass('form-control-sm');
  $('.dataTables_length .form-select').removeClass('form-select-sm').removeClass('form-control-sm');
}

// function to show error validation 
function showError(data) {
  $('.error').empty();
  $.each(data, function (key, value) {
    $('.error-' + key).empty();
    $('.error-' + key).append(value);
  });
}

// status [warning, success, error]
function makeAlert($status, message, title) {
  // On load Toast
  if (title == 'Success')
    title = '👋' + title;
  toastr[$status](message, title,
    {
      closeButton: true,
      tapToDismiss: false,
    }
  );
}

//  ajax to call asset list and call create datatable
function loadDatatable() {
  $.ajax({
    url: URLs['ajax_list']
    , type: "GET"
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , data: {}
    , success: function (data) {
      createDatatable(data);
    }
    , error: function () {
      //
    }
  });
}

loadDatatable();

// Reset form
function resetFormData(form) {
  $('.error').empty();
  form.trigger("reset")
  form.find('input:not([name="_token"])').val('');
  form.find('select.multiple-select2 option[selected]').attr('selected', false);
  form.find('select.select2 option').attr('selected', false);
  form.find("select.select2").each(function (index) {
    $(this).find('option').first().attr('selected', true);
  });
  form.find('select').trigger('change');
}

$('.modal').on('hidden.bs.modal', function () {
  resetFormData($(this).find('form'));
})

// Show set KPI Assigned User
function showModalToSetAssignedUser(id) {
  const editForm = $("#assign-KPI-assessment form");
  editForm.find('input[name="id"]').val(id);
  // Clear and fetch users each time the modal opens to avoid duplicate entries
  $('#assigned_to').empty().append('<option value="">' + lang['select-option'] + '</option>');

  FetchUserAssigned(id);
  $('#assign-KPI-assessment').modal('show');
}

function FetchUserAssigned(id) {
  let url = URLs['getUsersUrl']; // URL to fetch users

  $.ajax({
    url: url,
    type: 'GET',
    data: { id: id },  // Send the id as part of the query parameters
    success: function (data) {
      // First clear any existing options in the select element
      $('#assigned_to').empty().append('<option value="">' + lang['select-option'] + '</option>');

      // Populate the "Assigned To" dropdown with the returned users
      $.each(data.users, function (id, name) {
        // Append each user as an option
        let option = $('<option>', {
          value: id,
          text: name
        });

        // If the user is the one assigned, mark it as selected
        if (id == data.assigned_to) {
          option.prop('selected', true);
        }

        $('#assigned_to').append(option);
      });
    },
    error: function () {
      console.error('Could not fetch users');
    }
  });
}


$('#assign-KPI-assessment form').on('submit', function (e) {
  e.preventDefault();  // Prevent form submission
  let url = URLs['StoreAssignedUser'];  // The URL to send the request to
  $.ajax({
    url: url,
    type: "POST",  // Ensure you use PUT method
    data: $(this).serialize(),  // Serialize the form data
    success: function (data) {
      if (data.status) {
        makeAlert('success', data.message, lang['success']);
        $('#assign-KPI-assessment').modal('hide');
        $("#advanced-search-datatable").load(location.href +
          " #advanced-search-datatable>*", "");
        loadDatatable();
      } else {
        showError(data.errors);  // Make sure you're sending 'errors' in the response if there are errors
      }
    },
    error: function (response) {
      responseData = response.responseJSON;  // Get JSON response from error
      makeAlert('error', responseData.message, lang['error']);
      showError(responseData.errors);  // Show the error details
    }
  });
});



// Show set KPI assessment value
function showModalToSetKPIAssessment(id) {
  const editForm = $("#initiate-KPI-assessment form");

  // Start Assign KPI assessment data to modal
  editForm.find('input[name="id"]').val(id);
  // End Assign KPI assessment data to modal

  $('#initiate-KPI-assessment').modal('show');
}

// Submit form for editing asset
$('#initiate-KPI-assessment form').on('submit', function (e) {
  e.preventDefault();
  let url = URLs['set_assessment'];
  $.ajax({
    url: url
    , type: "put"
    , data: $(this).serialize()
    , success: function (data) {
      if (data.status) {
        makeAlert('success', data.message, lang['success']);
        $('#initiate-KPI-assessment form').trigger("reset");
        $('#initiate-KPI-assessment').modal('hide');
        $("#advanced-search-datatable").load(location.href +
          " #advanced-search-datatable>*", "");
        loadDatatable();
      } else {
        showError(data['errors']);
      }
    }
    , error: function (response, data) {
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
      showError(responseData.errors);
    }
  });
});
document.getElementById('value-input').addEventListener('input', function () {
  if (this.type === 'number' && this.max && parseFloat(this.value) > parseFloat(this.max)) {
    this.value = this.max;
  }
});
