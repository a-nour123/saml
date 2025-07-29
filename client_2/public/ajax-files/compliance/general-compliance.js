
//select2 class


  $('select.multiple-select2').select2();


  $('select.select2').select2();




// function to show error validation 
function showError(data,formId){
  $('#'+formId+' .error').empty();
  $.each(data,function(key,value){
    $('#'+formId+' .error-'+key).empty();
    $('#'+formId+' .error-'+key).append(value);
  });
}

function _showError(data) {
  $('.error').empty();
  $.each(data, function (key, value) {
    $('.error-' + key).empty();
    $('.error-' + key).append(value);
  });
}


//alert function 
function makeAlert($status, message, title) {
  // clear error message 
  $('.error').empty();
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


var basicPickr = $('.flatpickr-basic');
if (basicPickr.length) {
  basicPickr.flatpickr();
}

// clear textarea space 
$('textarea').val(function(i,v){
  return v.replace(/\s+/g,' ').replace(/>(\s)</g,'>\n<');
});

//show error in pop up
function showPopUpError(errors){
 
  $.each(errors,function(key,value){
    makeAlert('error',  value);
  });
}

// rest form 
function resetForm(id){
  $(id).trigger("reset");
  $(id+" .select2").val('-1').trigger('change');
}
