
//select2 class
$('select.multiple-select2').select2();
$('select.select2').select2();

// function to show error validation
function showError(data, formId) {
  $('#' + formId + ' .error').empty();
  $.each(data, function (key, value) {
    $('#' + formId + ' .error-' + key).empty();
    $('#' + formId + ' .error-' + key).append(value);
  });
}

//alert function
function makeAlert($status, message, title) {
  // On load Toast
  toastr[$status](message, title,
    {
      closeButton: true,
      tapToDismiss: false,
    }
  );
}

drawDatatable(
  // columnsData
  [
    { data: 'id' },
    { data: 'audit_name' },
    { data: 'framework' },
    { data: 'FrameworkControlWithFramworks' },
    { data: 'name' },
    { data: 'action_status' },
    { data: 'test_number' },
    { data: 'auditer' },
    { data: 'UserTester' },
    { data: 'created_at'},
    { data: 'last_date' },
    { data: 'next_date' },
    { data: 'audit_status' },
    { data: 'Actions' },
  ],
  // columnDefinitions
  [
    {
      // action_status Column
      targets: 5, // The index of action_status (0-based index)
      render: function (data, type, full, meta) {
        return full.action_status == 1 ? 
          `<span class="badge bg-success">${lang['Closed']}</span>` : 
          `<span class="badge bg-danger">${lang['Open']}</span>`;
      }
    },
    {
      // UserTester Column
      targets: 8, // The index of UserTester (0-based index)
      render: function (data, type, full, meta) {
        if (!data) {
          return `<span class="badge bg-secondary"></span>`;
        }
        // Split the UserTester string by comma and create badges for each tester
        const testers = data.split(',').map(tester => tester.trim());
        const badges = testers.map(tester => `<span class="badge bg-primary me-1">${tester}</span>`).join('');
        return badges;
      }
    },
    {
      // Actions
      targets: -1,
      orderable: false,
      render: function (data, type, full, meta) {
        let returnedString = '';
    
        
        if (full.pending === true && full.editable === true) {
          return 'Pending';
        }
    
    
        if (permission['result']) {
          returnedString += '<li><a class="dropdown-item btn-flat-primary" href="javascript:;" onclick="showResultAudit(' + data + ')">' +
            feather.icons['file'].toSvg({ class: 'me-50 font-small-4' }) +
              `${lang['Details']}</a></li>`;
    
          if (full.action_status === 1 && full.editable === true) {
            returnedString += '<li><a class="dropdown-item btn-flat-primary" href="javascript:;" onclick="openReopenModal(' + data + ')">' +
              feather.icons['refresh-cw'].toSvg({ class: 'me-50 font-small-4' }) +
              ` ${lang['Reopen']}</a></li>`;
          }
        }
    
      
        if (returnedString === '') {
          return '------';
        } else {
          return (
            '<div class="dropdown">' +
            '<a class="pe-1 dropdown-toggle hide-arrow text-primary" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' +
            feather.icons['more-vertical'].toSvg({ class: 'font-small-4' }) +
            '</a>' +
            '<ul class="dropdown-menu dropdown-menu-end">' +
            returnedString +
            '</ul>' +
            '</div>'
          );
        }
      }
    }
    ,
     
  ],
  // detailsOfItem
  lang['DetailsOfItem'],
  // detailsOfItemKey
  'name'
);

$(document).on('change', '#framework', function () {

  var selectedFramework = $(this).val();
  if (selectedFramework == null || selectedFramework == '') {
    selectedFramework = -1
  }
  // Make an AJAX request based on the selected value
  $.ajax({
    url: URLs['get_framework_controls'] + '/' + selectedFramework, // Replace with your backend URL
    method: 'GET', // Use the appropriate HTTP method (GET, POST, etc.)
    success: function (response) {
      controls = response
      var controlsOptions =
        '<option value="" selected>' + lang['selectOption'] + '</option>';
      $.each(controls, function (index, control) {
        controlsOptions += '<option value="' + control.short_name + '">' + control.short_name + '</option>'
      });
      $('#control').html(controlsOptions);
    },
    error: function (response, data) {
      responseData = response.responseJSON;
      makeAlert('error', responseData.message, lang['error']);
      showError(responseData.errors);
    }
  })
})
$(document).ready(function() {
  window.openReopenModal = function(dataId) { // Make function accessible globally
    $('#reopenModal').data('id', dataId);
    $('#reopenModal').modal('show');
  };
});