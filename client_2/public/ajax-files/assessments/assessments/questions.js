let table = $('.QuestionTable').DataTable({
    lengthChange: true,
    processing: false,
    serverSide: true,
    ajax: {
        url: datatable_url,
        data: function (d) {
            d.assessment_id = _assessment_id
        }
    },
     language: {
        "sProcessing": "{{ __('locale.Processing') }}",
        "sSearch": "{{ __('locale.Search') }}",
        "sLengthMenu": "{{ __('locale.lengthMenu') }}",
        "sInfo": "{{ __('locale.info') }}",
        "sInfoEmpty": "{{ __('locale.infoEmpty') }}",
        "sInfoFiltered": "{{ __('locale.infoFiltered') }}",
        "sInfoPostFix": "",
        "sSearchPlaceholder": "",
        "sZeroRecords": "{{ __('locale.emptyTable') }}",
        "sEmptyTable": "{{__('locale.NoDataAvailable') }}",
        "oPaginate": {
            "sFirst": "",
            "sPrevious": "{{ __('locale.Previous') }}",
            "sNext": "{{ __('locale.NextStep') }}",
            "sLast": ""
        },
        "oAria": {
            "sSortAscending": "{{ __('locale.sortAscending') }}",
            "sSortDescending": "{{ __('locale.sortDescending') }}"
        }
    },
    columns: [
        {
            name: "DT_RowIndex", data: "DT_RowIndex", searchable: false, orderable: false
        },


        {
            name: "file_attachment", data: "file_attachment", searchable: true, orderable: false, render: function (d) {
                var icon = '<i class="fa fa-close fa-sm text-danger"></i>';
                if (d) {
                    icon = '<i class="fa fa-check text-success"></i>';
                }
                return icon;
            }
        },
        {
            name: "question_logic", data: "question_logic", render: function (d) {
                var icon = '<i class="fa fa-close fa-sm text-danger"></i>';
                if (d) {
                    icon = '<i class="fa fa-check text-success"></i>';
                }
                return icon;
            }
        },
        {
            name: "risk_assessment", data: "risk_assessment", searchable: true, orderable: false, render: function (d) {
                var icon = '<i class="fa fa-close fa-sm text-danger"></i>';
                if (d) {
                    icon = '<i class="fa fa-check text-success"></i>';
                }
                return icon;
            }
        },
        {
            name: "compliance_assessment", data: "compliance_assessment", searchable: true, orderable: false, render: function (d) {
                var icon = '<i class="fa fa-close fa-sm text-danger"></i>';
                if (d) {
                    icon = '<i class="fa fa-check text-success"></i>';
                }
                return icon;
            }
        },

        {
            name: "maturity_assessment", data: "maturity_assessment", searchable: true, orderable: false, render: function (d) {
                var icon = '<i class="fa fa-close fa-sm text-danger"></i>';
                if (d) {
                    icon = '<i class="fa fa-check text-success"></i>';
                }
                return icon;
            }
        },

        {
            name: "answers_count", data: "answers_count", searchable: true, orderable: false, render: function (d) {

                var icon = '<i class="fa fa-close fa-sm text-danger"></i>';
                if (d > 0) {
                    icon = '<i class="fa fa-check text-success"></i>';
                }
                return icon;
            }
        },
        {
            name: "answer_type", data: "answer_type", searchable: false, sortable: false, orderable: false,
        },

        {
            name: "question", data: "question", render: function (data) {

                if (data.length > 100) {
                    data = data.slice(0, 100) + ' ...?';
                }

                return data;
            }
        },
        {
            name: "answer", 
            data: "id",
            searchable: false,
            orderable: false,
            render: function (id, type, row) {
                if (row.answer_type !== 3) {
                    return `<a href="/admin/answers/index/${id}" class="btn btn-sm btn-success">
                                <i class="fa fa-check fa-sm"></i> Answers
                            </a>`;
                } else {
                    return '';
                }
            }
        },
        {
            name: "actions", data: "actions"
        }
    ],
    columnDefs: [

        {
            targets: -1,
            width: "30%"
        },
        {
            targets: -2,
            width: "50%"
        }
    ],


});

var quill = new Quill('.question,.edit_question', {
    modules: {
        toolbar: '.question-toolbar'
    },
    theme: 'snow'
});

var edit_quill = new Quill('.edit_question', {
    modules: {
        toolbar: '.edit_question-toolbar'
    },
    theme: 'snow'
});


$('.answer_type').on('change', function () {
    var answer_type = $(this).val();
    // $('.options .mb-1 input[type="checkbox"]').prop('checked', false);
    if (answer_type == 1) {
        $('.controls , .options .mb-1').removeClass('d-none');
    } else if (answer_type == 2) {
        $('.controls').val('').trigger('change');
        $('#ComplianceAssessment, #MaturityAssessment').prop('checked', false);
        $('.options .mb-1:is(.file_attachment,.question_logic,.risk_assessment)').removeClass('d-none');
        $('.controls ,.options .mb-1:not(.file_attachment,.question_logic,.risk_assessment)').addClass('d-none');

    } else if (answer_type == 3) {
        $('.controls').val('').trigger('change');
        $('#ComplianceAssessment, #MaturityAssessment').prop('checked', false);
        $(".controls ,.options .mb-1:not(.file_attachment)").addClass('d-none');
    }
});

