$('.multiple-select2').select2();

// Preventing submit forms
// $('form:not(#download-file-form):not(#delete-file-form)').on('submit', function (e) {
//   e.preventDefault();
// });

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

// Collapse comment start
$('#show-comments-btn').on('click', function () {
  if (!$('#collapseComments').hasClass('show') && $('#add-comment-btn').hasClass('btn-danger')) {
    $('#add-comment').css('display', 'none');
    if ($('#add-comment-btn').hasClass('btn-success')) {
      $('#add-comment-btn').removeClass('btn-success');
      $('#add-comment-btn').addClass('btn-danger');
      $('#add-comment-btn').html($('#x-icon svg').clone(true));
    } else {
      $('#add-comment-btn').addClass('btn-success');
      $('#add-comment-btn').removeClass('btn-danger');
      $('#add-comment-btn').html($('#plus-icon svg').clone(true));
    }
  }
});
$('#add-comment-btn').on('click', function () {
  if ($(this).hasClass('btn-success')) {
    $('#add-comment').slideDown();
    // 1. collapse showed
    if ($('#collapseComments').hasClass('show')) { }
    // 2. collapse is not showed
    else {
      // $('#collapseComments').addClass('show');
      $('#show-comments-btn').trigger('click');
    }
    $(this).removeClass('btn-success');
    $(this).addClass('btn-danger');
    $(this).html($('#x-icon svg').clone(true));
  } else {
    $(this).addClass('btn-success');
    $(this).removeClass('btn-danger');
    // $('#add-comment').fadeIn('d-none');
    $('#add-comment').slideUp();
    $(this).html($('#plus-icon svg').clone(true));
  }
});
// Collapse comment end

// Show edit subject start
$('#edit-subject').on('click', function () {
  $('#static-subject').addClass('d-none');
  $('#edit-subject-container input[name="subject"]').val($('#edit-subject-container input').data('value'));
  $('#edit-subject-container').removeClass('d-none');
});
// Show edit subject End

// Cancel edit subject start
$('#cancel-edit-subject').on('click', function () {
  $('#static-subject').removeClass('d-none');
  $('#edit-subject-container').addClass('d-none');
});
// Cancel edit subject End

