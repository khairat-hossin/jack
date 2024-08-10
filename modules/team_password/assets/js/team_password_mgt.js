function copyToClipboard(element) {
  "use strict";
  var password = $(element).data('password');
  if(password != ''){
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(password).select();
    document.execCommand("copy");
    $temp.remove();
    alert_float('success','Copied');
  }
  else{
    alert_float('warning','The data is empty');    
  }
}