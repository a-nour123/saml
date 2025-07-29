// wizard stepper
$(function () {
  'use strict';

  var bsStepper = document.querySelectorAll('.bs-stepper');
});

// Form repeater
$(function () {
  'use strict';

  // form repeater jquery
  $('.invoice-repeater, .repeater-default').repeater({
    show: function () {
      $(this).slideDown();
      // Feather Icons
      if (feather) {
        feather.replace({ width: 14, height: 14 });
      }
    },
    hide: function (deleteElement) {
      if (confirm('Are you sure you want to delete this element?')) {
        $(this).slideUp(deleteElement);
      }
    }
  });
});

// Show modal for take security awareness exam
// function ShowModalUpdateSecurityAwarenessExam(id) {
//   let url = URLs['edit_exam'];
//   url = url.replace(':id', id);
//   $.ajax({
//     url: url
//     , type: "GET"
//     , headers: {
//       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//     }
//     , success: function (response) {
//       if (response.status) {
//         const editForm = $("#add-security-awareness-exam form");

//         // Start Assign SecurityAwareness data to modal
//         editForm.find('input[name="id"]').val(id);


//         $('#add-security-awareness-exam').modal('show');
//       }
//     }
//     , error: function (response, data) {
//       responseData = response.responseJSON;
//       makeAlert('error', responseData.message, lang['error']);
//     }
//   });
// }

// Show modal for take security awareness exam
function ShowModalAddSecurityAwarenessExam(id) {
  const addForm = $("#add-security-awareness-exam form");

  // Start Assign SecurityAwareness data to modal
  addForm.find('input[name="id"]').val(id);
  $('#add-security-awareness-exam').modal('show');
}
// Submit form for adding security awareness exam
$('#add-security-awareness-exam form').on('submit', function (e) {
  e.preventDefault();

  const id = $(this).find('input[name="id"]').val();
  let url = URLs['create_exam'];
  var formData = new FormData(document.querySelector('#add-security-awareness-exam form'));

  $.ajax({
    url: url
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false
    , cache: false
    , success: function (data) {
      if (data.status) {
        makeAlert('success', data.message, lang['success']);
        $('#add-security-awareness-exam form').trigger("reset");
        $('#add-security-awareness').modal('hide');
        if (data.reload)
          location.reload();
      } else {
        makeAlert('error', data.message, lang['error']);
      }
    }
    , error: function (response, data) {
      responseData = response.responseJSON;

      $('.custom-error').addClass('d-none'); // hide error messages
      makeAlert('error', responseData.message, lang['error']);
      // show Errors
      $.each(responseData.errors, function (key, value) {
        const result = key.split(/(questions)\.(\d{1,})\.(\w{1,})/);
        if (result[3] == 'answer')
          $(`[name="${result[1]}[${result[2]}][${result[3]}]"]`).parents('ul').next().removeClass('d-none');
        else
          $(`[name="${result[1]}[${result[2]}][${result[3]}]"]`).next().removeClass('d-none');
      });
    }
  });
});

