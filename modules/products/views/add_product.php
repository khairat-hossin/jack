<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin">
              <?php echo htmlspecialchars($title);?>
            </h4>
            <hr class="hr-panel-heading" />
            <?php echo form_open_multipart($this->uri->uri_string()); ?>
            <div class="row">
              <div class="col-md-5">
                <?php echo render_select('product_category_id', $product_categories, ['p_category_id', 'p_category_name'], 'products_categories', !empty(set_value('product_category_id')) ? set_value('product_category_id') : $product->product_category_id ?? ''); ?>
              </div>
              <div class="col-md-7">
                <?php echo render_input('product_name', 'product_name', $product->product_name ?? ''); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <?php echo render_textarea('product_description', 'product_description', $product->product_description ?? ''); ?>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-12">
                  <div class="form-group select-placeholder">
                        <label for="clientid" class="control-label"><?php echo _l('invoice_add_edit_allowed_payment_modes'); ?></label>
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
                   ?>
                            <option value="<?php echo $mode['id']; ?>" <?php echo $selected; ?>>
                                <?php echo $mode['name']; ?></option>
                            <?php
               } ?>
                        </select>
                        <?php } ?>
                    </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-3">
                <?php echo render_input('rate', _l('invoice_item_add_edit_rate_currency'), $product->rate ?? '', 'number',['min'=>"0.00"]); ?>
              </div>
              <div class="col-md-3">
                <label>Tax</label>
                <?php
                  $selected_taxes ='';
                  if (!empty($product->taxes)) {
                    $selected_taxes = (!empty(($product->taxes))) ? unserialize($product->taxes) : '';
                  }
                  echo $this->misc_model->get_taxes_dropdown_template('taxes[]', $selected_taxes);
                ?>
              </div>
              <div class="col-md-2">
                <?php echo render_input('quantity_number', 'quantity', $product->quantity_number ?? '', 'number'); ?>
              </div>
              <div class="col-md-4">
                <label for="is_digital"><?php echo _l('no_qty_digital_product'); ?></label>
                <div class="checkbox checkbox-danger">
                  <input type="checkbox" name="is_digital" id="is_digital" value="<?php echo isset($product) ? $product->is_digital : "" ?>"  <?php echo isset($product) ? ($product->is_digital == '1') ? "checked" : "" : "" ?> >
                  <label></label>
                </div>
              </div>
            </div>
            <?php
              $existing_image_class = 'col-md-4';
              $input_file_class     = 'col-md-8';
              if (empty($product->product_image)) {
                $existing_image_class = 'col-md-12';
                $input_file_class     = 'col-md-12';
              }
            ?>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group select-placeholder"<?php if (isset($product) && !empty($product->is_recurring_from)) { ?> data-toggle="tooltip" data-title="<?php echo _l('create_recurring_from_child_error_message', [_l('invoice_lowercase'), _l('invoice_lowercase'), _l('invoice_lowercase')]); ?>"<?php } ?>>
                  <label for="recurring" class="control-label">
                    <?php echo _l('invoice_add_edit_recurring'); ?>
                  </label>
                  <select class="selectpicker"
                    data-width="100%"
                    name="recurring"
                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                    <?php
                      if (isset($product) && !empty($product->is_recurring_from)) {
                        echo 'disabled';
                      } ?>
                    >
                    <?php for ($i = 0; $i <= 12; ++$i) { ?>
                      <?php
                        $selected = '';
                        if (isset($product)) {
                          if (0 == $product->custom_recurring) {
                            if ($product->recurring == $i) {
                              $selected = 'selected';
                            }
                          }
                        }
                        if (0 == $i) {
                          $reccuring_string =  _l('invoice_add_edit_recurring_no');
                        } elseif (1 == $i) {
                          $reccuring_string = _l('invoice_add_edit_recurring_month', $i);
                        } else {
                          $reccuring_string = _l('invoice_add_edit_recurring_months', $i);
                        }
                      ?>
                      <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $reccuring_string; ?></option>
                    <?php } ?>
                    <option value="custom" <?php if (isset($product) && 0 != $product->recurring && 1 == $product->custom_recurring) { echo 'selected'; } ?>><?php echo _l('recurring_custom'); ?></option>
                  </select>
                </div>
              </div>
              <div class="recurring_custom <?php if ((isset($product) && 1 != $product->custom_recurring) || (!isset($product))) { echo 'hide'; } ?>">
                <div class="col-md-2">
                  <?php $value = (isset($product) && 1 == $product->custom_recurring ? $product->recurring : 1); ?>
                  <?php echo render_input('repeat_every_custom', 'Number', $value, 'number', ['min'=>1]); ?>
                </div>
                <div class="col-md-5">
                  <label>Select</label>
                  <select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                    <option value="day" <?php if (isset($product) && 1 == $product->custom_recurring && 'day' == $product->recurring_type) { echo 'selected'; } ?>><?php echo _l('invoice_recurring_days'); ?></option>
                    <option value="week" <?php if (isset($product) && 1 == $product->custom_recurring && 'week' == $product->recurring_type) { echo 'selected'; } ?>><?php echo _l('invoice_recurring_weeks'); ?></option>
                    <option value="month" <?php if (isset($product) && 1 == $product->custom_recurring && 'month' == $product->recurring_type) { echo 'selected'; } ?>><?php echo _l('invoice_recurring_months'); ?></option>
                    <option value="year" <?php if (isset($product) && 1 == $product->custom_recurring && 'year' == $product->recurring_type) { echo 'selected'; } ?>><?php echo _l('invoice_recurring_years'); ?></option>
                  </select>
                </div>
              </div>
              <div id="cycles_wrapper" class="<?php if (!isset($product) || (isset($product) && 0 == $product->recurring)) { echo ' hide'; }?>">
                <div class="col-md-12">
                  <?php $value = (isset($product) ? $product->cycles : 0); ?>
                  <div class="form-group recurring-cycles">
                    <label for="cycles"><?php echo _l('recurring_total_cycles'); ?></label>
                    <div class="input-group">
                      <input type="number" class="form-control"<?php if (0 == $value) { echo ' disabled'; } ?> name="cycles" id="cycles" value="<?php echo $value; ?>" <?php if (isset($product) && $product->cycles > 0) { echo 'min="'.($product->cycles).'"'; } ?>>
                      <div class="input-group-addon">
                        <div class="checkbox">
                          <input type="checkbox"<?php if (0 == $value) { echo ' checked'; } ?> id="unlimited_cycles">
                          <label for="unlimited_cycles"><?php echo _l('cycles_infinity'); ?></label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <?php if (!empty($product->product_image)) { ?>
                <div class="<?php echo htmlspecialchars($existing_image_class); ?>">
                  <div class="existing_image">
                    <label class="control-label">Existing Image</label>
                    <img src="<?php echo base_url('modules/'.PRODUCTS_MODULE.'/uploads/'.$product->product_image); ?>" class="img img-responsive img-thubnail zoom"/>
                  </div>
                </div>
              <?php } ?>
              <div class="<?php echo htmlspecialchars($input_file_class); ?>">
                <div class="attachment">
                  <div class="form-group">
                    <label for="attachment" class="control-label"><small class="req text-danger">* </small><?php echo _l('product_image'); ?></label>
                    <input type="file" extension="png,jpg,jpeg,gif" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="product" id="product" required>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group">
                  <label for="is_variation" class="control-label">
                    <?php echo _l('product_add_edit_variation'); ?>
                  </label>
                  <select class="selectpicker"
                    data-width="100%"
                    name="is_variation"
                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                    >
                    <option value="0" <?php if (isset($product) && !empty($product->is_variation == 0)) echo 'selected'; ?>><?php echo _l('product_add_edit_variation_no'); ?></option>
                    <option value="1" <?php if (isset($product) && !empty($product->is_variation == 1)) echo 'selected'; ?>><?php echo _l('product_add_edit_variation_yes'); ?></option>
                  </select>
                </div>
              </div>
              <div id="variations_wrapper" class="<?php if (!isset($product) || (isset($product) && 0 == $product->is_variation)) { echo ' hide'; }?>">
                <div class="col-md-12">
                  <div class="table-responsive s_table">
                    <table class="table product-variations-table items table-main-product-variation-edit has-calculations no-mtop">
                      <thead>
                        <tr>
                          <th>
                            <?php echo _l('product_variation_table_heading'); ?>
                          </th>
                          <th><?php echo _l('product_variation_table_value'); ?></th>
                          <th><?php echo _l('product_variation_table_price'); ?></th>
                          <th><?php echo _l('product_variation_table_quantity'); ?></th>
                          <th align="center"><i class="fa fa-cog"></i></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="main">
                          <td>
                            <select class="selectpicker variation"
                              data-width="100%"
                              data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                              >
                              <?php foreach ($variations as $variation_index => $variation) { ?>
                                <option value="<?php echo $variation['id']; ?>"><?php echo $variation['name']; ?></option>
                              <?php } ?>
                            </select>
                          </td>
                          <td>
                            <select class="selectpicker variation_value"
                              data-width="100%"
                              data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                              <?php
                                if (isset($product) && !empty($product->is_recurring_from)) {
                                  echo 'disabled';
                                } ?>
                              >
                            </select>
                          </td>
                          <td></td>
                          <td>
                            <button type="button" onclick="add_variation_value_to_table(); return false;" class="btn pull-right btn-primary"><i class="fa fa-check"></i></button>
                          </td>
                        </tr>
                        <?php if (isset($product) && !empty($product->variations)) { ?>
                          <?php
                            $product_variation_id = '';
                            foreach ($product->variations as $product_variation) {
                              if ($product_variation->variation_id != $product_variation_id) {
                                $product_variation_id = $product_variation->variation_id; ?>
                                <tr class="variation">
                                  <td><input class="form-control" value="<?php echo $product_variation->variation_name ?>" data-id="<?php echo $product_variation->variation_id ?>" readonly /></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td><a href="#" class="btn btn-danger pull-right" onclick="delete_variation(this); return false;"><i class="fa fa-times"></i></a></td>
                                </tr>
                              <?php } ?>
                              <tr class="variation_value">
                                <td><input name="variations[variation][]" class="form-control variation" value="<?php echo $product_variation->variation_name ?>" data-id="<?php echo $product_variation->variation_id ?>" readonly /></td>
                                <td><input name="variations[variation_value][]" class="form-control variation_value" value="<?php echo $product_variation->variation_value ?>" data-id="<?php echo $product_variation->variation_value_id ?>" readonly /></td>
                                <td><input name="variations[rate][]" class="form-control rate" value="<?php echo $product_variation->rate ?>" /></td>
                                <td><input name="variations[quantity_number][]" class="form-control quantity_number" value="<?php echo $product_variation->quantity_number ?>" /></td>
                                <td><a href="#" class="btn btn-danger pull-right" onclick="delete_variation_value(this); return false;"><i class="fa fa-times"></i></a></td>
                              </tr>
                            <?php }
                          ?>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
  var mode = '<?php echo $this->uri->segment(3, 0); ?>';
  (mode == 'add_product') ? $('input[type="file"]').prop('required',true) : $('input[type="file"]').prop('required',false);
  $(function () {
    if ($('#is_digital').is(':checked')) {
      $('#quantity_number').attr({readonly:true, value:1}); 
    }
    appValidateForm($('form'), {
      product_name        : "required",
      product_description : "required",
      product_category_id : "required",
      rate                : "required",
      quantity_number     : "required"
    });
    $('#is_digital').click(function(event) {
      if($('#is_digital').is(':checked')){
        $(this).attr({value:1});
        $('#quantity_number').attr({readonly:true,value:1});
      }else{
        $(this).attr({value:0});
        $('#quantity_number').attr({readonly:false,value:1});
      }
    });
    change_variation_values();
    change_variation_quantity_event();
  });
  function get_variation_value_preview_values() {
    var response = {};
    response.variation_id = parseInt($('.selectpicker.variation').val());
    response.variation_name = '';
    for (var variation_index = 0; variation_index < $('.selectpicker.variation option').length; variation_index++) {
      var variation_item = $($('.selectpicker.variation option')[variation_index]);
      if (variation_item.val() == response.variation_id) {
        response.variation_name = variation_item.text();
      }
    }
    response.variation_value_id = parseInt($('.selectpicker.variation_value').val());
    response.variation_value_value = '';
    response.variation_value_values = [];
    for (var variation_index = 0; variation_index < $('select.variation_value option').length; variation_index++) {
      var variation_value_item = $($('.selectpicker.variation_value option')[variation_index]);
      if (variation_value_item.val() == response.variation_value_id) {
        response.variation_value_value = variation_value_item.text();
      }
      response.variation_value_values.push({id: parseInt(variation_value_item.val()), value: variation_value_item.text()});
    }
    return response;
  }
  $("body").on(
    "change",
    '[name="recurring"]',
    function () {
      var val = $(this).val();
      val == "custom" ? $(".recurring_custom").removeClass("hide") : $(".recurring_custom").addClass("hide");
    }
  );
  $("body").on(
    "change",
    '[name="is_variation"]',
    function () {
      var val = $(this).val();
      if (val !== "" && val != 0) {
        $("body").find("#variations_wrapper").removeClass("hide");
      } else {
        $("body").find("#variations_wrapper").addClass("hide");
      }
    }
  );
  function change_variation_values() {
    $.ajax({
      url: site_url+'products/variations/values',
      type: 'POST',
      dataType: 'json',
      data : {'variation_id':$('.selectpicker.variation').val()},
      success : function (data) {
        var variation_values_html = '<option value="">' + $('.selectpicker.variation_value').data('none-selected-text') + '</option>';
        for (var variation_index = 0; variation_index < data.length; variation_index++) {
          variation_values_html += '<option value="' + data[variation_index]['id'] + '">' + data[variation_index]['value'] + '</option>';
        }
        $('.selectpicker.variation_value').html(variation_values_html);
        $('.selectpicker.variation_value').selectpicker("refresh");
      }
    });
  }
  function change_variation_quantity_event() {
    change_variation_quantity();
    $("body").on(
      "change",
      'input.quantity_number',
      function () {
        change_variation_quantity();
      }
    );
  }
  function change_variation_quantity() {
    var total_quantities = 0;
    var quantity_numbers = $('input.quantity_number');
    for (var quantiry_index = 0; quantiry_index < quantity_numbers.length; quantiry_index++) {
      total_quantities += parseInt($(quantity_numbers[quantiry_index]).val());
    }
    $('#quantity_number').val(total_quantities);
  }
  function add_variation_value_to_table() {
    var data = get_variation_value_preview_values();

    if (data.variation_id === "") {
      return;
    }
    
    var variation_row = null;
    var row_variation_id = '';
    var row_variation_value_id = '';
    var rows = $(".table.product-variations-table tbody tr:not(.main)");
    for (var row_index = 0; row_index < rows.length; row_index++) {
      if ($(rows[row_index]).hasClass('variation')) {
        row_variation_id = $(rows[row_index]).find("input").data('id');
        if (row_variation_id == data.variation_id) {
          variation_row = $(rows[row_index]);
        }
      } else {
        row_variation_id = $(rows[row_index]).find("input.variation").data('id');
        row_variation_value_id = $(rows[row_index]).find("input.variation_value").data('id');
        if (row_variation_id == data.variation_id) {
          variation_row = $(rows[row_index]);
        }
        if (!data.variation_value_id) {
          if (row_variation_id == data.variation_id) {
            return;
          }
        } else {
          if (row_variation_value_id == data.variation_value_id) {
            return;
          }
        }
      }
    }

    var table_row = "";
    if (!data.variation_value_id) {
      table_row += '<tr class="variation">';
      table_row += '<td><input class="form-control" value="' + data.variation_name + '" data-id="' + data.variation_id + '" readonly /></td>';
      table_row += '<td></td>';
      table_row += '<td></td>';
      table_row += '<td></td>';
      table_row += '<td><a href="#" class="btn btn-danger pull-right" onclick="delete_variation(this); return false;"><i class="fa fa-times"></i></a></td>';
      table_row += '</tr>';
      for (var variation_value_index = 0; variation_value_index < data.variation_value_values.length; variation_value_index++) {
        if (data.variation_value_values[variation_value_index].id) {
          table_row += '<tr class="variation_value">';
          table_row += '<td><input name="variations[variation][]" class="form-control variation" value="' + data.variation_name + '" data-id="' + data.variation_id + '" readonly /></td>';
          table_row += '<td><input name="variations[variation_value][]" class="form-control variation_value" value="' + data.variation_value_values[variation_value_index].value + '" data-id="' + data.variation_value_values[variation_value_index].id + '" readonly /></td>';
          table_row += '<td><input name="variations[rate][]" class="form-control rate" value="' + $('input[name="rate"]').val() + '" /></td>';
          table_row += '<td><input name="variations[quantity_number][]" class="form-control quantity_number" value="1" /></td>';
          table_row += '<td><a href="#" class="btn btn-danger pull-right" onclick="delete_variation_value(this); return false;"><i class="fa fa-times"></i></a></td>';
          table_row += '</tr>';
        }
      }
      $("table.product-variations-table tbody").append(table_row);
    } else {
      if (!variation_row) {
        table_row += '<tr class="variation">';
        table_row += '<td><input class="form-control" value="' + data.variation_name + '" data-id="' + data.variation_id + '" readonly /></td>';
        table_row += '<td></td>';
        table_row += '<td></td>';
        table_row += '<td></td>';
        table_row += '<td><a href="#" class="btn btn-danger pull-right" onclick="delete_variation(this); return false;"><i class="fa fa-times"></i></a></td>';
        table_row += '</tr>';
      }
      table_row += '<tr class="variation_value">';
      table_row += '<td><input name="variations[variation][]" class="form-control variation" value="' + data.variation_name + '" data-id="' + data.variation_id + '" readonly /></td>';
      table_row += '<td><input name="variations[variation_value][]" class="form-control variation_value" value="' + data.variation_value_value + '" data-id="' + data.variation_value_id + '" readonly /></td>';
      table_row += '<td><input name="variations[rate][]" class="form-control rate" value="' + $('input[name="rate"]').val() + '" /></td>';
      table_row += '<td><input name="variations[quantity_number][]" class="form-control quantity_number" value="1" /></td>';
      table_row += '<td><a href="#" class="btn btn-danger pull-right" onclick="delete_variation_value(this); return false;"><i class="fa fa-times"></i></a></td>';
      table_row += '</tr>';
      if (!variation_row) {
        $("table.product-variations-table tbody").append(table_row);
      } else {
        variation_row.after(table_row);
      }
    }

    change_variation_quantity_event();
  }
  $("body").on(
    "change",
    '.selectpicker.variation',
    function () {
      change_variation_values();
    }
  );
  function delete_variation_values(row) {
    if (row.hasClass('variation_value')) {
      delete_variation_values(row.next());
      row.remove();
    }
  }
  function delete_variation(row) {
    $(row)
      .parents("tr")
      .addClass("animated fadeOut", function () {
        setTimeout(function () {
          delete_variation_values($(row).parents("tr").next());
          $(row).parents("tr").remove();
        }, 50);
      });
  }
  function delete_variation_value(row) {
    $(row)
      .parents("tr")
      .addClass("animated fadeOut", function () {
        setTimeout(function () {
          $(row).parents("tr").remove();
        }, 50);
      });
  }
</script>