/*add new question*/
$('#add_questions').on('submit', function (e) {
    e.preventDefault();
    var url = $(this).attr('action');
    //var question = $('#question .ql-editor').html(); // includes html  tags
    var question = quill.getText();
    var data = new FormData(this);
    data.append('question', question);
    data.append('assessment_id', _assessment_id);
    if (quill.getLength() == 1) {
        $('.error-question').empty().append('Question is Required').css('display', 'inline-block');
        return 0;
    }
    $.ajax({
        processData: false,
        contentType: false,
        cache: false,
        type: "post",
        data: data,
        url: url,
        success: function (response) {
            makeAlert('success', response);
            // $('.sideNavBtn.active').trigger('click');

            table.page(table.page.info().page).draw('page');

            $('#new-question-modal').modal('hide');
        },
        error: function (xhr) {
            if (xhr.responseJSON.message) {
                makeAlert('error', xhr.responseJSON.message);
            }
        }
    });

});

/*edit question*/
$(document).on('click', '.edit_question_btn', function (e) {
    e.preventDefault();
    // 1- get question data using ajax call
    let url = $(this).data('url');

    $.ajax({
        type: "GET",
        url: url,
        success: function (question) {

            // 2- render data into form
            var question_edit_form = $('#edit_question_form');
            question_edit_form.find('input[name="question_id"]').val(question.id);
            edit_quill.setText(question.question);
            var controls = $('#edit_question_form select[name="control_id"] option');
            /* var questions_control = question.control;*/

            $('#edit_question_form select[name="control_id"] option[value="' + question.control_id + '"]').prop('selected', true);
            $('#edit_question_form select[name="control_id"]').trigger('change');

            var answer_types = $('#edit_question_form select[name="answer_type"] option');
            answer_types.each(function (key, option) {
                if (question.answer_type == option.value) {
                    $(option).prop('selected', true);
                }
            });
            $('#edit_question_form select[name="answer_type"]').trigger('change');

            $(question_edit_form).find('select[name="answer_type"]').trigger('change');
            $(question_edit_form).find('input[name="file_attachment"]').prop('checked', !!question.file_attachment);
            $(question_edit_form).find('input[name="question_logic"]').prop('checked', !!question.question_logic);
            $(question_edit_form).find('input[name="risk_assessment"]').prop('checked', !!question.risk_assessment);
            $(question_edit_form).find('input[name="compliance_assessment"]').prop('checked', !!question.compliance_assessment);
            $(question_edit_form).find('input[name="maturity_assessment"]').prop('checked', !!question.maturity_assessment);

        },
        error: function (xhr) {
            makeAlert('error', xhr.responseJSON.message);

        }
    }).then(function () {
        // 3- open edit modal
        $('#edit-question-modal').modal('show')
    })
});

/* on submit edit_question_form*/

$('#edit_question_form').on('submit', function (e) {
    e.preventDefault();
    var data = new FormData(this);
    var question = edit_quill.getText();
    data.append('question', question);

    data.append('assessment_id', _assessment_id);
    if (edit_quill.getLength() == 1) {
        $('.error-question').empty().append('Question is Required').css('display', 'inline-block');
        return 0;
    }
    var question_id = $(this).find('input[name="question_id"]').val();
    var url = $(this).attr('action');
    url = url.replace(':id', question_id);
    $.ajax({
        processData: false,
        contentType: false,
        cache: false,
        type: "Post",
        data: data,
        url: url,
        success: function (response) {
            makeAlert('success', response);
            // $('.sideNavBtn.active').trigger('click');
            table.page(table.page.info().page).draw('page');
            $('#edit-question-modal').modal('hide');
        }, error: function (xhr) {
            if (xhr.responseJSON.message) {
                makeAlert('error', xhr.responseJSON.message);
            }
        }
    })


});

// delete question
$(document).on('click', '.delete_question_btn', function (e) {
    e.preventDefault();
    var url = $(this).data('url');

    Swal.fire({
        title: swal_title,
        text: swal_text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: swal_confirmButtonText,
        cancelButtonText: swal_cancelButtonText,
        customClass: {
            confirmButton: 'btn btn-relief-success ms-1',
            cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                type: "DELETE",
                url: url,
                data: {
                    assessment_id: _assessment_id
                },
                headers: {
                    'x-csrf-token': $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (response) {
                    var message = response.message;
                    makeAlert('success', message, swal_success);
                    // $('.sideNavBtn.active').trigger('click');
                    table.page(table.page.info().page).draw('page');
                },
                error: function (xhr) {

                }
            })
        }
    });


})

