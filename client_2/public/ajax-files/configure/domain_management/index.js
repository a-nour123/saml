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
    { data: 'id' },
    { data: 'name' },
    { data: 'order' },
    { data: 'parentFamily' },
    { data: 'familiesOlny' },
    { data: 'Actions' }
  ],
  // columnDefinitions
  [
    {
      // Actions
      targets: -1,
      orderable: false,
      render: function (data, type, full, meta) {
        // إذا لم يكن هناك أذونات، عرض رسالة فارغة
        if (!permission['delete'] && !permission['edit']) {
          return '------';
        }
    
        // إنشاء القائمة المنسدلة
        let dropdownMenu = `
          <div class="dropdown">
            <a class="pe-1 dropdown-toggle hide-arrow text-primary" href="#" role="button" id="actionsDropdown${data}" data-bs-toggle="dropdown" aria-expanded="false">
              ${feather.icons['more-vertical'].toSvg({ class: 'font-small-4' })}
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown${data}">
        `;
    
        // زر Delete
        if (permission['delete']) {
          dropdownMenu += `
            <li>
              <a href="javascript:;" class="dropdown-item item-delete" onclick="ShowModalDeleteDomain(${data})">
                ${feather.icons['trash-2'].toSvg({ class: 'me-50 font-small-4' })} ${lang['Delete']}
              </a>
            </li>
          `;
        }
        
        // زر Edit
        if (permission['edit']) {
          dropdownMenu += `
            <li>
              <a href="javascript:;" class="dropdown-item item-edit" onclick="ShowModalEditDomain(${data})">
                ${feather.icons['edit'].toSvg({ class: 'me-50 font-small-4' })} ${lang['Edit']}
              </a>
            </li>
          `;
        }
        
        
    
        // إنهاء القائمة المنسدلة
        dropdownMenu += `</ul></div>`;
    
        return dropdownMenu;
      }
    }
    
    , {
      // Label for parent
      targets: -3,
      orderable: false,
      render: function (data, type, full, meta) {
        return '<span class="badge rounded-pill badge-light-success">' + data + '</span>';
      }
    }
    , {
      // Label for sub-domains
      targets: -2,
      orderable: false,
      render: function (data, type, full, meta) {
        returnedData = '';
        data.forEach(element => {
          returnedData += '<span class="badge rounded-pill badge-light-primary" style="margin: 4px">' +
            element +
            '</span>'
        });
        return returnedData;
      }
    }
  ],
  // detailsOfItem
  lang['DetailsOfItem'],
  // detailsOfItemKey
  'name'
);
