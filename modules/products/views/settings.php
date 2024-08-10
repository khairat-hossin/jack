<div class="col-md-12">
	<p class="bold"><?php echo _l('products_module_settings'); ?></p>
	<hr />
</div>
<div class="col-md-6">
	<?php render_yes_no_option('product_menu_disabled', 'product_menu_disabled', 'product_menu_disabled_tooltip'); ?>
</div>
<div class="col-md-6">
	<?php render_yes_no_option('nlu_product_menu_disabled', 'nlu_product_menu_disabled', 'nlu_product_menu_disabled_tooltip'); ?>
</div>
<div class="col-md-6">
	<?php render_yes_no_option('nlu_hiddenprices_disabled', 'nlu_hiddenprices_disabled', 'nlu_hiddenprices_disabled_tooltip'); ?>
</div>
<div class="col-md-6">
	<?php render_yes_no_option('b2bmode_disabled', 'b2bmode_disabled', 'b2bmode_disabled_tooltip'); ?>
</div>
<div class="col-md-12">
	<hr />
</div>
<div class="col-md-4">
	<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('product_low_quantity_tooltip'); ?>"></i>
	<?php echo render_input('settings[product_low_quantity]', _l('low_qty'), get_option('product_low_quantity'), 'number', ['required'=>true,'min'=>0]); ?>
</div>
<div class="col-md-4">
	<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('flat_shipping_tooltip'); ?>"></i>
	<?php echo render_input('settings[product_flat_rate_shipping]', _l('flat_shipping'), get_option('product_flat_rate_shipping'), 'number', ['required'=>true,'min'=>0]); ?>
</div>
<div class="col-md-4">
	<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('tax_for_shipping_cost_tooltip'); ?>"></i>
	<div class="form-group" app-field-wrapper="settings[product_tax_for_shipping_cost]">
		<label for="settings[product_tax_for_shipping_cost]" class="control-label"><?php echo _l('tax_for_shipping_cost') ?></label>
		<?php
			$selected_taxes ='';
			if (!empty(get_option('product_tax_for_shipping_cost'))) {
				$selected_taxes = (!empty((get_option('product_tax_for_shipping_cost')))) ? unserialize(get_option('product_tax_for_shipping_cost')) : '';
			}
      		echo $this->misc_model->get_taxes_dropdown_template('settings[product_tax_for_shipping_cost][]', $selected_taxes);
      	?>
	</div>
</div>
<div class="col-md-6">
	<?php render_yes_no_option('coupons_disabled', 'coupons_disabled', 'coupons_disabled_tooltip'); ?>
</div>