// Subject edit start
$('#submit-edit-subject').on('click', function () {
  const id = $('#edit-subject-container input[name="id"]').val(),
    value = $('#edit-subject-container input[name="subject"]').val();

  let url = URLs['updateSubject'];
  $.ajax({
    url: url
    , type: "PUT"
    , data: $('#edit-subject-form').serialize()
    , success: function (data) {
      if (data.status) {
        makeAlert(data.alert ? 'warning' : 'success', data.alert ? `${data.alert}<br>${data.message}` : `${data.message}`, lang['success']);
        $('#edit-subject-container input').data('value', data.data['subject']); // Update risk subject data value
        $('#subject-text').text(data.data['subject']);  // Update risk subject text
        $('#static-subject').removeClass('d-none');
        $('#edit-subject-container').addClass('d-none');
        if (data.reload)
          location.reload();
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
// Subject edit End

// Add comment start
$('#submit-add-comment').on('click', function () {
  let url = URLs['addComment'];
  $.ajax({
    url: url
    , type: "PUT"
    , data: $('#add-comment-form').serialize()
    , success: function (data) {
      if (data.status) {
        $('#add-comment-form').trigger("reset");
        makeAlert('success', data.message, lang['success']);
        // Add new comment text
        $("#all-comments").prepend(`
        <p class="card-text mt-1">
            ${data.data.content}
        </p>
        <hr>
        `);
        if (data.reload)
          location.reload();
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
// Add comment End

// dataPickr custom for compliance
dateTimePickr = $('.flatpickr-date-time-compliance');
// Date & TIme
if (dateTimePickr.length) {
  dateTimePickr.flatpickr({
    enableTime: false,
    // dateFormat: "Y-m-d",
    dateFormat: dataFormat,
  });
}

$('#RiskScoringDetailsBtn').on('click', function () {
  if ($(this).data('showstatus')) {
    $(this).data('showstatus', 0);
    $(this).text($(this).data('showtext'));
  }
  else {
    $(this).data('showstatus', 1);
    $(this).text($(this).data('hidetext'));
  }
});

$('#RiskScoreOverTimeBtn').on('click', function () {
  $(this).css('pointer-events', 'none');
  if ($(this).data('showstatus')) {
    $(this).data('showstatus', 0);
    $(this).text($(this).data('showtext'));
    $('#RiskScoreOverTime').html('');
    $(this).css('pointer-events', '');
  }
  else {
    var tabContainer = $(this).parents('.risk-session');
    var risk_id = $(this).data('riskid');

    $(this).data('showstatus', 1);
    $(this).text($(this).data('hidetext'));

    $.ajax({
      type: "GET",
      url: URLs['getRiskLevels'],
      dataType: 'json',
      success: function (result) {
        riskScoringChart('RiskScoreOverTime', result.data.risk_levels);
        $('#RiskScoreOverTimeBtn').css('pointer-events', '');
      }
      , error: function (response, data) {
        responseData = response.responseJSON;
        makeAlert('error', responseData.message, lang['error']);
        showError(responseData.errors);
        $('#RiskScoreOverTimeBtn').css('pointer-events', '');
      }
    })
  }
});

$('#impact-detail-btn').on('click', function () {
  $('#impact-detail').slideToggle();
  $('#likelihood-detail').css('display', 'none');
});

$('#likelihood-detail-btn').on('click', function () {
  $('#likelihood-detail').slideToggle();
  $('#impact-detail').css('display', 'none');
});

$('#UpdateClassicScoreShowBtn').on('click', function () {
  $(this).fadeOut();
  $('#edit-risk-scoring-form').slideDown();
  $('#edit-risk-scoring-form').prev().slideUp();
});

$('#cancel-edit-risk-scoring').on('click', function () {
  $('#UpdateClassicScoreShowBtn').fadeIn();
  $('#edit-risk-scoring-form').slideUp();
  $('#edit-risk-scoring-form').prev().slideDown();
});

// RiskScoring edit start
$('#update-edit-risk-scoring').on('click', function () {
  let url = URLs['updateRiskScoring'];
  $.ajax({
    url: url
    , type: "PUT"
    , data: $('#edit-risk-scoring-form').serialize()
    , success: function (data) {
      if (data.status) {
        makeAlert('success', `${data.message}`, lang['success']);
        if (data.reload)
          location.reload();
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
// RiskScoring edit End

/**
* Create and Update the risk scoring chart
* 
* @param risk_id
*/
function riskScoringChart(renderTo, risk_levels) {
  var backgroundColor = "#f5f5f5";
  // Creates stops array
  var stops = [
    [0, backgroundColor],
  ];

  risk_levels.sort(function (a, b) {
    if (Number(a.value) > Number(b.value)) {
      return -1;
    }
    if (Number(a.value) < Number(b.value)) {
      return 1;
    }
  })
  risk_levels.push({ value: 0, color: "#fff" });


  var to = 10;
  var plotBands = [];
  for (var i = 0; i < risk_levels.length; i++) {
    var risk_level = risk_levels[i];
    plotBands.push({
      color: risk_level.color,
      to: to,
      from: Number(risk_level.value),
    })
    to = Number(risk_level.value);
  }

  // For all plots, change Date axis to local timezone
  Highcharts.setOptions({
    global: {
      useUTC: false
    }
  });

  var chartObj = new Highcharts.Chart({
    chart: {
      renderTo: renderTo,
      type: 'spline',
    },
    title: {
      text: $('#_RiskScoringHistory').length ? $("#_RiskScoringHistory").val() : 'Risk Scoring History'
    },
    yAxis: [{
      title: {
        text: $('#_RiskScore').length ? $('#_RiskScore').val() : "Risk Score"
      },
      min: 0,
      max: 10,
      gridLineWidth: 0,
      plotBands: plotBands,
    }],
    xAxis: [{
      type: 'datetime',
      dateTimeLabelFormats: { // don't display the dummy year
        millisecond: '%Y-%m-%d<br/>%H:%M:%S',
        second: '%Y-%m-%d<br/>%H:%M:%S',
        minute: '%Y-%m-%d<br/>%H:%M',
        hour: '%Y-%m-%d<br/>%H:%M',
        day: '%Y-%m-%d<br/>%H:%M',
        month: '%Y-%m-%d<br/>%H:%M',
        year: '%Y-%m-%d<br/>%H:%M'
      },
      title: {
        text: $("#_DateAndTime").val() ? $("#_DateAndTime").val() : "Date and time"
      }
    }],
    legend: {
      layout: 'vertical',
      align: 'right',
      verticalAlign: 'middle'
    },
    plotOptions: {
      spline: {
        marker: {
          enabled: true
        }
      }
    },
    series: [
      { name: $('#_RiskScore').length ? $('#_RiskScore').val() : "Inherent Risk" },
      { name: $('#_ResidualRiskScore').length ? $('#_ResidualRiskScore').val() : "ResidualRisk Score" },
    ]

  });

  chartObj.showLoading(`<img src="${assets['showLoading']}"></img>`);
  $.ajax({
    type: "GET",
    url: URLs['residualScoringHistory'],
    dataType: 'json',
    success: function (data) {
      var residual_histories = data.data;
      var residualChartData = [];
      for (var i = 0; i < residual_histories.length; i++) {
        // var date = new Date(histories[i].last_update.replace(/\s/, 'T'));
        // Added the three lines below to make the timestamp work properly with Safari
        var parts = residual_histories[i].last_update.split(/[ \/:-]/g);
        var dateFormatted = parts[1] + "/" + parts[2] + "/" + parts[0] + " " + parts[3] + ":" + parts[4] + ":" + parts[5];
        var date = new Date(dateFormatted);
        residualChartData.push([date.getTime(), Number(residual_histories[i].residual_risk)]);
      }

      chartObj.series[1].setData(residualChartData)
      chartObj.hideLoading();

    },
    error: function (xhr, status, error) {
      if (xhr.responseJSON && xhr.responseJSON.status_message) {
        showAlertsFromArray(xhr.responseJSON.status_message);
      }
    }
  })
  $.ajax({
    type: "GET",
    url: URLs['getScoringHistories'],
    dataType: 'json',
    success: function (data) {
      var histories = data.data;
      var chartData = [];
      for (var i = 0; i < histories.length; i++) {
        // var date = new Date(histories[i].last_update.replace(/\s/, 'T'));
        // Added the three lines below to make the timestamp work properly with Safari
        var parts = histories[i].last_update.split(/[ \/:-]/g);
        var dateFormatted = parts[1] + "/" + parts[2] + "/" + parts[0] + " " + parts[3] + ":" + parts[4] + ":" + parts[5];
        var date = new Date(dateFormatted);
        chartData.push([date.getTime(), Number(histories[i].calculated_risk)]);
      }

      chartObj.series[0].setData(chartData)

      chartObj.hideLoading();

    },
    error: function (xhr, status, error) {
      if (xhr.responseJSON && xhr.responseJSON.status_message) {
        showAlertsFromArray(xhr.responseJSON.status_message);
      }
    }
  })
}

// Load controls of framework
$("[name='framework_id']").on('change', function () {
  const frameworkControls = $(this).find('option:selected').data('controls');
  $("[name='control_id']").find('option:not(:first)').remove();
  $("[name='control_id']").find('option:first').attr('selected', true)
  if (frameworkControls)
    frameworkControls.forEach(frameworkControl => {
      $("[name='control_id']").append(`<option value="${frameworkControl.id}">${frameworkControl.short_name}</option>`);
    });
});

// Load Owner manager
$("[name='owner_id']").on('change', function () {
  const ownerManger = $(this).find('option:selected').data('manager');
  $("[name='owner_manager_id']").find('option:not(:first)').remove();
  $("[name='owner_manager_id']").find('option:first').attr('selected', true)
  if (ownerManger) {
    $("[name='owner_manager_id']").append(`<option value="${ownerManger.id}">${ownerManger.name}</option>`);
    if ($("[name='owner_manager_id']").data('ownerselected') == 1) {
      $("[name='owner_manager_id'] option").prop('selected', true);
    }
  }
});

$("[name='owner_id']").trigger('change');

// Show edit detail start
$('#edit-details').on('click', function () {
  $('#static-details').addClass('d-none');
  $('#edit-details-form').removeClass('d-none');
});
// Show edit detail End

// Cancel edit details start
$('#cancel-edit-details').on('click', function () {
  $('#static-details').removeClass('d-none');
  $('#edit-details-form').addClass('d-none');
});
// Cancel edit details End

// Detail edit start
$('#submit-edit-details').on('click', function () {

  var formData = new FormData(document.querySelector('#edit-details-form'));
  var additionalNotes = quill.root.innerHTML;
  formData.append('additional_notes', additionalNotes);
  let url = URLs['updateDetails'];
  $.ajax({
    url: url
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false
    , success: function (data) {
      if (data.status) {
        makeAlert(data.alert ? 'warning' : 'success', data.alert ? `${data.alert}<br>${data.message}` : `${data.message}`, lang['success']);
        if (data.reload)
          location.reload();
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
// Detail edit End

// Detail edit start
$('#submit-edit-details2').on('click', function () {

  var formData = new FormData(document.querySelector('#edit-details-form2'));
  var additionalNotes = quill.root.innerHTML;
  formData.append('additional_notes', additionalNotes);
    var description = quillDescription.root.innerHTML;
  formData.append('risk_description', description);
  let url = URLs['updateDetails'];
  $.ajax({
    url: url
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false
    , success: function (data) {
      if (data.status) {
        makeAlert(data.alert ? 'warning' : 'success', data.alert ? `${data.alert}<br>${data.message}` : `${data.message}`, lang['success']);
        if (data.reload)
          location.reload();
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
// Detail edit End
// Download supporting documentation start
$('.supporting_documentation').on('click', function () {
  const form = $('#download-file-form');
  form.find('[name="id"').val($(this).data('id'));
  form.find('[name="risk_id"').val($(this).data('riskId'));

  form.trigger('submit');
});
// Download supporting documentation End

// Delete supporting documentation start
$('.delete_supporting_documentation').on('click', function () {
  showModalDeleteRisk($(this).data('id'), $(this).data('riskId'), this);
});

// Delete supporting documentation End

// Show delete alert modal Start
function showModalDeleteRisk(id, riskId, that) {
  Swal.fire({
    title: lang['confirmDeleteFileMessage']
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
      DeleteFile(id, riskId, that)
    }
  });
}
// Show delete alert modal End

// Delete supporting documentation start
function DeleteFile(id, riskId, that) {
  let url = URLs['deleteFile'];
  $.ajax({
    url: url
    , type: "DELETE"
    , data: {
      id: id,
      risk_id: riskId,
      mitigation: $(that).parent().hasClass('mitigation-files') ? 1 : 0
    }
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (response) {
      if (response.status) {
        makeAlert('success', response.message, lang['success']);
        $(that).parent().remove();
        appendLog(response.data.log);
      }
    }
    , error: function (response, data) {
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
    }
  });
}

// Delete supporting documentation End

// Append Log Start
function appendLog($log) {
  $('#collapseAuditTrail').prepend(`<p class="card-text mt-1">${$log}</p>`);
}
// Append Log End

// Accept or reject mitigation start
$('.acceptOrRejectStatus').on('click', function () {

  const data = {
    risk_id: $(this).data('id'),
    accept: $(this).data('value')
  };

  let url = URLs['acceptRejectMitigation'];
  $.ajax({
    url: url
    , type: "POST"
    , data: data
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (data) {
      if (data.status) {
        makeAlert(data.alert ? 'warning' : 'success', data.alert ? `${data.alert}<br>${data.message}` : `${data.message}`, lang['success']);
        if (data.reload)
          location.reload();
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
// Accept or reject mitigation End

 


// Mitigation edit start
$('#submit-edit-mitigation').on('click', function () {

  var formData = new FormData(document.querySelector('#edit-mitigation-form'));
  var currentSolution = quillriskSolution1.root.innerHTML;
  formData.append('current_solution', currentSolution);

  let url = URLs['updateRiskMitigation'];
  $.ajax({
    url: url
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false
    , success: function (data) {
      if (data.status) {
        makeAlert(data.alert ? 'warning' : 'success', data.alert ? `${data.alert}<br>${data.message}` : `${data.message}`, lang['success']);
        if (data.reload)
          location.reload();
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

// Mitigation edit End

// View all reviews start
$('#review-history-btn').on('click', function () {
  $(this).parents('.review-btn').hide();
  $('#last-review-btn').parents('.review-btn').show();
  $('#last-review').hide();
  $('#review-history').show();
});

// View all reviews End

// View last review start
$('#last-review-btn').on('click', function () {
  $(this).parents('.review-btn').hide();
  $('#review-history-btn').parents('.review-btn').show();
  $('#last-review').show();
  $('#review-history').hide();
});

// View last review End

// Start working on risk action
$('#risk-actions').on('change', function () {
  resetActions();

  if (["CloseRisk", "ChangeStatus", "ResetMitigations", "ResetReviews"].includes($(this).val())) {
    // $('.main-containers').addClass('d-none');




    // Start handle CloseRisk action
    if ($(this).val() == "CloseRisk") {
      // $('#change-risk-status-container').addClass('d-none');
      // $('#reset-risk-mitigations-container').addClass('d-none');
      // $('#reset-risk-reviews-container').addClass('d-none');

      $('#closeRiskModal').modal('show');

    }
    // End handle CloseRisk action

    // Start handle ChangeStatus action
    if ($(this).val() == "ChangeStatus") {
      // $('#close-risk-container').addClass('d-none');
      // $('#reset-risk-mitigations-container').addClass('d-none');
      // $('#reset-risk-reviews-container').addClass('d-none');

      $('#changeRiskStatusModal').modal('show'); // Open the modal
    }
    // End handle ChangeStatus action

    // Start handle ResetMitigations action
    if ($(this).val() == "ResetMitigations") {
      $('#reset-risk-mitigations-container').removeClass('d-none');
    }
    // End handle ResetMitigations action

    // Start handle ResetReviews action
    if ($(this).val() == "ResetReviews") {
      $('#reset-risk-reviews-container').removeClass('d-none');
    }
    // End handle ResetReviews action


  } else {

    $('.main-containers').removeClass('d-none');
    $('#close-risk-container').addClass('d-none');



    // Start handle ReopenRisk action
    if ($(this).val() == "ReopenRisk") {
      const data = {
        id: $(this).data('id'),
        _method: 'put'
      }

      let url = URLs['riskReopen'];
      $.ajax({
        url: url
        , type: "POST"
        , data: data
        , headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
        , success: function (data) {
          if (data.status) {
            makeAlert(data.alert ? 'warning' : 'success', data.alert ? `${data.alert}<br>${data.message}` : `${data.message}`, lang['success']);
            if (data.reload)
              location.reload();
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

    }
    // End handle ReopenRisk action

    // Start handle EditRisk action
    if ($(this).val() == "EditRisk") {
      $('#editRiskModal').modal('show');

    }
    if ($(this).val() == "EditRisk2") {
      $('#editRiskModal2').modal('show');

    }
    // End handle EditRisk action

    // Start handle PlanAMitigation action
    if ($(this).val() == "PlanAMitigation") {

      // Active tab link and tab container
      // activeTab('#mitigation-tab', '#mitigation');

      // Show edit mitigation
      $('#edit-mitigation').trigger('click');
      $('#reset-risk-mitigations-container').addClass('d-none');

    }
    // End handle PlanAMitigation action

    // Start handle PerformAReview action
    if ($(this).val() == "PerformAReview") {
      $('#addReviewModal').modal('show'); // Open the modal
    }
    // End handle PerformAReview action

    // Start handle AddAComment action
    if ($(this).val() == "AddAComment") {
      // Show add comment form (trigger the click for the "Add Comment" button)
      $('#add-comment-btn').trigger('click');

      // Scroll to the comment container in the modal (if needed)
      window.scroll({
        top: $("#tags-container").offset().top - 20,
        behavior: 'smooth'
      });
      // Open the modal
      $('#addCommentModal').modal('show'); // Show the modal
    }
    // End handle AddAComment action


    // Start handle PrintableView action
    if ($(this).val() == "PrintableView") {
      // TODO PrintableView: action
    }
    // End handle PrintableView action
  }

});

function activeTab(tabLinkReference, tabContainerReference) {
  // Remove al active from all nav-link classes
  $('.nav-tabs .nav-item .nav-link').removeClass('active');
  // Add active only for mitigation nav-link (tabLinkReference)
  $(`.nav-tabs .nav-item ${tabLinkReference}`).addClass('active');

  // Remove al active from all tab-pane classes
  $('.tab-pane').removeClass('active');
  // Add active only for mitigation tab-pane (tabContainerReference)
  $(`${tabContainerReference}`).addClass('active');
}

// Reset tab content for view static container and hide editable form
$('.nav-link').on('click', function () {
  containers = $(`${$(this).attr('href')} > .row`);
  containers.eq(0).removeClass('d-none');
  containers.eq(1).addClass('d-none');
});

function resetActions() {
  $('#add-comment-btn.btn-danger').trigger('click');
}

// Cancel add review start
$('#cancel-add-review').on('click', function () {
  $('#static-review').removeClass('d-none');
  $('#add-review-form').addClass('d-none');
});

// Add review start
$('#submit-add-review').on('click', function () {

  var formData = new FormData(document.querySelector('#add-review-form'));


  let url = URLs['addRiskReview'];
  $.ajax({
    url: url
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false
    , success: function (data) {
      if (data.status) {
        makeAlert(data.alert ? 'warning' : 'success', data.alert ? `${data.alert}<br>${data.message}` : `${data.message}`, lang['success']);
        if (data.reload)
          location.reload();
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
// Add review End

// Work on next step status Start
$('[name="next_step"]').on('change', function () {
  if ($(this).val() == 2) {
    $('#project-container').removeClass('d-none');
  } else {
    $('#project-container').addClass('d-none');
  }
});
// work on next step status End

// Close reason start
$('#submit-close-reason').on('click', function () {

  var formData = new FormData(document.querySelector('#close-reason-form'));


  let url = URLs['riskCloseReason'];
  $.ajax({
    url: url
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false
    , success: function (data) {
      if (data.status) {
        makeAlert(data.alert ? 'warning' : 'success', data.alert ? `${data.alert}<br>${data.message}` : `${data.message}`, lang['success']);
        if (data.reload)
          location.reload();
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
// Close reason End
// Close reason start
$('#submit-close-reason2').on('click', function () {

  var formData = new FormData(document.querySelector('#close-reason-form2'));


  let url = URLs['riskCloseReason'];
  $.ajax({
    url: url
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false
    , success: function (data) {
      if (data.status) {
        makeAlert(data.alert ? 'warning' : 'success', data.alert ? `${data.alert}<br>${data.message}` : `${data.message}`, lang['success']);
        if (data.reload)
          location.reload();
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
// Close reason End

// change risk status start
$('#submit-change-risk-status').on('click', function () {

  var formData = new FormData(document.querySelector('#change-risk-status-form'));


  let url = URLs['riskChangeStatus'];
  $.ajax({
    url: url
    , type: "POST"
    , data: formData
    , contentType: false
    , processData: false
    , success: function (data) {
      if (data.status) {
        makeAlert(data.alert ? 'warning' : 'success', data.alert ? `${data.alert}<br>${data.message}` : `${data.message}`, lang['success']);
        if (data.reload)
          location.reload();
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
// change risk status End

// Reset risk mitigations start
$('#submit-reset-risk-mitigations').on('click', function () {

  let url = URLs['resetRiskMitigations'];
  const data = {
    id: $(this).data('id'),
    _method: 'put'
  }

  $.ajax({
    url: url
    , type: "POST"
    , data: data
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (data) {
      if (data.status) {
        makeAlert(data.alert ? 'warning' : 'success', data.alert ? `${data.alert}<br>${data.message}` : `${data.message}`, lang['success']);
        if (data.reload)
          location.reload();
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
// Reset risk mitigations End

// Reset risk reviews start
$('#submit-reset-risk-reviews').on('click', function () {

  let url = URLs['resetRiskReviews'];
  const data = {
    id: $(this).data('id'),
    _method: 'put'
  }

  $.ajax({
    url: url
    , type: "POST"
    , data: data
    , headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    , success: function (data) {
      if (data.status) {
        makeAlert(data.alert ? 'warning' : 'success', data.alert ? `${data.alert}<br>${data.message}` : `${data.message}`, lang['success']);
        if (data.reload)
          location.reload();
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
// Reset risk reviews End

var quill = new Quill('#risk_addational_notes', {
  theme: 'snow',
  modules: {
    toolbar: [
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      [{ 'indent': '-1' }, { 'indent': '+1' }],
      [{ 'direction': 'rtl' }], // Right-to-left direction
      ['clean'],
    ],
  },
});
var quillDescription = new Quill('#risk_description', {
  theme: 'snow',
  modules: {
    toolbar: [
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      [{ 'indent': '-1' }, { 'indent': '+1' }],
      [{ 'direction': 'rtl' }], // Right-to-left direction
      ['clean'],
    ],
  },
});
var quillriskAddationalNotes = new Quill('#risk_addational_notes_show', {
  theme: 'snow',
  modules: {
    toolbar: [
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      [{ 'indent': '-1' }, { 'indent': '+1' }],
      [{ 'direction': 'rtl' }], // Right-to-left direction
      ['clean'],
    ],
  },
  readOnly: true
});
var quillriskAddationalDescription = new Quill('#risk_description_editor', {
  theme: 'snow',
  modules: {
    toolbar: [
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      [{ 'indent': '-1' }, { 'indent': '+1' }],
      [{ 'direction': 'rtl' }], // Right-to-left direction
      ['clean'],
    ],
  },
  readOnly: true
});

var quillriskSolution1 = new Quill('#risk_current_solution', {
  theme: 'snow',
  modules: {
    toolbar: [
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      [{ 'indent': '-1' }, { 'indent': '+1' }],
      [{ 'direction': 'rtl' }], // Right-to-left direction
      ['clean'],
    ],
  },
});
var quillriskCurrentSolution = new Quill('#risk_current_solution_show', {
  theme: 'snow',
  modules: {
    toolbar: [
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      [{ 'indent': '-1' }, { 'indent': '+1' }],
      [{ 'direction': 'rtl' }], // Right-to-left direction
      ['clean'],
    ],
  },
  readOnly: true
});

