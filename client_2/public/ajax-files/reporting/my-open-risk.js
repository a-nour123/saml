//  ajax to call risk list and call create datatable
function loadDatatable() {
    $.ajax({
      url: listURL
      , type: "GET"
      , headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
      , data: {}
      , success: function (data) {
        createDatatable(data);
      }
      , error: function () {
        //
      }
    });
  }
  loadDatatable(); // Load table for first time


  $(document).ready(function () {
    $('.multiple-select2').select2();

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
      if (ownerManger)
        $("[name='owner_manager_id']").append(`<option value="${ownerManger.id}">${ownerManger.name}</option>`);
    });

  });


  //filter Column
  function filterColumn(i, val) {

    $('.dt-advanced-search').DataTable().column(i).search(val, false, true).draw();

  }
  //function to create database
  function createDatatable(JsonList) {

    var isRtl = $('html').attr('data-textdirection') === 'rtl';

    var dt_ajax_table = $('.datatables-ajax'),
      dt_filter_table = $('.dt-column-search'),
      dt_adv_filter_table = $('.dt-advanced-search'),
      dt_responsive_table = $('.dt-responsive'),
      riskPath = '../../../app-risks/';

    if ($('body').attr('data-framework') === 'laravel') {
      riskPath = $('body').attr('data-risk-path');
    }
    if (dt_adv_filter_table.length) {


      var dt_adv_filter = dt_adv_filter_table.DataTable({
        data: JsonList,
        responsive: true,
        autoWidth: true,
        searching: true,
        columns: [
          { data: 'responsive_id' },
          { data: 'status' },
          { data: 'subject' },
          { data: 'inherent_risk_current' },
          { data: 'created_at' },
          // { data: 'mitigation_planned' },
          // { data: 'management_review' },
          { data: 'Actions' }
        ],


        columnDefs: [
          {
            className: 'index',
            orderable: false,
            targets: 0
          },
          {
            // Actions
            targets: -1,
            orderable: false,
            render: function (data, type, full, meta) {
              let url = showURL;
              url = url.replace(':id', data);
              return (
                `<a  href="${url}" class="item-show">` +
                feather.icons['eye'].toSvg({ class: 'me-50 font-small-4' }) +
                '</a>'
                // +
                // '<a  href="javascript:;" onclick="showModalEditRisk(' + data + ')" class="item-edit">' +
                // feather.icons['edit'].toSvg({ class: 'font-small-4' }) +
                // '</a>'
              );
            }
          },
          // {
          //   // Label for tags
          //   targets: -2,
          //   render: function (data, type, full, meta) {
          //     if (data == 'No' || data == 'لا')
          //       return '<span class="badge rounded-pill badge-light-danger">' + data + '</span>';
          //     else
          //       return '<span class="badge rounded-pill badge-light-success">' + data + '</span>';
          //   }
          // },
          // {
          //   // Label for tags
          //   targets: -3,
          //   render: function (data, type, full, meta) {
          //     if (data == 'No' || data == 'لا')
          //       return '<span class="badge rounded-pill badge-light-danger">' + data + '</span>';
          //     else
          //       return '<span class="badge rounded-pill badge-light-success">' + data + '</span>';
          //   }
          // },
          {
            // Label for tags
            targets: -3,
            render: function (data, type, full, meta) {
              return '<div class="risk-cell-holder" style="position:relative;">' + data[0] + '<span class="risk-color" style="background-color:' + data[1] + ';position: absolute;width: 20px;height: 20px;right: 10px;top: 50%;transform: translateY(-50%);border-radius: 2px;border: 1px solid;"></span></div>'
              // return '<span class="badge rounded-pill badge-light-success">' + data + '</span>';
            }
          }

        ],
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        orderCellsTop: true,

        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function (row) {
                var data = row.data();
                return 'Details of ' + data['name'];
              }
            }),
            type: 'column',
            renderer: function (api, rowIdx, columns) {
              var data = $.map(columns, function (col, i) {
                return col.title !== ''
                  ? '<tr data-dt-row="' +
                  col.rowIndex +
                  '" data-dt-column="' +
                  col.columnIndex +
                  '">' +
                  '<td>' +
                  col.title +
                  ':' +
                  '</td> ' +
                  '<td>' +
                  col.data +
                  '</td>' +
                  '</tr>'
                  : '';
              }).join('');

              return data ? $('<table class="table"/><tbody />').append(data) : false;
            }
          }
        },
        language: {
          paginate: {
            previous: '&nbsp;',
            next: '&nbsp;'
          }
        }
      });

      // Start index from 1 not get index from item recorded ID
      dt_adv_filter.on('order.dt search.dt', function () {
        dt_adv_filter.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
          cell.innerHTML = i + 1;
        });
      }).draw();
    }

    // call function in input filter
    $('input.dt-input').on('keyup', function () {
      filterColumn($(this).attr('data-column'), $(this).val());
    });
    // call function in select filter
    $('select.dt-select').on('change', function () {
      filterColumn($(this).attr('data-column'), $(this).val());
    });

    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm').removeClass('form-control-sm');
  }

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
