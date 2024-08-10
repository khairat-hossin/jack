<?php defined('BASEPATH') or exit('No direct script access allowed');
$CI = &get_instance();
if (strpos($CI->uri->uri_string(), 'admin/clients/client/') !== false) 
{
            $params = $CI->uri->uri_string();
            $param  = explode('/',$params);
            $userid = end($param);
            $stripe_connect_show = '';
            $stripe_connect = $CI->db->query('select * from '.db_prefix().'clients where userid="'.$userid.'"')->row();
            if(isset($_GET['a']) && $_GET['a'] == 'stripe_connect')
            {
               $stripe_connect_show = 'yes';
            }
   ?>
   <div class="modal fade" id="stripe_connect" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('multi_stripe_checkout/stripe_checkout_module/save_stripe_connect')); ?>
        <input type="hidden" id="user_id" name="user_id" />
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Stripe Connect</h4>
            </div>
            <div class="modal-body text-center">
                <div class="radio radio-primary radio-inline">
                    <?=$stripe_connect->stripe_checkout?>
                    <input type="radio" id="gp_marketing" value="gp_marketing" name="stripe_connect" />
                    <label for="gp_marketing">G.P Marketing</label>
                </div>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" id="gp_workspace" value="gp_workspace" name="stripe_connect"  />
                    <label for="gp_workspace">G.P Workspace</label>
                </div>
                <div  class="row" style="margin-top:10px;">
                    <div class="col-md-4">
                        <label for="expire_month">Stripe Customer Id</label>
                    </div>
                    <div class="col-md-6">
                        <?php $stripe_id = !empty($stripe_connect->stripe_id)?$stripe_connect->stripe_id:''; ?>
                        <input type="text" class="form-control" name="stripe_id" id="stripe_id" value="<?php echo $stripe_id;?>" required >
                    </div>  
                </div>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
   <script>
       $('document').ready(function()
       {
           var  userid = $('input[name="userid"]').val();
           var stripe_connect_show = "<?=$stripe_connect_show?>";
           $("body").append('<div class="dt-loader"></div>');
           $('.customer-tabs').append('<li class="customer_tab_profile"><a href="#" data-toggle="modal" data-target="#stripe_connect" id="stripe_connect_btn"><i class="fa fa-brands fa-cc-stripe  menu-icon"></i>Stripe Connect</a></li>');
           $("body").find(".dt-loader").remove();
           $('#user_id').val(userid);
           if(stripe_connect_show == 'yes')
           {
              $('#stripe_connect_btn').click();
           }
           var stripe_connect =  "<?=$stripe_connect->stripe_connect?>";
           if(stripe_connect != null || stripe_connect != '')
           {
               document.getElementById(stripe_connect).checked = true;
           }
           
       })
    
    </script>
   <?php
}
if ($CI->uri->uri_string() ==  'admin/invoices/invoice')
{
    ?>
     <script>
      $('document').ready(function()
       {
          client_dropdown();
       $('select[name="clientid"]').change('on',function(e)
       {
           client_dropdown();
       });
       });
       function client_dropdown()
       {
           
          var client_id = $('#clientid').val();
          if(client_id != '')
          {
             $.ajax({
                url: "<?php echo base_url();?>multi_stripe_checkout/stripe_checkout_module/get_company_stripe_connect",
                type: "GET",
                data : {'client_id':client_id},
                success: function(response)
                {
                  var res = JSON.parse(response);
                  if(res.success == 'true')
                  {
                    var html = '<div style="margin:10px 0 10px 0;"><div class="form-group"><label class="control-label">Related To: </label><input type="text" id="related_to" name="related_to" class="form-control" value="'+res.html+'" readonly></div></div>'
                    $('.tagit').after(html);
                    $('input[name="custom_fields[invoice][63]"]').val(res.stripe_id);
                  }
                  else 
                  {
                    // alert('Please Stripe Connect for the company');
                    // window.location.href = "<?=base_url()?>admin/clients/client/"+client_id+"?a=stripe_connect";
                  }
                }
              });
          }
        }
       </script>
    <?php
}
?>
