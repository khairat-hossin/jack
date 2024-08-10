<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Stripe Credit Cards UPDATE
 */
 $contact_id = get_contact_user_id();
 $CI = &get_instance();
 $sql = 'select * from '.db_prefix().'contact_card_details where contact_id = "'.$contact_id.'"';
 $card_details = $CI->db->query($sql)->row();
$disabled = '';
if(is_staff_logged_in())
{
    $disabled = 'disabled';
}
?>

<h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 section-text section-heading-credit-card">
    <?php echo _l('update_credit_card'); ?>
</h4>

<div class="panel_s">
    <div class="panel-body credit-card">
        <?php 
        if (!empty($payment_method)) 
        {
            $card_detail = $payment_method->card;
               ?>
               <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open('stripe_checkout/stripe_checkout_module/save_credit_card_details'); ?>
                        <?php echo form_hidden('contact_id', get_contact_user_id()); ?>
                        <div class="form-group">
                            <label for="card_number"><?php echo _l('card_number'); ?></label>
                            <input type="tel" class="form-control" name="card_number" id="card_number" value="***********<?php echo $card_detail->last4;?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="expire_month"><?php echo _l('Expiry Month'); ?></label>
                            <input type="text" class="form-control" name="expire_month" id="expire_month" value="<?php echo $card_detail->exp_month;?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="expire_year"><?php echo _l('Expiry Year'); ?></label>
                            <?php $value = !empty($card_details->expire_year)?$card_details->expire_year:''; ?>
                            <input type="text" class="form-control" name="expire_year" id="expire_year" value="<?php echo $card_detail->exp_year;?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="cvv"><?php echo _l('cvv'); ?></label>
                            <?php $value = !empty($card_details->cvv)?$card_details->cvv:''; ?>
                            <input type="password" class="form-control" name="cvv" id="cvv" value="***" required maxlength="4" disabled>
                        </div>
                        <div class="form-group">
                            <label for="card_name"><?php echo _l('card_name'); ?></label>
                            <?php $value = !empty($card_details->card_name)?$card_details->card_name:''; ?>
                            <input type="text" class="form-control" name="card_name" value="<?php echo $payment_method->billing_details->name;?>" id="card_name" disabled>
                        </div>
                        <div class="form-group">
                             
                        <a href="<?php echo site_url('clients/update_credit_card'); ?>" class="btn btn-primary" >
                            <?php echo _l('update_card_btn'); ?> (<?php echo $payment_method->card->brand; ?>
                            <?php echo $payment_method->card->last4; ?>
                        </a>
                
                        <div<?php if (!customer_can_delete_credit_card()) { ?> data-toggle="tooltip"
                                title="<?php echo _l('delete_credit_card_info'); ?>" <?php } ?> class="inline-block">
                                <a class="btn btn-danger<?php if (!customer_can_delete_credit_card()) { ?> disabled<?php } ?>"
                                    href="<?php echo site_url('clients/delete_credit_card'); ?>" >
                                    <?php echo _l('delete_credit_card'); ?>
                                </a>
                        </div>
                        </div>
                    </div>
                </div>
               <?php
        }
        else 
        { 

        ?>
    <div class="col-md-12 contact-profile-change-password-section">
    <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 section-text">
        <?php echo _l('credit_card_details'); ?>
    </h4>
    <div class="panel_s">
        <div class="panel-body">
            <?php echo form_open('stripe_checkout/stripe_checkout_module/save_credit_card_details'); ?>
            <?php echo form_hidden('contact_id', get_contact_user_id()); ?>
            <div class="form-group">
                <label for="card_number_dup"><?php echo _l('card_number'); ?></label>
                <?php $value = !empty($card_details->card_number)?convert_uudecode($card_details->card_number):''; ?>
                <input type="hidden" name="card_number" id="card_number" value="<?php echo $value;?>" />
                <input type="tel" class="form-control" name="card_number_dup" id="card_number_dup" value="<?php echo $value;?>" onkeypress="return isNumberKey(event)" required maxlength="16" >
            </div>
            <div class="form-group">
                <label for="expire_month"><?php echo _l('Expiry Month'); ?></label>
                <?php $value = !empty($card_details->expire_month)?convert_uudecode($card_details->expire_month):''; ?>
                <input type="text" class="form-control" name="expire_month" id="expire_month" value="<?php echo $value;?>" onkeypress="return isNumberKey(event)" required maxlength="2" >
            </div>
            <div class="form-group">
                <label for="expire_year"><?php echo _l('Expiry Year'); ?></label>
                <?php $value = !empty($card_details->expire_year)?convert_uudecode($card_details->expire_year):''; ?>
                <input type="text" class="form-control" name="expire_year" id="expire_year" value="<?php echo $value;?>" onkeypress="return isNumberKey(event)" required maxlength="2" >
            </div>
            <div class="form-group">
                <label for="cvv"><?php echo _l('cvv'); ?></label>
                <?php $value = !empty($card_details->cvv)?convert_uudecode($card_details->cvv):''; ?>
                <input type="password" class="form-control" name="cvv" id="cvv" value="<?php echo $value;?>" onkeypress="return isNumberKey(event)" required maxlength="4" >
            </div>
            <div class="form-group">
                <label for="card_name"><?php echo _l('card_name'); ?></label>
                <?php $value = !empty($card_details->card_name)?convert_uudecode($card_details->card_name):''; ?>
                <input type="text" class="form-control" name="card_name" value="<?php echo $value;?>" id="card_name" required >
            </div>
            <div class="form-group">
                <button type="submit"
                    class="btn btn-primary btn-block" ><?php echo _l('save'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
    <?php } ?>
</div>
</div>
<script>
$(document).ready(function() {
    var detail = $('#card_number_dup').val();
    if(detail != '')
    {
        $('#card_number_dup').val(maskCardNumber(detail));
    }
})
$('#card_number_dup').on('blur',function()
{
    var detail = $('#card_number_dup').val();
    if(detail != '')
    {
        $('#card_number').val(detail);
        $('#card_number_dup').val(maskCardNumber(detail));
    }
})
$('#card_number_dup').on('focus',function()
{
    var detail = $('#card_number').val();
    if(detail != '')
    {
        $('#card_number_dup').val(detail);
    }
})
function maskCardNumber(cardNumber) {
    return cardNumber.slice(-3).padStart(cardNumber.length, '*')
}
function isNumberKey(evt) {
  var charCode = (evt.which) ? evt.which : evt.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57))
    return false;
  return true;
}
</script>
