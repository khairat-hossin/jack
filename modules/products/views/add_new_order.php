<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin">
              <?php echo $title; ?>
            </h4>
            <hr class="hr-panel-heading" />
            <?php echo form_open($this->uri->uri_string()); ?>
            <div class="row">
              <div class="col-md-7">
                <div class="form-group select-placeholder">
                  <label for="clientid" class="control-label"><?php echo _l('invoice_select_customer'); ?></label>
                  <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search<?php if (isset($products) && empty($invoice->clientid)) { echo ' customer-removed'; } ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                    <?php
                      $selected = (isset($invoice) ? $invoice->clientid : '');
                      if ('' == $selected) {
                        $selected = ($customer_id ?? '');
                      }
                      if ('' != $selected) {
                        $rel_data = get_relation_data('customer', $selected);
                        $rel_val  = get_relation_values($rel_data, 'customer');
                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                      }
                    ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="product_row" id="product_row_0">
              <div class="row">
                <div class="col-md-7 col-sm-7 product-column">
                  <?php echo render_select('product_items[0][product_id]', $products, ['id', 'product_name', 'quantity_number'], 'Product', !empty(set_value('product_id')) ? set_value('product_id') : $products['product_name'] ?? '', ['required' => true, 'data-header' => 'Select a Product'], [], '', 'show-tick product_id'); ?>
                </div>
                <div class="col-md-2 col-sm-2 product-variation-column hide">
                  <div class="form-group">
                    <label for="" class="control-label"><small class="req"><span class="text-danger"> * </span></small>Variation</label>
                    <div class="dropdown bootstrap-select show-tick variation_id bs3 dropup" style="width: 100%;">
                      <select name="" class="selectpicker show-tick variation_id" data-header="Select a Variation" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true">
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-2 col-sm-2 product-variation-value-column hide">
                  <div class="form-group">
                    <label for="" class="control-label"><small class="req"><span class="text-danger"> * </span></small>Variation Value</label>
                    <div class="dropdown bootstrap-select show-tick variation_value_id bs3 dropup" style="width: 100%;">
                      <select name="" class="selectpicker show-tick variation_value_id" data-header="Select a Variation Value" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true">
                      </select>
                    </div>
                  </div>
                  <input type="hidden" name="product_items[0][product_variation_id]" class="product_variation_id" />
                </div>
                <div class="col-md-3 col-sm-3">
                  <?php echo render_input('product_items[0][qty]', 'quantity', '', 'number', ['required' => true], [], '', 'quantity'); ?>
                </div>
                <div class="col-md-2 col-sm-2">
                  <label>&nbsp;</label><br/>
                  <button type="button" class="btn btn-sm btn-success add_row"><i class="fa fa-plus"></i> Add</button>
                </div>
              </div>
            </div>
            <div id="append_product_row"></div>
            <?php if (0 == get_option('coupons_disabled')) { ?>
              <div class="row">
                <div class="col-md-6 col-sm-6">
                  <?php echo render_select('coupon_id', [], ['id', 'code'], 'Coupon', '', ['data-header' => 'Select a Coupon'], [], '', 'show-tick coupon_id'); ?>
                </div>
              </div>
            <?php } ?>
            <div class="row">
              <div class="col-md-12 col-sm-12">
                <button type="submit" class="btn btn-info pull-right save_button"><?php echo _l('submit'); ?></button>
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<?php if(!empty($message)) { ?>
  <script type="text/javascript">
    alert_float('warning', '<?php echo $message; ?>', 6000);
  </script>
<?php } ?>
<script type="text/javascript">
  jQuery(document).ready( function () {
    "use strict";
    var product_row ="";
    init_order_required_label();
    $(".add_row").click( function(event) {
      event.preventDefault();
      var total_element = $(".product_row").length;
      var last_id = $(".product_row:last").attr('id').split("_");
      var next_id = Number(last_id[2]) + 1;
      product_row =`<div class="product_row" id="product_row_${next_id}">
        <div class="row">
        <div class="col-md-7 col-sm-7 product-column">
          <?php echo render_select('product_items[0][product_id]', $products, ['id', 'product_name', 'quantity_number'], 'Product', !empty(set_value('product_id')) ? set_value('product_id') : $products['product_name'] ?? '', ['required'=>true, 'data-header'=>'Select a Product'], [], '', 'show-tick product_id'); ?>
        </div>
        <div class="col-md-2 col-sm-2 product-variation-column hide">
          <div class="form-group">
            <label for="" class="control-label"><small class="req"><span class="text-danger"> * </span></small>Variation</label>
            <div class="dropdown bootstrap-select show-tick variation_id bs3 dropup" style="width: 100%;">
              <select name="" class="selectpicker show-tick variation_id" data-header="Select a Variation" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true">
              </select>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-sm-2 product-variation-value-column hide">
          <div class="form-group">
            <label for="" class="control-label"><small class="req"><span class="text-danger"> * </span></small>Variation Value</label>
            <div class="dropdown bootstrap-select show-tick variation_value_id bs3 dropup" style="width: 100%;">
              <select name="" class="selectpicker show-tick variation_value_id" data-header="Select a Variation Value" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true">
              </select>
            </div>
          </div>
          <input type="hidden" name="product_items[0][product_variation_id]" class="product_variation_id" />
        </div>
        <div class="col-md-3">
          <?php echo render_input('product_items[0][qty]', 'quantity', $products->quantity_number ?? '', 'number', ['required'=>true],[],'','quantity'); ?>
        </div>
        <div class="col-md-2 col-sm-2">
        <label>&nbsp;</label><br/>
        <button type="button" class="btn btn-sm btn-danger" onclick="remove_div(${next_id})"><i class="fa fa-times"></i></button>
        </div>
        </div>
      </div>`;
      $("#append_product_row").append(product_row);
      $(`#product_row_${next_id}`).find('select.product_id').prop('name',`product_items[${next_id}][product_id]`);
      $(`#product_row_${next_id}`).find('input.product_variation_id').prop('name',`product_items[${next_id}][product_variation_id]`);
      $(`#product_row_${next_id}`).find('input.quantity').prop('name',`product_items[${next_id}][qty]`);
      $(`#product_row_${next_id}`).find('input.quantity').on('change', function () {
        if($(this).val() < 1 || !$.isNumeric($(this).val())) {
          alert_float("danger","Quantity Must Be Greater Than 0 ");
          $(this).val(1);
        }
      });
      init_selectpicker();
      init_order_required_label();
      fire_change_variation();
      fire_change_variation_value();
    });

    appValidateForm($('form'), {
      clientid        : "required",
    });

    var product_variations = [];
    $(document).on('change', 'select.product_id', function(event) {
      var select_element = $(this);
      select_element.parents('.product_row').find('.quantity').val("").prop('readonly',false);
      $(".save_button").prop('disabled', true);
      $.post(admin_url+"products/staff_order/get_product_data", {product_id: select_element.val()}, function(data, textStatus, xhr) {
        $(".save_button").prop('disabled', false);
        data = $.parseJSON(data);
        if (data.is_digital != 1) {
          select_element.parents('.product_row').find('.quantity').attr('qty_max', data.quantity_number);
        }
        product_variations = data.variations;
        if (data.variations) {
          select_element.parents('.product_row').find('.product-column').removeClass('col-md-7').removeClass('col-sm-7').addClass('col-md-3').addClass('col-sm-3');
          select_element.parents('.product_row').find('.product-variation-column').removeClass('hide');
          select_element.parents('.product_row').find('.product-variation-value-column').removeClass('hide');
          var product_variation_ids = [];
          var variations_html = '';
          if (data.variations.length) variations_html += '<option value="">' + select_element.parents('.product_row').find('.selectpicker.variation_id').data('none-selected-text') + '</option>';
          for (var variation_index = 0; variation_index < data.variations.length; variation_index++) {
            if (!product_variation_ids.includes(data.variations[variation_index]['variation_id'])) {
              product_variation_ids.push(data.variations[variation_index]['variation_id']);
              variations_html += '<option value="' + data.variations[variation_index]['variation_id'] + '">' + data.variations[variation_index]['variation_name'] + '</option>';
            }
          }
          select_element.parents('.product_row').find('.selectpicker.variation_id').html(variations_html);
          select_element.parents('.product_row').find('.selectpicker.variation_id').selectpicker("refresh");
          select_element.parents('.product_row').find('.product_variation_id').attr("required", true);
        } else {
          select_element.parents('.product_row').find('.product-column').removeClass('col-md-3').removeClass('col-sm-3').addClass('col-md-7').addClass('col-sm-7');
          select_element.parents('.product_row').find('.product-variation-column').addClass('hide');
          select_element.parents('.product_row').find('.product-variation-value-column').addClass('hide');
          select_element.parents('.product_row').find('.product_variation_id').attr("required", false);
        }
        fire_change_variation();
      });
    });
    function fire_change_variation() {
      $(document).on('change', 'select.variation_id', function(event) {
        if (product_variations) {
          var variation_values_html = '';
          if (product_variations.length) variation_values_html += '<option value="">' + $(this).parents('.product_row').find('.selectpicker.variation_value_id').data('none-selected-text') + '</option>';
          for (var variation_index = 0; variation_index < product_variations.length; variation_index++) {
            if (product_variations[variation_index]['variation_id'] == $(this).parents('.product_row').find('.selectpicker.variation_id').val()) {
              variation_values_html += '<option value="' + product_variations[variation_index]['id'] + '">' + product_variations[variation_index]['variation_value'] + '</option>';
            }
          }
        }
        $(this).parents('.product_row').find('.selectpicker.variation_value_id').html(variation_values_html);
        $(this).parents('.product_row').find('.selectpicker.variation_value_id').selectpicker("refresh");
        fire_change_variation_value();
      });
    }
    function fire_change_variation_value() {
      $(document).on('change', 'select.variation_value_id', function(event) {
        $(this).parents('.product_row').find('.product_variation_id').val($(this).val());
      });
    }
  });

  function init_order_required_label() {
    $(':input[required]').each(function(index, el) {
      label = $(this).parents('.form-group').find('label');
      if (label.length > 0 && label.find('.req').length === 0) {
        label.prepend('<small class="req"><span class=text-danger> * </<span></small>');
      }
    });
  }

  $(document).on('change', 'input[type="number"]' , function () {
    var max = $(this).attr('qty_max');
    var quantity = $(this).val();
    $(".save_button").prop('disabled', false);
    if (quantity <= 0 || !$.isNumeric(quantity)) {
      $(".save_button").prop('disabled', true);
      alert_float("danger","Quantity Must Be Greater Than 0 ");
      return false;
    }
    if (parseInt(quantity) > parseInt(max)) {
      $(".save_button").prop('disabled', true);
      alert_float("danger",`Only ${max} Items are in stock for this Product`);
      return false;
    }
  });

  <?php if (0 == get_option('coupons_disabled')) { ?>
    $(document).on('change', 'select[name="clientid"]' , function () {
      $.post(admin_url+"products/staff_order/get_available_coupons", {client_id: $(this).val()}, function(data, textStatus, xhr) {
        data = $.parseJSON(data);
        var coupons_html = '';
        if (data.length) coupons_html += '<option value="">' + $('.selectpicker.coupon_id').data('none-selected-text') + '</option>';
        for (var coupon_index = 0; coupon_index < data.length; coupon_index++) {
          coupons_html += '<option value="' + data[coupon_index]['id'] + '">' + data[coupon_index]['code'] + '</option>';
        }
        $('.selectpicker.coupon_id').html(coupons_html);
        $('.selectpicker.coupon_id').selectpicker("refresh");
      });
    });
  <?php } ?>

  function remove_div(id) {
    $('#product_row_'+id).remove();
  }
</script>