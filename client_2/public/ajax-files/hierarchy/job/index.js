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

drawDatatable(
  // columnsData
  [
    {
      data: 'id'
    },
    {
      data: 'name'
    },
    {
      data: 'code'
    },
    {
      data: 'description'
    },
    {
      data: 'employees'
    },
    {
      data: 'created_at'
    },
    {
      data: 'Actions'
    }
  ],
  // columnDefinitions
  [
    {
      // Actions
      targets: -1,
      orderable: false,
      searchable: false,
      render: function (data, type, full, meta) {
        let returnedString = '<div class="dropdown">' +
            '<a class="pe-1 dropdown-toggle hide-arrow text-primary" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical font-small-4">' +
            '<circle cx="12" cy="12" r="1"></circle>' +
            '<circle cx="12" cy="5" r="1"></circle>' +
            '<circle cx="12" cy="19" r="1"></circle>' +
            '</svg>' +
            '</a>' +
            '<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">';
    
        if (permission['delete'] && full.employees.length == 0) {
          returnedString += '<li><a class="dropdown-item item-delete" href="javascript:;" onclick="ShowModalDeleteJob(' + data + ')">' +
              feather.icons['trash-2'].toSvg({ class: 'me-50 font-small-4' }) +
              `${lang['Delete']}</a></li>`;
        }
    
        if (permission['edit']) {
          returnedString += '<li><a class="dropdown-item item-edit" href="javascript:;" onclick="ShowModalEditJob(' + data + ')">' +
              feather.icons['edit'].toSvg({ class: 'font-small-4' }) +
              `${lang['Edit']}</a></li>`;
        }
    
        returnedString += '</ul></div>';
    
        if (returnedString == '<div class="dropdown">' +
          '<a class="pe-1 dropdown-toggle hide-arrow text-primary" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' +
          '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical font-small-4">' +
          '<circle cx="12" cy="12" r="1"></circle>' +
          '<circle cx="12" cy="5" r="1"></circle>' +
          '<circle cx="12" cy="19" r="1"></circle>' +
          '</svg>' +
          '</a><ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"></ul></div>') {
          returnedString = '------';
        }
    
        return returnedString;
      }
    },
     {
    // Label for employee
    orderable: false,
    targets: -3,
    render: function (data, type, full, meta) {
      returnedData = '';
      data.forEach(element => {
        returnedData +=
          '<span class="badge rounded-pill badge-light-success">' +
          element +
          '</span>'
      });
      return returnedData;
    }
  }],
  // detailsOfItem
  lang['DetailsOfItem'],
  // detailsOfItemKey
  'name'
);
