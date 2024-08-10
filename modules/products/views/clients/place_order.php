<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s section-heading section-products">
    <div class="panel-body">
        <h4 class="no-margin section-text"><?php echo _l('place_order'); ?></h4>
    </div>
</div>
<?php if (!empty($message)) { ?>
    <!-- Removed HTML entities because in message we are sending HTML format -->
    <div class="alert alert-danger"><?php echo $message; ?></div>
<?php } ?>
<?php echo form_open(uri_string()); ?>
<?php echo form_hidden('clientid', $this->session->client_user_id); ?>
<?php $total = 0; ?>
<div class="panel_s">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
               <div class="table-responsive">
                  <table class="table items items-preview invoice-items-preview" data-type="invoice">
                     <thead>
                        <tr>
                           <th align="center">#</th>
                           <th class="description" width="50%" align="left"><?php echo _l('item') ?></th>
                           <th align=""><?php echo _l('qty') ?></th>
                           <th align="right"><?php echo _l('rate') ?></th>
                           <th align="right"><?php echo _l('tax') ?></th>
                           <th align="right"></th>
                           <th align="right"><?php echo _l('amount') ?></th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach ($products as $key => $product) { ?>
                            <?php if (!empty($product->quantity)) { ?>
                                <tr nobr="true">
                                    <td align="center">
                                        <?php echo form_hidden('product_items['.$key.'][product_id]', $product->id); ?>
                                        <?php echo $key + 1; ?>
                                    </td>
                                    <td class="description" align="left;">
                                        <span>
                                            <strong><?php echo htmlspecialchars($product->product_name); ?></strong>
                                            <?php if (isset($product->product_variation_id)) { ?>
                                                <?php echo form_hidden('product_items['.$key.'][product_variation_id]', $product->product_variation_id); ?>
                                                (<?php echo htmlspecialchars($product->variation_name); ?> <?php echo htmlspecialchars($product->variation_value); ?>)
                                            <?php } ?>
                                        </span>
                                        <br>
                                        <span><?php echo htmlspecialchars($product->product_description); ?></span>
                                    </td>
                                    <td align="" width="10%">
                                        <?php
                                            $qty_attr = [
                                                'data-rate' => $product->rate,
                                                'min' => 0,
                                                'data-old-qty' => $product->quantity,
                                                'data-product_id' => $product->id,
                                                'data-product_variation_id' => $product->product_variation_id ?? '',
                                                'max' => $product->quantity_number,
                                                'data-apply_shipping'=> $product->apply_shipping
                                            ];
                                            if ($product->is_digital) {
                                                unset($qty_attr['max']);
                                            }
                                        ?>
                                        <?php echo render_input('product_items['.$key.'][qty]', '', $product->quantity, 'number', $qty_attr, [], '', 'quantity'); ?>
                                        <?php echo render_select('taxes['.$key.'][]', $product->taxes, ['name', 'name'], '', array_column($product->taxes, 'name'), ['multiple'=>true], [], 'hidden', 'hidden product_taxes'); ?>
                                    </td>
                                    <td align="right">
                                        <?php
                                            $recurring_type = '';
                                            $cycles_text    = '';
                                            if (0 != $product->recurring) {
                                                $recurring_type = $product->recurring_type;
                                                if ('' == $product->recurring_type) {
                                                    $recurring_type = 'month';
                                                }
                                                if (0 == $product->cycles) {
                                                    $cycles_text = 'Infinite recurring';
                                                }
                                                if (1 == $product->cycles) {
                                                    $cycles_text = '1 time totally';
                                                }
                                                if ($product->cycles > 1) {
                                                    $cycles_text = $product->cycles . ' times totally';
                                                }
                                                $recurring_type = ' / '. ((1 != $product->recurring) ? $product->recurring : '') . ' ' . $recurring_type;
                                            }
                                            echo htmlspecialchars($product->rate.$recurring_type);
                                            echo '<br>';
                                            echo htmlspecialchars($cycles_text);
                                        ?>
                                    </td>
                                    <td align="right">
                                        <?php
                                            foreach ($product->taxes as $tax) {
                                                echo htmlspecialchars($tax['taxname'] . '(' . $tax['taxrate'] . '%)') . '<br>';
                                            }
                                        ?>
                                    </td>
                                    <td align="right">
                                        <button type="button" class="btn btn-sm btn-danger remove_cart" data-product_id="<?php echo htmlspecialchars($product->id); ?>" data-product_variation_id="<?php echo htmlspecialchars($product->product_variation_id ?? ''); ?>" data-toggle="tooltip"  data-placement="right" title="Remove">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                    <td class="amount" align="right"><?php $total += $product->quantity * $product->rate; echo app_format_money($product->quantity * $product->rate, $base_currency->name); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                     </tbody>
                  </table>
               </div>
            </div>
        </div>
        <?php if (0 == get_option('coupons_disabled')) { ?>
            <div class="row">
                <div class="col-md-6 col-md-offset-6">
                    <table class="table">
                        <tbody>
                            <tr id="subtotal">
                            <td>
                                <span class="bold" style="position: relative; top: 8px;"><?php echo _l('invoice_add_edit_allowed_payment_modes'); ?></span>
                            </td>
                            <td class="subtotal">
                            <?php if (count($payment_modes) > 0) { ?>
                        <select class="selectpicker"
                            data-toggle="<?php echo $this->input->get('allowed_payment_modes'); ?>"
                            name="allowed_payment_modes[]" data-actions-box="true" multiple="true" data-width="100%"
                            data-title="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php foreach ($payment_modes as $mode) {
                   $selected = '';
                   if (isset($product)) {
                    if ($product->allowed_payment_modes) {
                        $inv_modes = unserialize($product->allowed_payment_modes);
                        if (is_array($inv_modes)) {
                            foreach ($inv_modes as $_allowed_payment_mode) {
                                if ($_allowed_payment_mode == $mode['id']) {
                                    $selected = ' selected';
                                }
                            }
                        }
                    }
                } else {
                    if ($mode['selected_by_default'] == 1) {
                        $selected = ' selected';
                    }
                }
                if($selected == ''){
                    continue;
                }
                   ?>
                            <option value="<?php echo $mode['id']; ?>" <?php echo $selected; ?>>
                                <?php echo $mode['name']; ?></option>
                            <?php
               } ?>
                        </select>
                        <?php } ?>
                            </td>
                        </tr>
                            <tr>
                                <td>
                                    <span class="bold"><?php echo _l('coupon_code_label');?></span>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" id="coupon_code" class="form-control" value="" />
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success pull-right apply_coupon"><i class="fa fa-check"></i> <?php echo _l('apply_coupon'); ?></button>
                                    <button type="button" class="btn btn-danger pull-right remove_coupon hide"><i class="fa fa-check"></i> <?php echo _l('remove_coupon'); ?></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-md-6 col-md-offset-6">
                <table class="table text-right">
                    <tbody>
                        <tr id="subtotal">
                            <td>
                                <span class="bold"><?php echo _l('subtotal');?></span>
                            </td>
                            <td class="subtotal">
                                <?php echo app_format_money($total, $base_currency->name); ?>
                            </td>
                        </tr>
                        <tr id="coupon_discount" class="hide">
                            <td>
                                <span class="bold"><?php echo _l('coupon_discount');?></span>
                                <?php echo form_hidden('coupon_id', ''); ?>
                            </td>
                            <td class="coupon_discount">
                            </td>
                        </tr>
                        <?php foreach ($all_taxes as $tax) { ?>
                            <tr class="tax-area">
                                <td>
                                    <span class="bold"><?php echo htmlspecialchars($tax['taxname'] . '(' . $tax['taxrate'].'%)'); ?></span>
                                </td>
                                <td>
                                    <?php $total += array_sum($init_tax[$tax['name']]); echo app_format_money(array_sum($init_tax[$tax['name']]), $base_currency->name); ?>    
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($shipping_cost)) { ?>
                            <tr id="shipping_costs">
                                <td>
                                    <span class="bold">
                                        <?php 
                                            echo _l('shipping_costs');
                                            echo "(" . app_format_money($shipping_base, $base_currency->name) . "+" . $shipping_tax . "%)";
                                        ?>
                                    </span>
                                </td>
                                <td class="shipping_costs">
                                    <?php echo form_hidden('shipping_cost', $shipping_cost); ?>
                                    <?php echo app_format_money($shipping_cost, $base_currency->name); ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td><span class="text-danger bold"><?php echo _l('payable_amount');?></span></td>
                            <td>
                                <span class="text-danger total"><?php echo app_format_money($total + $shipping_cost, $base_currency->name); ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="panel_s section-heading section-products">
    <div class="panel-body">
        <button class="btn btn-success pull-right"><i class="fa fa-check"></i> <?php echo _l('place_order'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript" src="<?php echo base_url('assets/plugins/accounting.js/accounting.js'); ?>"></script>
<script type="text/javascript">
    "use strict";
    var base_currency = <?php echo htmlspecialchars($base_currency->id); ?>
</script>
<?php if (!empty($message)) { ?>
    <script type="text/javascript">
        $(function() {
            alert_float('warning','<?php echo $message; ?>',6000);
        });
    </script>
<?php } ?>
<script type="text/javascript" src="<?php echo module_dir_url('products', 'assets/js/place_order.js'); ?>"></script>