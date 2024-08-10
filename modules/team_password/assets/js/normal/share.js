(function(){
    "use strict";
   var fnServerParams = {
    "obj_id": "[name='obj_id']",
    "type": "[name='type']"
   }
   initDataTable('.table-share', admin_url + 'team_password/share_table', false, false, fnServerParams, [0, 'desc']);

   if($('input[name="unlimited"]').prop('checked') == true){
      $('#effective_time').removeAttr('required');
    }else{
      $('#effective_time').attr('required', 1);
    }


  if($('select[name="customer_group"]').val() != ''){
      $('#client_sl').addClass('hide');
    }else{
      $('#client_sl').removeClass('hide');
    }
})(jQuery);

  /**
   * open modal to add share
   */
  function add_share(){
    "use strict";
    $('.add-title').removeClass('hide');
    $('.update-title').addClass('hide');
    $('input[name="id"]').val('');
    $('input[name="not_in_the_system"]').prop('checked',false); 
    $('.email_fr').addClass('hide');
    $('.client_fr').removeClass('hide');
  	$('#share').modal();
  }
  /**
   * switch input client or email
   */
  function open_frame(el){
    "use strict";
      if($(el).is(':checked')){
        $('.email_fr').removeClass('hide');
        $('.client_fr').addClass('hide');
      }
      else{
        $('.email_fr').addClass('hide');
        $('.client_fr').removeClass('hide');
      }
  }
  /**
   * open modal to edit share
   */
  function update(el){
    "use strict";
    $('.add-title').addClass('hide');
    $('.update-title').removeClass('hide');
    var id = $(el).data('id');
    $('input[name="id"]').val(id);
    if($(el).data('not_in_the_system') == 'on'){
        $('input[name="not_in_the_system"]').prop('checked',true); 
        $('.email_fr').removeClass('hide');
        $('.client_fr').addClass('hide');
        $('input[name="email"]').val($(el).data('email'));
    }
    else{
        $('input[name="not_in_the_system"]').prop('checked',false); 
        $('.client_fr').removeClass('hide');
        $('.email_fr').addClass('hide');
        $('select[name="client"]').val($(el).data('client')).change();
        $('select[name="customer_group"]').val($(el).data('customer_group')).change();
    }
    $('select[name="share_id"]').val($(el).data('share_id')).change();

    if($(el).data('effective_time') != '0000-00-00 00:00:00' && $(el).data('effective_time') != ''){
      $('input[name="effective_time"]').val($(el).data('effective_time'));
    }

    if($(el).data('read')=='on'){
      $('input[name="read"]').prop('checked',true);
    }
    else{
      $('input[name="read"]').prop('checked',false);      
    }

    if($(el).data('write')=='on'){
      $('input[name="write"]').prop('checked',true);
    }
    else{
      $('input[name="write"]').prop('checked',false);      
    }

    if($(el).data('unlimited')== 1){
      $('input[name="unlimited"]').prop('checked',true);
    }
    else{
      $('input[name="unlimited"]').prop('checked',false);      
    }

    if($(el).data('send_notify')== 1){
      $('input[name="send_notify"]').prop('checked',true);
    }
    else{
      $('input[name="send_notify"]').prop('checked',false);      
    }

    $('#share').modal();

  }

  function unlimited_change(el){
    if($('input[name="unlimited"]').prop('checked') == true){
      $('#effective_time').removeAttr('required');
    }else{
      $('#effective_time').attr('required', 1);
    }
  }

  function customer_group_change(el){
    if(el.value != ''){
      $('#client_sl').addClass('hide');
    }else{
      $('#client_sl').removeClass('hide');
    }
  }


function copyToClipboard(element) {
  "use strict";
  var temp = $("input[id="+element+"]").val();
  var copyText = document.getElementById(element);
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  alert_float('success','Copied');

}