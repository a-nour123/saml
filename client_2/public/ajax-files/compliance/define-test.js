
//select2 class

if (jQuery.fn.select2) {
  $('select.multiple-select2').select2();
  $('select.select2').select2();
}

//filter Column 
function filterColumn(i, val) {
  $('.dt-advanced-search').DataTable().column(i).search(val, false, true).draw();
}
//function to create database
function createDatatable (JsonList) {
      
var isRtl = $('html').attr('data-textdirection') === 'rtl';

var dt_ajax_table = $('.datatables-ajax'),
  dt_filter_table = $('.dt-column-search'),
  dt_adv_filter_table = $('.dt-advanced-search'),
  dt_responsive_table = $('.dt-responsive'),
  assetPath = '../../../app-assets/';

if($('body').attr('data-framework') === 'laravel') {
  assetPath = $('body').attr('data-asset-path');
}
if (dt_adv_filter_table.length) {

    
  var dt_adv_filter = dt_adv_filter_table.DataTable({
      data: JsonList,
      responsive: true,
      autoWidth: true,
      searching: true,
     
    columns: [
      { data: 'responsive_id' },
      { data: 'select' },
      { data: 'control' },
      { data: 'framework' },
      { data: 'family' },
      { data: 'tester' },
      { data: 'test_frequency' },
      { data: 'last_date' },
      { data: 'next_date' },
      { data: 'Actions' },
     
      
      


    ],
    
    

    columnDefs: [
      {
        
        title: '#',
        className: 'index',
        orderable: false,
        responsivePriority: 2,
        targets: 0
      },
      {
        // For Checkboxes
        targets: 1,
        title: 'select',
        orderable: false,
        responsivePriority: 3,
        render: function (data, type, full, meta) {
          return (
            '<div class="form-check"> <input class="form-check-input dt-checkboxes" name="audits[]" type="checkbox" value="'+data+'" id="checkbox" /><label class="form-check-label" for="checkbox"></label></div>'
          );
        },
        checkboxes: {
          selectAllRender:
            '<div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" /><label class="form-check-label" for="checkboxSelectAll"></label></div>'
        }
      },
      
      {
        // Actions
        targets: -1,
        orderable: false,
        render: function (data, type, full, meta) {
          let returnedString = '<div class="dropdown">' +
              '<a class="pe-1 dropdown-toggle hide-arrow text-primary" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' +
              feather.icons['more-vertical'].toSvg({ class: 'font-small-4' }) +
              '</a>' +
              '<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">';
      
          
          returnedString += '<li><a class="dropdown-item btn-flat-danger" href="javascript:;" onclick="ShowModalDeleteTest(' + data + ')">' +
              feather.icons['trash-2'].toSvg({ class: 'me-50 font-small-4' }) +
              ' Delete</a></li>';
      
          
          /*
          if (permission['edit']) {
            returnedString += '<li><a class="dropdown-item btn-flat-primary" href="javascript:;" onclick="ShowModalEditTest(' + data + ')">' +
                feather.icons['edit'].toSvg({ class: 'me-50 font-small-4' }) +
                ' Edit</a></li>';
          }
      
          if (permission['audit']) {
            returnedString += '<li><a class="dropdown-item btn-flat-primary" href="javascript:;" onclick="showModalCreateAudit(' + data + ')">' +
                feather.icons['file-plus'].toSvg({ class: 'me-50 font-small-4' }) +
                ' Audit</a></li>';
          }
          */
      
          returnedString += '</ul></div>';
      
          return returnedString;
        }
      }
      ,
     
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
  dt_adv_filter.on( 'order.dt search.dt', function () {
    dt_adv_filter.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
        cell.innerHTML = i+1;
    } );
  } ).draw();
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
function showError(data,formId){
  $('#'+formId+' .error').empty();
  $.each(data,function(key,value){
    $('#'+formId+' .error-'+key).empty();
    $('#'+formId+' .error-'+key).append(value);
  });
}

//alert function 
function makeAlert($status, message, title) {
  // On load Toast
  toastr[$status](message,title,
      {
          closeButton: true,
          tapToDismiss: false,
      }
  );
}


// dataPickr custom for compliance
dateTimePickr = $('.flatpickr-date-time-compliance');
// Date & TIme
if (dateTimePickr.length) {
  dateTimePickr.flatpickr({
    enableTime: true,
    minDate: "today"
  });
}


$('#add-new-test form').submit(function(e){
  e.preventDefault();
  $.ajax({
          url: $(this).attr('action'),
          type: "POST",
          data: $(this).serialize(),
              success: function (data) {
                if(data['status']){
                  makeAlert('success',  'You have successfully added new value!', ' Created!');
                  $('form#add-new-record').trigger("reset");
                  $("#advanced-search-datatable").load(location.href+" #advanced-search-datatable>*","");
                  loadDatatable();
                }else{
                  showError(data['errors'],'add-new-record');
                }
              }
    });
});


