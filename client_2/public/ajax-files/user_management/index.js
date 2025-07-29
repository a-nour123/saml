
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

$('#add-new-test form').submit(function (e) {
  e.preventDefault();
  $.ajax({
    url: $(this).attr('action'),
    type: "POST",
    data: $(this).serialize(),
    success: function (data) {
      if (data['status']) {
        makeAlert('success', 'You have successfully added new value!', ' Created!');
        $('form#add-new-record').trigger("reset");
        redrawDatatable();
      } else {
        showError(data['errors'], 'add-new-record');
      }
    }
  });
});

drawDatatable(
  // columnsData
  [
    { data: 'id' },
    { data: 'type' },
    { data: 'username' },
    { data: 'name' },
    { data: 'email' },
    { data: 'role' },
    { data: 'admin' },
    { data: 'active' },
    { data: 'department' },
    { data: 'Actions' },
  ],
  // columnDefinitions
  [
    {
      // Actions
      targets: -1,
      orderable: false,
      render: function (data, type, full, meta) {
        let returnedString = '';

        if (permission['edit'] && ((full['id'] != 1) || (full['id'] == 1 && currentUser == 1))) {
          returnedString += '<a  href="javascript:;" onclick="UserEdit(' + data + ')" class="dropdown-item  btn-flat-primary">' +
            feather.icons['edit'].toSvg({ class: 'me-50 font-small-4' }) +
            'Edit</a>';
        }
        if (permission['delete'] && ((full['id'] != 1) || (full['id'] == 1 && currentUser == 1))) {
          returnedString += '<a  href="javascript:;" onclick="UserDelete(' + data + ')" class="dropdown-item  btn-flat-primary">' +
            feather.icons['delete'].toSvg({ class: 'me-50 font-small-4' }) +
            'Delete</a>';
        }

        if (returnedString == '')
          return ('------');
        else
          return (
            '<div class="d-inline-flex">' +
            '<a class="pe-1 dropdown-toggle hide-arrow text-primary" data-bs-toggle="dropdown">' +
            feather.icons['more-vertical'].toSvg({ class: 'font-small-4' }) +
            '</a>' +
            '<div class="dropdown-menu dropdown-menu-end">' +
            returnedString +
            '</div>' +
            '</div>'
          );
      }
    },
    {
      // Admin
      targets: 6,
      title: 'admin',
      orderable: false,
      render: function (data, type, full, meta) {

        if (data) {
          return ('<button class=" btn badge bg-success">' + feather.icons['check'].toSvg({ class: 'font-small-4 ' }) + '</button>');
        } else {
          return ('<button class=" btn badge bg-danger">' + feather.icons['x'].toSvg({ class: 'font-small-4 ' }) + '</button>')
        }
      }
    },
    {
      // Active
      targets: 7,
      title: 'active',
      orderable: false,
      render: function (data, type, full, meta) {

        $check = (data) ? 'checked' : '';
        if (!permission['edit'])
          return ('------');
        else {
          if (full['id'] == 1)
            return ('<div class="form-check form-check-success form-switch"><input type="checkbox" checked disabled class="form-check-input accountStatus" /></div>');
          else
            return ('<div class="form-check form-check-success form-switch"><input onchange="ChangeAccountStutas(' + full['id'] + ')"  type="checkbox" ' + $check + ' class="form-check-input accountStatus"  /></div>');
        }
      }
    }
  ],
  // detailsOfItem
  lang['DetailsOfItem'],
  // detailsOfItemKey
  'name'
);
