<script>
(function(){
    "use strict";
   var fnServerParams = {  
      "cate": "[name='cate']"    
   }
   initDataTable('.table-permission', admin_url + 'team_password/permission_table_by_cate', false, false, fnServerParams, [0, 'desc']);

   var shareServerParams = {  
      "cate": "[name='cate']",
      "customer_group": "[name='client_group_filter[]']",
      "client_filter": "[name='client_filter[]']"
   }
   initDataTable('.table-share', admin_url + 'team_password/share_table_by_cate', [0], [0], shareServerParams, [1, 'desc']);

   $.each(shareServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {  
            $('.table-share').DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });


   $('#tree').treeview({
    data:  <?php echo html_entity_decode($tree_cate); ?>,
    enableLinks: true,
  });

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

function staff_bulk_actions(){
  "use strict";
  $('#table_share_list_bulk_actions').modal('show');
}


function add_share(){
  "use strict";
  $('.add-title').removeClass('hide');
  $('.update-title').addClass('hide');
  $('input[name="id"]').val('');
  $('input[name="not_in_the_system"]').prop('checked',false); 
  $('.email_fr').addClass('hide');
  $('.client_fr').removeClass('hide');
  $('input[name="share_id"]').val('');
	$('#share').modal();
}

function update_share(el){
    "use strict";
    $('.add-title').addClass('hide');
    $('.update-title').removeClass('hide');
    var id = $(el).data('id');
    $('input[name="shareid"]').val(id);
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

 function add_permission(){
  "use strict";
  $('.add-title').removeClass('hide');
  $('.update-title').addClass('hide');
  $('input[name="id"]').val('');
  $('#permission').modal();
}

function update_permission(id,el){
    "use strict";
    $('.add-title').addClass('hide');
    $('.update-title').removeClass('hide');

    $('input[name="id"]').val(id);
    var list_id = [];
        list_id.push($(el).data('staff'));
    $('select[name="staff[]"]').val(list_id).change();
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
    $('#permission').modal();
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

function tp_delete_bulk_action(event) {
  "use strict";

  if (confirm_delete()) {
    var mass_delete = $('#mass_delete').prop('checked');

    if(mass_delete == true){
      var ids = [];
      var data = {};

      data.mass_delete = true;
      data.rel_type = 'share';

      var rows = $('.table-share').find('tbody tr');
      $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') === true) {
          ids.push(checkbox.val());
        }
      });

      data.ids = ids;
      $(event).addClass('disabled');
      setTimeout(function() {
        $.post(admin_url + 'team_password/tp_delete_bulk_action', data).done(function() {
          window.location.reload();
        }).fail(function(data) {
          $('#table_share_list_bulk_actions').modal('hide');
          alert_float('danger', data.responseText);
        });
      }, 200);
    }else{
      window.location.reload();
    }

  }
}

</script>

