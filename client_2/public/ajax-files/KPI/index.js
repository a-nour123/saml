//select2 class
$(document).ready(function () {
  $('.multiple-select2').select2();
});

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

// Submit form for creating asset
$('#add-new-KPI form').on('submit', function (e) {
  e.preventDefault();
  $.ajax({
    url: $(this).attr('action')
    , type: "POST"
    , data: $(this).serialize()
    , success: function (data) {
      if (data.status) {
        makeAlert('success', data.message, lang['success']);
        $('#add-new-KPI').modal('hide');
        redrawDatatable();
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

// Submit form for editing asset
$('#edit-KPI form').on('submit', function (e) {
  e.preventDefault();
  const id = $(this).find('input[name="id"]').val();
  let url = URLs['update'];
  url = url.replace(':id', id);
  $.ajax({
    url: url
    , type: "PUT"
    , data: $(this).serialize()
    , success: function (data) {
      if (data.status) {
        makeAlert('success', data.message, lang['success']);
        $('#edit-KPI form').trigger("reset");
        $('#edit-KPI').modal('hide');
        redrawDatatable();
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

function DeleteKPI(id) {
  let url = URLs['delete'];
  url = url.replace(':id', id);
  $.ajax({
    url: url
    , type: "DELETE"
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (data) {
      if (data.status) {
        makeAlert('success', data.message, lang['success']);
        $('.dtr-bs-modal').modal('hide');
        redrawDatatable();
      }
    }
    , error: function (response, data) {
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
    }
  });
}
 
// Show modal for editing
function ShowModalEditKPI(id) {
  let url = URLs['edit'];
  url = url.replace(':id', id);
  $.ajax({
    url: url
    , type: "GET"
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (response) {
      if (response.status) {
        const editForm = $("#edit-KPI form");

        // Start Assign KPI data to modal
        editForm.find('input[name="id"]').val(id);
        editForm.find("input[name='title']").val(response.data.title);
        editForm.find("textarea[name='description']").val(response.data.description);
        editForm.find(`select[name='department'] option[value='${response.data.department_id}']`).attr('selected', true).trigger('change');
        editForm.find(`select[name='value_type'] option[value='${response.data.value_type}']`).attr('selected', true).trigger('change');
        editForm.find("input[name='value']").val(response.data.value);
        editForm.find(`select[name='period_of_assessment'] option[value='${response.data.period_of_assessment}']`).attr('selected', true).trigger('change');
        // End Assign KPI data to modal

        $('.dtr-bs-modal').modal('hide');
        $('#edit-KPI').modal('show');
      }
      // alert(1);
    }
    , error: function (response, data) {
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
    }
  });
}








// Show delete alert modal
function ShowModalDeleteKPI(id) {
  $('.dtr-bs-modal').modal('hide');
  Swal.fire({
    title: lang['confirmDeleteRecordMessage']
    , text: lang['revert']
    , icon: 'question'
    , showCancelButton: true
    , confirmButtonText: lang['confirmDelete']
    , cancelButtonText: lang['cancel']
    , customClass: {
      confirmButton: 'btn btn-relief-success ms-1'
      , cancelButton: 'btn btn-outline-danger ms-1'
    }
    , buttonsStyling: false
  }).then(function (result) {
    if (result.value) {
      DeleteKPI(id);
    }
  });
}

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

// Show create KPI assessment alert modal
function showModalCreateKPIAssessment(id) {
  Swal.fire({
    title: lang['confirmInitiateKPIAssessment']
    , text: lang['revert']
    , icon: 'question'
    , showCancelButton: true
    , confirmButtonText: lang['confirmInitiateAssessment']
    , cancelButtonText: lang['cancel']
    , customClass: {
      confirmButton: 'btn btn-relief-success ms-1'
      , cancelButton: 'btn btn-outline-danger ms-1'
    }
    , buttonsStyling: false
  }).then(function (result) {
    if (result.value) {
      createKPIAssessment(id);
    }
  });
}

// Create KPI assessment
function createKPIAssessment(id) {
  let url = URLs['initiate_assessment'];
  url = url.replace(':id', id);
  $.ajax({
    url: url
    , type: "POST"
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (data) {
      if (data.status) {
        makeAlert('success', data.message, lang['success']);
        window.location.reload(true);

      }
    }
    , error: function (response, data) {
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
    }
  });
}

// Show modal for list KPI assessment
function showModalListKPIAssessment(id) {
  let url = URLs['list_assessment'];
  url = url.replace(':id', id);
  $.ajax({
    url: url
    , type: "GET"
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (response) {
      if (response.status) {
        $('.dtr-bs-modal').modal('hide');
        $('#list-KPI-assessment tbody').html('');
        response.data.forEach(assessment => {
          $('#list-KPI-assessment tbody').append(
            `
            <tr>
              <td>${assessment.kpi_value}</td>
              <td>${assessment.value}</td>
              <td><span class="badge rounded-pill badge-light-primary">${assessment.createdBy}</span></td>
              <td>${assessment.created_at}</td>
              <td><span class="badge rounded-pill badge-light-primary">${assessment.actionBy}</span></td>
              <td>${assessment.assessment_date}</td>
            </tr>
            `
          );
        });
        $('#list-KPI-assessment').modal('show');
      }
      // alert(1);
    }
    , error: function (response, data) {
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
    }
  });
}

drawDatatable(
  // columnsData
  [
    { data: 'id', orderable: false },
    { data: 'title', orderable: false },
    { data: 'description', orderable: false },
    { data: 'value_type', orderable: false },
    { data: 'value', orderable: false },
    { data: 'period_of_assessment', orderable: false },
    { data: 'department', orderable: false },
    { data: 'department_manager', orderable: false },
     { data: 'created_by_user', orderable: false },
    { data: 'created_at', orderable: false },
    { data: 'Actions', orderable: false }
  ],
  // columnDefinitions
  [
    {
      // Actions
      targets: -1,
      orderable: false,
      render: function (data, type, full, meta) {

        let returnedString = '';
        let auditCreateString = '';
        let editString = '';
        let deleteString = '';

        if (permission['InitiateAssessment'])
          auditCreateString = '<a  href="javascript:;" onclick="showModalCreateKPIAssessment(' + data +
            ')" class="item-edit dropdown-item ">' +
            feather.icons['mouse-pointer'].toSvg({
              class: 'me-50 font-small-4'
            }) + lang['InitiateKPIAssessment'] +
            '</a>';

        if (permission['delete']) {
          deleteString = '<a  href="javascript:;" onclick="ShowModalDeleteKPI(' + data + ')" class="item-delete dropdown-item">' +
            feather.icons['trash-2'].toSvg({ class: 'me-50 font-small-4' }) + lang['Delete']
          '</a>';
        }

        if (permission['edit']) {
          editString = '<a  href="javascript:;" onclick="ShowModalEditKPI(' + data + ')" class="item-edit dropdown-item">' +
            feather.icons['edit'].toSvg({ class: 'me-50 font-small-4' }) + lang['Edit']
          '</a>';
        }

        return ('<div class="d-inline-flex">' +
          '<a class="pe-1 dropdown-toggle hide-arrow text-primary" data-bs-toggle="dropdown">' +
          feather.icons['more-vertical'].toSvg({
            class: 'font-small-4'
          }) +
          '</a>' +
          '<div class="dropdown-menu dropdown-menu-end">' +

          '<a  href="javascript:;" onclick="showModalListKPIAssessment(' + data +
          ')" class="item-edit dropdown-item ">' +
          feather.icons['list'].toSvg({
            class: 'me-50 font-small-4'
          }) + lang['ListKPIAssessments'] +
          '</a>' +

          auditCreateString +
          editString +
          deleteString +
          '</div>' +
          '</div>');
      }
    }
    // , {
    //   // Label for creator
    //   targets: -3,
    //   orderable: false,
    //   render: function (data, type, full, meta) {
    //     return '<span class="badge rounded-pill badge-light-primary">' +
    //       data +
    //       '</span>'
    //   }
    // }
    // , {
    //   // Label for department
    //   targets: -4,
    //   orderable: false,
    //   render: function (data, type, full, meta) {
    //     return '<span class="badge rounded-pill badge-light-primary">' +
    //       data +
    //       '</span>'
    //   }
    // }
  ],
  // detailsOfItem
  lang['DetailsOfItem'],
  // detailsOfItemKey
  'title'
);