// Show modal for editing
function ShowModalViewSecurityAwarenessExam(id) {
  let url = URLs['show_exam'];
  url = url.replace(':id', id);
  $.ajax({
    url: url
    , type: "GET"
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (response) {
      if (response.status) {
        const showModal = $("#show-security-awareness-exam .modal-content");
        showModal.find('.show-question-container').remove();

        response.data.forEach((question, index) => {
          const content = $('.show-questions-container .show-question-container-template').clone();
          content.removeClass('show-question-container-template d-none').addClass('show-question-container');
          content.find('.question-number').text(`${content.find('.question-number').data('title')} (${index + 1})`); // question number
          content.find('.question-content').text(question.question); // question content
          content.find('.optionA-content').text(question.option_a); // question option_a content
          content.find('.optionB-content').text(question.option_b); // question option_b content
          content.find('.optionC-content').text(question.option_c); // question option_c content
          content.find('.optionD-content').text(question.option_d); // question option_d content
          content.find('.optionE-content').text(question.option_e); // question option_e content
          content.find('[name="q1_answer"]').attr('name', `q${index}-answer`); // rename attr name for each question
          content.find(`[name="q${index}-answer"][value="${question.answer}"]`).prop('checked', 1); // question option_e content
 

          showModal.find('.show-questions-container').append(content);
        });

        $('#show-security-awareness-exam').modal('show');
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
function ConfirmShowModalTakeSecurityAwarenessExam(id) {
  Swal.fire({
    title: lang['confirmTakeAnExamMessage']
    , text: lang['revert']
    , icon: 'question'
    , showCancelButton: true
    , confirmButtonText: lang['confirmTakeAnExam']
    , cancelButtonText: lang['cancel']
    , customClass: {
      confirmButton: 'btn btn-relief-success ms-1'
      , cancelButton: 'btn btn-outline-danger ms-1'
    }
    , buttonsStyling: false
  }).then(function (result) {
    if (result.value) {
      $('#preview-security-awareness-file').modal('hide');
      ShowModalTakeSecurityAwarenessExam(id);
    }
  });
}

// Show modal for take security awareness exam
function ShowModalTakeSecurityAwarenessExam(id) {
  let url = URLs['show_take_exam'];
  url = url.replace(':id', id);
  $.ajax({
    url: url
    , type: "GET"
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (response) {
      if (response.status) {
        const examForm = $("#take-security-awareness-exam form");
        const stepsHeaderContainer = examForm.find('.bs-stepper-header');
        const StepsContnetContainer = examForm.find('.bs-stepper-content');
        examForm.find('input[name="id"]').val(id);
        examForm.find('input[name="uniqid"]').val(response.uniqid);

        // Reset content
        stepsHeaderContainer.html('');
        StepsContnetContainer.html('');
        // <i data-feather="check" class="font-medium-3"></i>
        response.data.forEach((question, index) => {

          // Add step icons (Question 1)
          const QuestionId = `question-${index}`;
          stepsHeaderContainer.append(`
            <div class="step" data-target="#${QuestionId}" role="tab" id="${QuestionId}-trigger">
                <button type="button" class="step-trigger" id="questions_${index}_answer">
                    <span class="bs-stepper-box">${index + 1}</span>
                    <span class="bs-stepper-label">
                        <span class="bs-stepper-title">${lang['Question']} (${index + 1})</span>
                        <span class="bs-stepper-subtitle text-danger d-none">${lang['NoAnswer']}</span>
                    </span>
                </button>
            </div>
          `);

          // Add steps content (question content and its options)
          StepsContnetContainer.append(`
            <div id="${QuestionId}" class="content" role="tabpanel" aria-labelledby="${QuestionId}-trigger">
              <div class="content-header">
                  <h5 class="mb-0">${lang['Question']} (${index + 1})</h5>
              </div>
              <input type="hidden" name="questions[${index}][id]" value="${question.id}">
              <p class="text-muted mx-2 question-content">${question.question}</p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item border-0 px-0">
                        <label for="${QuestionId}A" class="d-flex cursor-pointer">
                            <span class="avatar avatar-tag bg-light-info me-1">${lang['OptionA']}</span>
                            <span class="d-flex align-items-center justify-content-between flex-grow-1">
                                <span class="me-1">
                                    <span>${question.option_a}</span>
                                </span>
                                <span>
                                    <input class="form-check-input" id="${QuestionId}A" value="A" type="radio" name="questions[${index}][answer]" />
                                </span>
                            </span>
                        </label>
                    </li>
                    <li class="list-group-item border-0 px-0">
                        <label for="${QuestionId}B" class="d-flex cursor-pointer">
                            <span class="avatar avatar-tag bg-light-info me-1">${lang['OptionB']}</span>
                            <span class="d-flex align-items-center justify-content-between flex-grow-1">
                                <span class="me-1">
                                    <span>${question.option_b}</span>
                                </span>
                                <span>
                                    <input class="form-check-input" id="${QuestionId}B" value="B" type="radio" name="questions[${index}][answer]" />
                                </span>
                            </span>
                        </label>
                    </li>
                    <li class="list-group-item border-0 px-0">
                        <label for="${QuestionId}C" class="d-flex cursor-pointer">
                            <span class="avatar avatar-tag bg-light-info me-1">${lang['OptionC']}</span>
                            <span class="d-flex align-items-center justify-content-between flex-grow-1">
                                <span class="me-1">
                                    <span>${question.option_c}</span>
                                </span>
                                <span>
                                    <input class="form-check-input" id="${QuestionId}C" value="C" type="radio" name="questions[${index}][answer]" />
                                </span>
                            </span>
                        </label>
                    </li>
                    <li class="list-group-item border-0 px-0">
                        <label for="${QuestionId}D" class="d-flex cursor-pointer">
                            <span class="avatar avatar-tag bg-light-info me-1">${lang['OptionD']}</span>
                            <span class="d-flex align-items-center justify-content-between flex-grow-1">
                                <span class="me-1">
                                    <span>${question.option_d}</span>
                                </span>
                                <span>
                                    <input class="form-check-input" id="${QuestionId}D" value="D" type="radio" name="questions[${index}][answer]" />
                                </span>
                            </span>
                        </label>
                    </li>
                    <li class="list-group-item border-0 px-0">
                        <label for="${QuestionId}E" class="d-flex cursor-pointer">
                            <span class="avatar avatar-tag bg-light-info me-1">${lang['OptionE']}</span>
                            <span class="d-flex align-items-center justify-content-between flex-grow-1">
                                <span class="me-1">
                                    <span>${question.option_e}</span>
                                </span>
                                <span>
                                    <input class="form-check-input" id="${QuestionId}E" value="E" type="radio" name="questions[${index}][answer]" />
                                </span>
                            </span>
                        </label>
                    </li>
                </ul>
                <div class="d-flex justify-content-between">
                    <button type="button"` + ((index == 0 /* first question */) ? 'class="btn btn-outline-secondary btn-prev" disabled' : 'class="btn btn-primary btn-prev"') + `>` + feather.icons['arrow-left'].toSvg({
            class: 'align-middle me-sm-25 me-0'
          }) +
            `<span class="align-middle d-sm-inline-block d-none">${lang['Previous']}</span>
                      </button>`

            + ((response.data.length == (index + 1)) ?
              `<button class="btn btn-success btn-submit">${lang['Submit']}</button>`
              : `<button type="button" class="btn btn-primary btn-next">
                          <span class="align-middle d-sm-inline-block d-none">${lang['Next']}</span>` + feather.icons['arrow-right'].toSvg({
                class: 'align-middle me-sm-25 me-0'
              }) +
              `</button>`)
            +
            `</div>
          `);
        });

        const modernVerticalWizard = document.querySelector('.modern-vertical-wizard-example');
        // Modern Vertical Wizard
        // --------------------------------------------------------------------
        if (typeof modernVerticalWizard !== undefined && modernVerticalWizard !== null) {
          var modernVerticalStepper = new Stepper(modernVerticalWizard, {
            linear: false
          });

          $(modernVerticalWizard)
            .find('.btn-next')
            .on('click', function () {
              modernVerticalStepper.next();
            });
          $(modernVerticalWizard)
            .find('.btn-prev')
            .on('click', function () {
              modernVerticalStepper.previous();
            });
        }

        // Start Assign SecurityAwareness data to modal
        examForm.find('input[name="id"]').val(id);


        $('#take-security-awareness-exam').modal('show');
      }
    }
    , error: function (response, data) {
      if (response.status == 403)
        location.reload();
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
    }
  });
}

// Submit form for adding security awareness exam
$('#take-security-awareness-exam form').on('submit', function (e) {
  e.preventDefault();

  const id = $(this).find('input[name="id"]').val(),
    examForm = $("#take-security-awareness-exam form");
  let url = URLs['take_exam'];
  var formData = new FormData(document.querySelector('#take-security-awareness-exam form'));

  $.ajax({
    url: url
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false
    , cache: false
    , success: function (data) {
      if (data.status) {
        examForm.find('.bs-stepper-subtitle').addClass('d-none');
        makeAlert('success', data.message, lang['success']);
        $('#take-security-awareness-exam form').trigger("reset");
        $('#add-security-awareness').modal('hide');
        ShowModalTakeSecurityAwarenessExamResult(data.result)
      } else {
        examForm.find('.bs-stepper-subtitle').addClass('d-none');
        makeAlert('error', data.message, lang['error']);
      }
    }
    , error: function (response, data) {
      if (response.status == 403)
        location.reload();
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
      // show Errors
      examForm.find('.bs-stepper-subtitle').addClass('d-none');
      $.each(responseData.errors, function (key, value) {
        $(`#${key.replaceAll('.', '_')}`).find('.bs-stepper-subtitle').removeClass('d-none');
      });
    }
  });
});


// Show exam result after take exam alert modal
function ShowModalTakeSecurityAwarenessExamResult(result) {
  const isRtl = $('html').attr('data-textdirection') === 'rtl';
  const customSvg = $('.custom-svg-ratings');
  if (customSvg.length) {
    customSvg.rateYo({
      rating: (parseFloat(result.success_answers / result.total * 5).toPrecision(2)),
      starSvg:
        "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'>" +
        "<path d='M12 6.76l1.379 4.246h4.465l-3.612 2.625 1.379" +
        ' 4.246-3.611-2.625-3.612 2.625' +
        ' 1.379-4.246-3.612-2.625h4.465l1.38-4.246zm0-6.472l-2.833' +
        ' 8.718h-9.167l7.416 5.389-2.833 8.718 7.417-5.388' +
        ' 7.416 5.388-2.833-8.718' +
        " 7.417-5.389h-9.167l-2.833-8.718z'-></path>",
      rtl: isRtl
    });
  }

  Swal.fire({
    title: $('.rating-container').clone().html(),
    text: result.message,
    icon: 'success',
    customClass: {
      confirmButton: 'btn btn-primary'
    },
    buttonsStyling: false
  }).then(function (result) {
    location.reload();
  });
}

// Show exam result after alert modal
function ShowModalViewSecurityAwarenessExamResult(id) {
  let url = URLs['show_exam_result'];
  url = url.replace(':id', id);
  $.ajax({
    url: url
    , type: "GET"
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (response) {
      if (response.status) {
        const cloneRating = $('.rating-container').clone();
        const isRtl = $('html').attr('data-textdirection') === 'rtl';
        const customSvg = cloneRating.find('.custom-svg-ratings');
        if (customSvg.length) {
          customSvg.rateYo({
            rating: (parseFloat(response.result.success_answers / response.result.total * 5).toPrecision(2)),
            starSvg:
              "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'>" +
              "<path d='M12 6.76l1.379 4.246h4.465l-3.612 2.625 1.379" +
              ' 4.246-3.611-2.625-3.612 2.625' +
              ' 1.379-4.246-3.612-2.625h4.465l1.38-4.246zm0-6.472l-2.833' +
              ' 8.718h-9.167l7.416 5.389-2.833 8.718 7.417-5.388' +
              ' 7.416 5.388-2.833-8.718' +
              " 7.417-5.389h-9.167l-2.833-8.718z'-></path>",
            rtl: isRtl
          });
        }

        Swal.fire({
          title: cloneRating.html(),
          text: response.result.message,
          icon: 'success',
          customClass: {
            confirmButton: 'btn btn-primary'
          },
          buttonsStyling: false
        });
      }
    }
    , error: function (response, data) {
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
    }
  });
}

// send link to contacts

// // Assuming swal_cancelButtonText is defined somewhere in your code
// var swal_cancelButtonText = lang['cancel'];
// function GenerateLinkForUsersOutside(id) {
//   // Get the base URL
//   var baseUrl = window.location.origin;

//   // Construct the complete URL with the 'id' parameter
//   var link = baseUrl + "/admin/security-awareness/exam/" + id;

//   Swal.fire({
//       title: lang['AcceptShareLinkOutside'],
//       icon: 'question',
//       showCancelButton: true,
//       confirmButtonText: lang['Yes'],
//       cancelButtonText: swal_cancelButtonText,
//       customClass: {
//           confirmButton: 'btn btn-relief-success ms-1',
//           cancelButton: 'btn btn-outline-danger ms-1'
//       },
//       buttonsStyling: false
//   }).then(function (result) {
//       if (result.value) {
//           var tempInput = document.createElement("input");
//           tempInput.value = link;
//           document.body.appendChild(tempInput);
//           tempInput.select();
//           tempInput.setSelectionRange(0, 99999); // For mobile devices
//           document.execCommand("copy");
//           document.body.removeChild(tempInput);
//           makeAlert('success', lang['Linkcopiedsuccessfully'], 'Success');
//       } else {
//           makeAlert('error', 'Failed to copy link to clipboard', 'Error');
//       }
//   });
// }


var swal_cancelButtonText = lang['cancel'];

function GenerateLinkForUsersOutside(id) {
  var encodedId = btoa(id);
  var baseUrl = window.location.origin;
  var link = baseUrl + "/grc/public/admin/security-awareness/exam/" + encodedId;
  Swal.fire({
      title: lang['AcceptShareLinkOutside'],
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: lang['Yes'],
      cancelButtonText: swal_cancelButtonText,
      customClass: {
          confirmButton: 'btn btn-relief-success ms-1',
          cancelButton: 'btn btn-outline-danger ms-1'
      },
      buttonsStyling: false
  }).then(function(result) {
      if (result.value) {
          // Create a temporary input element
          var tempInput = document.createElement("input");
          // Set the value of the input element to the link
          tempInput.value = link;
          // Append the input element to the document
          document.body.appendChild(tempInput);
          // Select the text inside the input element
          tempInput.select();
          tempInput.setSelectionRange(0, 99999); // For mobile devices
          // Execute the copy command
          document.execCommand("copy");
          // Remove the temporary input element from the document
          document.body.removeChild(tempInput);
          // Show success message
          makeAlert('success', lang['Linkcopiedsuccessfully'], 'Success');
      } else {
          // Show error message
          makeAlert('error', 'Failed to copy link to clipboard', 'Error');
      }
  });

}



function makeAlert(status, message, title) {
  // On load Toast
  if (title == 'Success')
      title = '👋' + title;
  toastr[status](message, title, {
      closeButton: true,
      tapToDismiss: false,
  });
}


// // copy link of Awarness
// function GenerateLinkForUsersOutside(id) {
//   const addForm = $("#add-security-awareness-exam form");

//   // Start Assign SecurityAwareness data to modal
//   addForm.find('input[name="id"]').val(id);
//   $('#add-security-awareness-exam').modal('show');
// }


