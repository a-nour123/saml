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
$('#add-new-change-request form').on('submit', function (e) {

  var formData = new FormData(document.querySelector('#add-new-change-request form'));

  e.preventDefault();
  $.ajax({
    url: $(this).attr('action')
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false
    , success: function (data) {
      if (data.status) {
        makeAlert('success', data.message, lang['success']);
        $('#add-new-change-request').modal('hide');
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
$('#edit-change-request form').on('submit', function (e) {
  e.preventDefault();

  const id = $(this).find('input[name="id"]').val();
  let url = URLs['update'];
  url = url.replace(':id', id);
  var formData = new FormData(document.querySelector('#edit-change-request form'));

  formData.append('_method', 'put');

  $.ajax({
    url: url
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false, success: function (data) {
      if (data.status) {
        makeAlert('success', data.message, lang['success']);
        $('#edit-change-request form').trigger("reset");
        $('#edit-change-request').modal('hide');
        $('.dtr-bs-modal').modal('hide');
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

function DeleteChangeRequest(id) {
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
function ShowModalEditChangeRequest(id) {
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
        const editForm = $("#edit-change-request form");

        // Start Assign ChangeRequest data to modal
        editForm.find('input[name="id"]').val(id);
        editForm.find("input[name='title']").val(response.data.title);
        editForm.find("textarea[name='description']").val(response.data.description);
        // End Assign ChangeRequest data to modal

        $('.dtr-bs-modal').modal('hide');
        $('#edit-change-request').modal('show');
      }
      // alert(1);
    }
    , error: function (response, data) {
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
    }
  });
}

// Show modal for makeDecision
function ShowModalMakeDecisionChangeRequest(id) {
  let url = URLs['edit'];
  url = url.replace(':id', id);
  $('.dtr-bs-modal').modal('hide');
  $('#change-request-decision').modal('show');
  const editForm = $("#change-request-decision form");
  // Start Assign ChangeRequest data to modal
  editForm.find('input[name="id"]').val(id);
  // End Assign ChangeRequest data to modal
}

// Show delete alert modal
function ShowModalDeleteChangeRequest(id) {
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
      DeleteChangeRequest(id);
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
  $('#reason-container').addClass('d-none');
  resetFormData($(this).find('form'));
})

// Downloadnote file start
$(document).on('click', '.download-file', function () {
  const form = $('#download-file-form');
  form.find('[name="id"').val($(this).data('id'));

  form.trigger('submit');
});
// Downloadnote file End

// Status change event
$('[name="decision"]').on('change', function () {
  if ($(this).val() == 'Rejected') {
    $('#reason-container').removeClass('d-none');
  }
  else {
    $('#reason-container').addClass('d-none');
  }
});

// Submit form for creating asset
$('#change-request-decision form').on('submit', function (e) {

  var formData = new FormData(document.querySelector('#change-request-decision form'));

  e.preventDefault();
  $.ajax({
    url: $(this).attr('action')
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false
    , success: function (data) {
      if (data.status) {
        makeAlert('success', data.message, lang['success']);
        $('#change-request-decision').modal('hide');
        $('.dtr-bs-modal').modal('hide');
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

drawDatatable(
  // columnsData
  [
    { data: 'id' },
    { data: 'title' },
    { data: 'description' },
    { data: 'created_by_user' },
    { data: 'file' },
    { data: 'status' },
    { data: 'reason' },
    { data: 'created_at' },
    { data: 'Actions' }
  ],
  // columnDefinitions
  [
    {
      // Actions
      targets: -1,
      orderable: false,
      render: function (data, type, full, meta) {
        let returnedString = '';

        if (permission['delete'] && full.deletable) {
          returnedString += '<a  href="javascript:;" onclick="ShowModalDeleteChangeRequest(' + data + ')" class="item-delete">' +
            feather.icons['trash-2'].toSvg({ class: 'me-50 font-small-4' }) +
            '</a>';
        }

        if (permission['edit'] && full.editable) {
          returnedString += '<a  href="javascript:;" onclick="ShowModalEditChangeRequest(' + data + ')" class="item-edit">' +
            feather.icons['edit'].toSvg({ class: 'me-50 font-small-4' }) +
            '</a>';
        }

        if (full.decision) {
          returnedString += '<a  href="javascript:;" onclick="ShowModalMakeDecisionChangeRequest(' + data + ')" class="item-edit">' +
            feather.icons['help-circle'].toSvg({ class: 'me-50 font-small-4' }) +
            '</a>';
        }

        if (returnedString == '')
          returnedString = '------';

        return (
          returnedString
        );
      }
    }
    , {
      // File
      targets: -5,
      orderable: false,
      render: function (data, type, full, meta) {
        return `<p class="download-file cursor-pointer" data-id="${full.id}"><u>${data}</u></p>`;
      }
    }
    , {
      // Status
      targets: -4,
      orderable: false,
      render: function (data, type, full, meta) {
        if (['Department-Manager-In-Review', 'Responsible-Department-In-Review'].includes(full.original_status))
          return '<span class="badge rounded-pill badge-light-warning">' + data + '</span>';
        else if (['Department-Manager-Rejected', 'Responsible-Department-Rejected'].includes(full.original_status))
          return '<span class="badge rounded-pill badge-light-danger">' + data + '</span>';
        else if (['Responsible-Department-Accepted'].includes(full.original_status))
          return '<span class="badge rounded-pill badge-light-success">' + data + '</span>';
      }
    }
    , {
      // Creator
      targets: -6,
      orderable: false,
      render: function (data, type, full, meta) {
        return '<span class="badge rounded-pill badge-light-primary">' + data + '</span>';
      }
    }
  ],
  // detailsOfItem
  lang['DetailsOfItem'],
  // detailsOfItemKey
  'title'
);