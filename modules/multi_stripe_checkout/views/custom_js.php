<?php defined('BASEPATH') or exit('No direct script access allowed');
$CI = &get_instance();
?>
<?php
 if($CI->uri->segment(1) == 'invoice')
 {
    $invoice_id = $CI->uri->segment('2');
    $invoice = $CI->db->query('select * from '.db_prefix().'invoices where id="'.$invoice_id.'"')->row();
    if($invoice->type !== 'product')
    {
        if(is_client_logged_in())
        {
            $contact_id = get_contact_user_id();
        }
        else
        {
            $CI = &get_instance();
            $contact1 = $CI->db->query('select * from '.db_prefix().'contacts where userid = '.$invoice->clientid.' and is_primary = 1')->row();
            if ($contact1->email) 
            {
                $email_address = $contact1->email;
            }
            $contact_id = $contact1->id;
        }
    ?>
    <script>
       $('document').ready(function()
       {
         if($('#online_payment_form').length > 0)
         {
           var params = '<?php echo $CI->uri->uri_string();?>';
           var param  = params.split('/');
           if(param[0] == 'invoice')
           {
             var invoice_id = param[1];
           }
           else 
           {
             var invoice_id = '';
           }
           var loader_html = '<div class="loading" style="display:none;">Loading&#8230;</div>';
        //   $('#wrapper').before(loader_html);
        //   $.ajax({
        //      url: "<?php echo base_url();?>multi_stripe_checkout/stripe_checkout_module/subscription_html",
        //      type: "GET",
        //      data : {'invoice_id':invoice_id},
        //      beforeSend: function() {
        //       $('.loading').show();
        //      },
        //      success: function(response)
        //      {
        //       var res = JSON.parse(response);
        //       if(res.html !== 'no')
        //       {
        //         //  $('.online-payment-radio input').attr('disabled','true');
        //          $("#online_payment_form").find('input[name="csrf_token_name"]').after(res.html);
        //          $('#subscription').selectpicker();
        //         //  appValidateForm($('#online_payment_form'), {
        //         //      subscription: 'required',
        //         //  });
        //       }
        //       $('.loading').hide();
        //      }
        //   });

        //     $('#online_payment_form').on('submit',function(event){
        //       var stripe = 0;
        //       var payment = $('input[name="paymentmode"]');
        //       for(i=0; i < payment.length; i++)
        //       {
        //           var tag = payment[i].value;
        //           if(tag.includes("stripe") == true && payment[i].checked == true)
        //           {
        //             stripe = 1;
        //             event.preventDefault();
        //           }
        //       }
        //         if(stripe == 1)
        //           {
        //               $.ajax({
        //                 url: "<?php echo base_url();?>multi_stripe_checkout/stripe_checkout_module/card_details_exist",
        //                 type: "GET",
        //                 data : {'contact_id':<?php echo $contact_id;?>},
        //                 success: function(response)
        //                 {
        //                   var res = JSON.parse(response);
        //                   if(res.success == 'true')
        //                   {
        //                     $('#pay_now').removeAttr('disabled');
        //                     $('#pay_now').removeAttr('name');
        //                     $('#pay_now').after('<input type="hidden" name="make_payment" value="1"/>');
        //                     $('#online_payment_form').unbind('submit').submit();
        //                   }
        //                   else {
        //                     $('#pay_now').before('<p style="color:red">'+res.html+'</p>');
        //                     $('#pay_now').attr('disabled','true');
        //                   }
        //                 }
        //               }); 
        //           }
        //   });
         }
      });
      </script>
     
        <?php } ?>
         <script>
       function subscription_change(select)
       {
         var sub = $(select).val();
         $.ajax({
           url: "<?php echo base_url();?>multi_stripe_checkout/stripe_checkout_module/subscription_data",
           type: "GET",
           data : {'sub_id':sub},
           success: function(response)
           {
             var res = JSON.parse(response);
             if(res.sub_type !== '' && res.sub_type == 'gpm')
             {
               var type = 'pm_stripeaccount2';
             }
             if(res.sub_type !== '' && res.sub_type == 'gpw')
             {
               var type = 'pm_stripeaccount1';
             }
             $('.online-payment-radio #'+type).removeAttr('disabled');
           }
         });
       }
    </script>
  <?php } ?>
<?php
if($CI->uri->uri_string() == 'clients/profile')
{
   ?>
   <script>
    //   $('document').ready(function()
    //   {
    //     var loader_html = '<div class="loading" style="display:none;">Loading&#8230;</div>';
    //     $('.header').before(loader_html);
    //     $.ajax({
    //       url: "<?php echo base_url();?>multi_stripe_checkout/stripe_checkout_module/card_detail_html",
    //       type: "POST",
    //       datatype: 'html',
    //       data : {'<?php echo $CI->security->get_csrf_token_name();?>':'<?php echo $CI->security->get_csrf_hash();?>'},
    //       beforeSend: function() {
    //         $('.loading').show();
    //       },
    //       success: function(html)
    //       {
    //         $(".section-profile").append(html);
    //         $('.loading').hide();
    //       }
    //     });
    //   });
   </script>
 <?php } ?>
 
 <?php
if($CI->uri->uri_string() == 'products/client/place_order')
{
   ?>
   <script>
      $('document').ready(function()
      {
            var form_url = $('body').find('form');
            var form1 = form_url[0];
            var form_action = $(form1).attr('action');
            if(form_action.indexOf("client/place_order") > -1)
            {
              $(form1).attr('action',"<?php echo base_url();?>multi_stripe_checkout/stripe_checkout_module/place_order");
            }
      });
   </script>
 <?php } ?>
