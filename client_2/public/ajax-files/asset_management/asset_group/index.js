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

// Redirect to asset list with asset id
$(document).on('click', '.redirect-edit-asset', function () {
  window.location.href = `${URLs['asset_list']}?asset=${$(this).data('id')}`;
});

drawDatatable(
  // columnsData
  [
    { data: 'id' ,orderable: false},
    { data: 'name' ,orderable: false},
    { data: 'assets' ,orderable: false},
    { data: 'Actions' ,orderable: false}
  ],
  // columnDefinitions
  [
    {
      // Actions
      targets: -1,
      orderable: false,
      render: function (data, type, full, meta) {
        let returnedString = '';

        if (permission['delete']) {
          returnedString += '<a  href="javascript:;" onclick="ShowModalDeleteAsset(' + data + ')" class="item-edit">' +
            feather.icons['trash-2'].toSvg({ class: 'me-50 font-small-4' }) +
            '</a>';
        }

        if (permission['edit']) {
          returnedString += '<a  href="javascript:;" onclick="ShowModalEditAsset(' + data + ')" class="item-edit">' +
            feather.icons['edit'].toSvg({ class: 'font-small-4' }) +
            '</a>';
        }

        if (returnedString == '')
          returnedString = '------';

        return (
          returnedString
        );
      }
    }, {
      // Label for assets
      targets: -2,
      render: function (data, type, full, meta) {
        returnedData = '';
        data.forEach(element => {
          returnedData += `<span class="badge rounded-pill badge-light-primary cursor-pointer redirect-edit-asset" data-id="${element.id}">` +
            element.name +
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