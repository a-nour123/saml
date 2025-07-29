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
$('#add-new-security-awareness form').on('submit', function (e) {
  var formData = new FormData(document.querySelector('#add-new-security-awareness form'));

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
        $('#add-new-security-awareness').modal('hide');
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
$('#edit-security-awareness form.todo-modal').on('submit', function (e) {
  e.preventDefault();

  const id = $(this).find('input[name="id"]').val();
  let url = URLs['update'];
  url = url.replace(':id', id);
  var formData = new FormData(document.querySelector('#edit-security-awareness form.todo-modal'));
  formData.append('_method', 'put');

  $.ajax({
    url: url
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false
    , success: function (data) {
      if (data.status) {
        makeAlert('success', data.message, lang['success']);
        $('#edit-security-awareness form.todo-modal').trigger("reset");
        $('#edit-security-awareness').modal('hide');
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

function DeleteSecurityAwareness(id) {
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
function ShowModalEditSecurityAwareness(id) {
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
        const editForm = $("#edit-security-awareness form.todo-modal");

        // Start Assign SecurityAwareness data to modal
        editForm.find('input[name="id"]').val(id);
        editForm.find("input[name='title']").val(response.data.title);

        // Set additional_stakeholders
        response.data.additional_stakeholders.forEach(additionalStakeholderId => {
          editForm.find(`select[name='additional_stakeholders[]'] option[value='${additionalStakeholderId}']`).attr('selected', true).trigger('change');
        });

        // Set team
        response.data.team_ids.forEach(teamId => {
          editForm.find(`select[name='team_ids[]'] option[value='${teamId}']`).attr('selected', true).trigger('change');
        });

        // Set last review date
        editForm.find("input[name='last_review_date']").val(response.data.last_review_date).flatpickr({
          enableTime: false,
          dateFormat: "Y-m-d",
        });

        // Set review frequency
        editForm.find("input[name='review_frequency']").val(response.data.review_frequency);

        // Set next review date
        editForm.find("input[name='next_review_date']").val(response.data.next_review_date);

        // Set approval date
        editForm.find("input[name='approval_date']").val(response.data.approval_date).flatpickr({
          enableTime: false,
          dateFormat: "Y-m-d",
        });

        // Set status
        editForm.find("select[name='status'] option").attr('selected', false); // deselect all options
        editForm.find(`select[name='status'] option[value='${response.data.status}']`).attr('selected', true).trigger('change');

        // Set reviewer
        editForm.find("select[name='reviewer'] option").attr('selected', false); // deselect all options
        if (response.data.reviewer)
          editForm.find(`select[name='reviewer'] option[value='${response.data.reviewer}']`).attr('selected', true).trigger('change');

        // Set privacy
        editForm.find("select[name='privacy'] option").attr('selected', false); // deselect all options
        if (response.data.privacy)
          editForm.find(`select[name='privacy'] option[value='${response.data.privacy}']`).attr('selected', true).trigger('change');

        // Set description
        editForm.find("textarea[name='description']").val(response.data.description);

        // Set opned status
        editForm.find("input[name='opened']").val(1);
        editForm.find("input[name='opened']").prop('checked', response.data.opened).trigger('change');

        addMessageToChat(response.data);
        // End Assign SecurityAwareness data to modal

        $('.dtr-bs-modal').modal('hide');
        $('#edit-security-awareness').modal('show');
      }
    }
    , error: function (response, data) {
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
    }
  });
}

// Show modal for editing
function ShowModalViewSecurityAwareness(id) {
  let url = URLs['show'];
  url = url.replace(':id', id);
  $.ajax({
    url: url
    , type: "GET"
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (response) {
      if (response.status) {
        const showModal = $("#show-security-awareness .modal-content");

        // Start Assign SecurityAwareness data to modal
        showModal.find('input[name="id"]').val(id);
        showModal.find("input[name='title']").val(response.data.title);

        // Set additional_stakeholders
        const additionalStakeholderContainer = showModal.find(`select[name='additional_stakeholders[]']`);
        additionalStakeholderContainer.find('option').remove();
        response.data.additional_stakeholders.forEach(additionalStakeholderName => {
          additionalStakeholderContainer.append(`<option selected>${additionalStakeholderName}</option>`);
        });

        // Set team
        const teamContainer = showModal.find(`select[name='team_ids[]']`);
        teamContainer.find('option').remove();
        response.data.teams.forEach(teamName => {
          teamContainer.append(`<option selected>${teamName}</option>`);
        });

        // Set last review date
        showModal.find("input[name='last_review_date']").val(response.data.last_review_date);

        // Set review frequency
        showModal.find("input[name='review_frequency']").val(response.data.review_frequency);

        // Set next review date
        showModal.find("input[name='next_review_date']").val(response.data.next_review_date);

        // Set approval date
        showModal.find("input[name='approval_date']").val(response.data.approval_date);

        // Set status
        showModal.find("select[name='status'] option").attr('selected', false); // deselect all options
        showModal.find(`select[name='status'] option[value='${response.data.status}']`).attr('selected', true).trigger('change');

        // Set reviewer
        const securityAwarenessReviewerContainer = showModal.find(`select[name='reviewer']`);
        securityAwarenessReviewerContainer.find('option').remove();
        securityAwarenessReviewerContainer.append(`<option selected>${response.data.reviewer}</option>`);


        // Set privacy
        const securityAwarenessPrivacyContainer = showModal.find(`select[name='privacy']`);
        securityAwarenessPrivacyContainer.find('option').remove();

        if (response.data.privacy) {
          securityAwarenessPrivacyContainer.append(`<option selected>${response.data.privacy}</option>`);
        }

        // Set description
        showModal.find("textarea[name='description']").val(response.data.description);

        // Set opned status
        showModal.find("input[name='opened']").prop('checked', response.data.opened).trigger('change');

        addMessageToChat(response.data);
        // End Assign SecurityAwareness data to modal

        $('.dtr-bs-modal').modal('hide');
        $('#show-security-awareness').modal('show');
      }
    }
    , error: function (response, data) {
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
    }
  });
}

// Show modal for editing
function ShowModalFilePreviewSecurityAwareness(id) {
  let url = URLs['preview'];
  url = url.replace(':id', id);
  $.ajax({
    url: url
    , type: "GET"
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (response) {
      if (response.status) {
        const previewModal = $("#preview-security-awareness-file .modal-content");
        let modalTitle = previewModal.find('.modal-title').data('title');
        modalTitle = modalTitle.replace('SEC_AWARE', `(${response.data.title})`);
        modalTitle = modalTitle.replace('FILE_NAME', `(${response.data.file_name})`);

        previewModal.find('.modal-title').text(modalTitle);
        if (response.data.takeExam) {
          previewModal.find('button.take-exam').removeClass('d-none').data('id', id);
        } else {
          previewModal.find('button.take-exam').addClass('d-none').data('id', '');
        }

        if (response.data.showExamResult) {
          previewModal.find('button.show-exam-result').removeClass('d-none').data('id', id);
        } else {
          previewModal.find('button.show-exam-result').addClass('d-none').data('id', '');
        }

        // reset model body
        previewModal.find('.modal-body').html('');
        // Preview file
        if (response.data.file_extension == 'pdf') {
          previewModal.find('.modal-body').append(`<iframe src="${response.data.file_path}#toolbar=0" style="width:100%; height:100%;" frameborder="0"></iframe>`);
          setTimeout(() => {
            removeTempLinkedFile(id);
          }, 1000);
        } else if (response.data.file_extension == 'mp4') { // video mp4
          previewModal.find('.modal-body').append(`<video playsinline controls poster="${response.data.banner_path}">
            <source src="${response.data.file_path}" type="video/${response.data.file_extension}" />
          </video>`);

          const player = new Plyr('video', { captions: { active: true } });
          // Expose player so it can be used from the console
          window.player = player;

          setTimeout(() => {
            removeTempLinkedFile(id);
          }, 3000);
        }

        $('.dtr-bs-modal').modal('hide');
        $('#preview-security-awareness-file').modal('show');
      }
    }
    , error: function (response, data) {
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
    }
  });
}

// Show delete alert modal
function ShowModalDeleteSecurityAwareness(id) {
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
      DeleteSecurityAwareness(id);
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

// dataPickr custom 
dateTimePickr = $('.flatpickr-date-time-compliance');
// Date & TIme
if (dateTimePickr.length) {
  dateTimePickr.flatpickr({
    enableTime: false,
    dateFormat: "Y-m-d",
  });
}

// Handle change status event
$('[name="status"]').on('change', function () {
  const parentModalContent = $(this).parents('.modal-content');
  const status = $(this).val();
  if (status == 3) { // Approved
    parentModalContent.find('[name="approval_date"]').parents('div.mb-1').show();
    parentModalContent.find('[name="reviewer"]').parents('div.mb-1').hide();
    parentModalContent.find('[name="privacy"]').parents('div.mb-1').show();
  } else if (status == 2) { // InReview
    parentModalContent.find('[name="approval_date"]').parents('div.mb-1').hide();
    parentModalContent.find('[name="reviewer"]').parents('div.mb-1').show();
    parentModalContent.find('[name="privacy"]').parents('div.mb-1').hide();
  } else { // Draft
    parentModalContent.find('[name="approval_date"]').parents('div.mb-1').hide();
    parentModalContent.find('[name="reviewer"]').parents('div.mb-1').hide();
    parentModalContent.find('[name="privacy"]').parents('div.mb-1').hide();
  }
});

/* Start change dates event */
$("[name='last_review_date']").on('change', function () {
  const parentModalContent = $(this).parents('.modal-content'),
    last_review = $(this).val(),
    days = parentModalContent.find("[name='review_frequency']").val();

  if (days !== '' && last_review !== '') {
    if (days != 0) {
      const url = URLs['nextReview'] + "/" + days + "/" + last_review;

      $.ajax({
        url: url
        , success: function (response) {
          parentModalContent.find("[name='next_review_date']").val(response);
        }
      });

    } else {
      parentModalContent.find("[name='next_review_date']").val(last_review);
    }
  }
});

$("[name='review_frequency']").on('change', function () {
  const parentModalContent = $(this).parents('.modal-content'),
    days = $(this).val(),
    last_review = parentModalContent.find("[name='last_review_date']").val();

  if (days !== '' && last_review !== '') {
    const url = URLs['nextReview'] + "/" + days + "/" + last_review;
    $.ajax({
      url: url
      , success: function (response) {
        parentModalContent.find("[name='next_review_date']").val(response);
      }
    });
  }
});

// Download security awareness file start
$(document).on("click", ".security_awareness_file", function () {
  $('.dtr-bs-modal').modal('hide');
  const form = $('#download-file-form');
  form.find('[name="security_awareness_id"').val($(this).data('securityAwarenessId'));
  form.trigger('submit');
});

$('#preview-security-awareness-file .take-exam').on('click', function () {
  $('.dtr-bs-modal').modal('hide');
  ConfirmShowModalTakeSecurityAwarenessExam($(this).data('id'));
});

$('#preview-security-awareness-file .show-exam-result').on('click', function () {
  $('.dtr-bs-modal').modal('hide');
  ShowModalViewSecurityAwarenessExamResult($(this).data('id'));
});

function removeTempLinkedFile(id) {
  let url = URLs['removeTempFile'];
  url = url.replace(':id', id);
  $.ajax({
    url: url
    , type: "post"
    , data: {
      file_path: id
    }
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (response) {
      //
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
    { data: 'id' },
    { data: 'title' },
    { data: 'description' },
    { data: 'status' },
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

        returnedString += '<a  href="javascript:;" onclick="ShowModalViewSecurityAwareness(' + data + ')" class="item-edit dropdown-item ">' +
          feather.icons['eye'].toSvg({
            class: 'me-50 font-small-4'
          }) +
          `${lang['View']}</a>`;

        returnedString += '<a  href="javascript:;" onclick="ShowModalFilePreviewSecurityAwareness(' + data + ')" class="item-edit dropdown-item ">' +
          feather.icons['eye'].toSvg({
            class: 'me-50 font-small-4'
          }) +
          `${lang['FilePreview']}</a>`;

        if (permission['download']) {
          returnedString += `<span class="tem-edit dropdown-item security_awareness_file" data-security-awareness-id="${data}">` + feather.icons['edit'].toSvg({ class: 'me-50 font-small-4' }) + `${lang['download']}</span>`
        }

        if (full.editable) {
          returnedString += '<a  href="javascript:;" onclick="ShowModalEditSecurityAwareness(' + data + ')" class="item-edit dropdown-item ">' +
            feather.icons['edit'].toSvg({
              class: 'me-50 font-small-4'
            }) +
            `${lang['Edit']}</a>`;
        }

        if (full.deletable) {
          returnedString += '<a  href="javascript:;" onclick="ShowModalDeleteSecurityAwareness(' + data + ')" class="dropdown-item  btn-flat-danger">' +
            feather.icons['trash-2'].toSvg({
              class: 'me-50 font-small-4'
            }) +
            `${lang['Delete']}</a>`;
        }

        if (full.takeExam) {
          returnedString += '<a href="javascript:;" onclick="ConfirmShowModalTakeSecurityAwarenessExam(' + data + ')" class="dropdown-item">' +
            feather.icons['help-circle'].toSvg({
              class: 'me-50 font-small-4'
            }) +
            `${lang['TakeAnExam']}</a>`;
        }

        if (full.createExam) {
          returnedString += '<a href="javascript:;" onclick="ShowModalAddSecurityAwarenessExam(' + data + ')" class="dropdown-item">' +
            feather.icons['help-circle'].toSvg({
              class: 'me-50 font-small-4'
            }) +
            `${lang['AddTheExam']}</a>`;
        }

        if (full.generateUrl) {
          returnedString += '<a href="javascript:;" onclick="GenerateLinkForUsersOutside(' + data + ')" class="dropdown-item">' +
            feather.icons['mail'].toSvg({ // Replace 'help-circle' with 'mail'
              class: 'me-50 font-small-4'
            }) +
            `${lang['GenerateLink']}</a>`;
        }


        if (full.showExam) {
          returnedString += '<a href="javascript:;" onclick="ShowModalViewSecurityAwarenessExam(' + data + ')" class="dropdown-item">' +
            feather.icons['help-circle'].toSvg({
              class: 'me-50 font-small-4'
            }) +
            `${lang['ShowTheExam']}</a>`;
        }

        if (full.showExamResult) {
          returnedString += '<a href="javascript:;" onclick="ShowModalViewSecurityAwarenessExamResult(' + data + ')" class="dropdown-item">' +
            feather.icons['help-circle'].toSvg({
              class: 'me-50 font-small-4'
            }) +
            `${lang['ShowAnExamResult']}</a>`;
        }

        // if (full.deletable) {
        //   returnedString += '<a href="javascript:;" onclick="ShowModalUpdateSecurityAwarenessExam(' + data + ')" class="dropdown-item">' +
        //     feather.icons['edit'].toSvg({
        //       class: 'me-50 font-small-4'
        //     }) +
        //     `${lang['AddTheExam']}</a>`;
        // }

        if (returnedString == '') {
          returnedString = '------';
          return returnedString;
        }

        else
          return (
            '<div class="d-inline-flex">' +
            '<a class="pe-1 dropdown-toggle hide-arrow text-primary" data-bs-toggle="dropdown">' +
            feather.icons['more-vertical'].toSvg({
              class: 'font-small-4'
            }) +
            '</a>' +
            '<div class="dropdown-menu dropdown-menu-end">' +
            returnedString +
            '</div>' +
            '</div>'


          );
      }
    }
    , {
      // Label for status
      targets: -3,
      render: function (data, type, full, meta) {
        const _class = (data == "Draft" ? 'danger' : ((data == "InReview" ? 'warning' : 'success')));
        return `<span class="badge rounded-pill badge-light-${_class}">` + data + '</span>';
      }
    }
  ],
  // detailsOfItem
  lang['DetailsOfItem'],
  // detailsOfItemKey
  'title'
);
