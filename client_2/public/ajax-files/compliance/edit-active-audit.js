// audit-update submit form
$("#form-audit-update").submit(function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        data: $(this).serialize(),
        success: function (data) {
            if (data["status"]) {
                _makeAlert('success', data.message, lang['success']);
            } else {
                showError(data["errors"], "form-audit-update");
                _makeAlert('error', data.message, lang['error']);
            }
        }
        , error: function (response, data) {
            const responseData = response.responseJSON;
            _makeAlert('error', responseData.message, lang['error']);
        }
    });
});

// add comment submit form
$("#add-comment-form").submit(function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        data: $(this).serialize(),
        success: function (data) {
            if (data["status"]) {
                $("#blogComment .card-body").append(data["html"]);
                $("#add-comment-form textarea").val("");
            } else {
                showError(data["errors"], "form-audit-update");
            }
        },
    });
});

// status [warning, success, error]
function _makeAlert($status, message, title) {
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

var quill = new Quill('#risk_addational_notes_submit', {
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