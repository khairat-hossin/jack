
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php //init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel_s">
          <div class="panel-body">
            <h3 class="no-margin title text-warning">
              <?php echo htmlspecialchars($title);?>
            </h3>
            <hr class="hr-panel-heading" />
            <?php echo form_open_multipart($this->uri->uri_string()); ?>
            <div class="row">
              <?php //echo '<pre>'; print_r($product); exit;?>
              <div class="col-md-6">
                <?php
                $option_attrs = ['p_category_id', 'p_category_name'];
                foreach ($product_categories as $option) {
                  $val       = '';
                  $_selected = '';
                  $key       = '';
                  if (isset($option[$option_attrs[0]]) && !empty($option[$option_attrs[0]])) {
                      $key = $option[$option_attrs[0]];
                  }
                  if (!is_array($option_attrs[1])) {
                      $val = $option[$option_attrs[1]];
                  } else {
                      foreach ($option_attrs[1] as $_val) {
                          $val .= $option[$_val] . ' ';
                      }
                  }
                  $val = trim($val);
                }
                // echo $val; exit;
                  ?>
                <div class="form-group" app-field-wrapper="product_name">
                    <label for="product_name" class="control-label"><?php echo _l('products_categories'); ?></label>
                    <span style="border: none; background-color: white !important; color: #555 !important;box-shadow: none;" type="text" id="name" name="name" class="form-control ays-ignore" value="<?php echo $val; ?>" aria-invalid="false"><?php echo $val; ?></span>
                    <input type="hidden" id="product_id" name="product_id" value="<?php echo $product->id ?? ''; ?>" aria-invalid="false">
                </div>
                <?php //echo render_select('product_category_id', $product_categories, ['p_category_id', 'p_category_name'], 'products_categories', !empty(set_value('product_category_id')) ? set_value('product_category_id') : $product->product_category_id ?? ''); ?>
              </div>
              <div class="col-md-6">
                <div class="form-group" app-field-wrapper="product_name">
                  <label for="product_name" class="control-label"><?php echo _l('product_name'); ?></label>
                  <span style="border: none; background-color: white !important; color: #555 !important;box-shadow: none;" type="text" id="name" name="name" class="form-control ays-ignore" value="<?php echo $product->product_name ?? ''; ?>" aria-invalid="false"><?php echo $product->product_name ?? ''; ?></span>
              </div>
            </div>
              </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group" app-field-wrapper="product_name">
                    <label for="product_name" class="control-label"><?php echo _l('product_description'); ?></label>
                    <span style="border: none; background-color: white !important; color: #555 !important;box-shadow: none;height:auto;" type="text" id="name" name="name" class="form-control ays-ignore" value="<?php echo $product->product_description ?? ''; ?>" aria-invalid="false"><?php echo $product->product_description ?? ''; ?></span>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group" app-field-wrapper="product_name">
                  <?php 
                  $payment_modes_arr = []; 
                  foreach ($payment_modes as $mode) {
                    if (isset($product)) {
                      if ($product->allowed_payment_modes) {
                          $inv_modes = unserialize($product->allowed_payment_modes);
                          if (is_array($inv_modes)) {
                              foreach ($inv_modes as $_allowed_payment_mode) {
                                  if ($_allowed_payment_mode == $mode['id']) {
                                      $selected = ' selected';
                                      $payment_modes_arr[] = $mode['name'];
                                  }
                              }
                          }
                      }
                    }
                  }
                  $payment_modes_string = implode(',', $payment_modes_arr);
                  ?>
                    <label for="product_name" class="control-label"><?php echo _l('invoice_add_edit_allowed_payment_modes'); ?></label>
                    <span style="border: none; background-color: white !important; color: #555 !important;box-shadow: none;" type="text" id="name" name="name" class="form-control ays-ignore" value="<?php echo $payment_modes_string ?? ''; ?>" aria-invalid="false"><?php echo $payment_modes_string ?? ''; ?></span>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <label for="product_name" class="control-label"><?php echo _l('invoice_item_add_edit_rate_currency'); ?></label>
                <span style="border: none; background-color: white !important; color: #555 !important;box-shadow: none;" type="text" id="name" name="name" class="form-control ays-ignore" value="<?php echo $product->rate ?? ''; ?>" aria-invalid="false"><?php echo $product->rate ?? ''; ?></span>

                <?php //echo render_input('rate', _l('invoice_item_add_edit_rate_currency'), $product->rate ?? '', 'number',['min'=>"0.00"]); ?>
              </div>
              <div class="col-md-6">
                <?php
                  $selected_taxes ='';
                  if (!empty($product->taxes)) {
                    $selected_taxes = (!empty(($product->taxes))) ? unserialize($product->taxes) : '';
                  }
                  $this->load->model('taxes_model');
                  $taxes = $this->taxes_model->get();
                  $i     = 0;
                  foreach ($taxes as $tax) {
                      unset($taxes[$i]['id']);
                      $taxes[$i]['name'] = $tax['name'] . '|' . $tax['taxrate'];
                      $i++;
                  }
                  if (is_array($selected_taxes)) {
                    foreach ($selected_taxes as $tax) {
                        // Check if tax empty
                        if ((!is_array($tax) && $tax == '') || is_array($tax) && $tax['taxname'] == '') {
                            continue;
                        };
                        // Check if really the taxname NAME|RATE don't exists in all taxes
                        if (!value_exists_in_array_by_key($taxes, 'name', $tax)) {
                            if (!is_array($tax)) {
                                $tmp_taxname = $tax;
                                $tax_array   = explode('|', $tax);
                            } else {
                                $tax_array   = explode('|', $tax['taxname']);
                                $tmp_taxname = $tax['taxname'];
                                if ($tmp_taxname == '') {
                                    continue;
                                }
                            }
                            $taxes[] = ['name' => $tmp_taxname, 'taxrate' => $tax_array[1]];
                        }
                    }
                }
                // Clear the duplicates
                // $taxes = Arr::uniqueByKey($taxes, 'name');
                $tax_arr = [];
                $tax_string = '';
                foreach ($taxes as $tax) {
                  $selected = '';
                  if (is_array($selected_taxes)) {
                      foreach ($selected_taxes as $_tax) {
                          if (is_array($_tax)) {
                              if ($_tax['taxname'] == $tax['name']) {
                                  $selected = 'selected';
                                  $tax_arr[] = $tax['taxrate'].'%';
                              }
                          } else {
                              if ($_tax == $tax['name']) {
                                  $selected = 'selected';
                                  $tax_arr[] = $tax['taxrate'].'%';
                              }
                          }
                      }
                  } else {
                      if ($selected_taxes == $tax['name']) {
                          $selected = 'selected';
                          $tax_arr[] = $tax['taxrate'].'%';
                      }
                  }
      
              }
              $tax_string = implode(',', $tax_arr);

                  // echo $this->misc_model->get_taxes_dropdown_template('taxes[]', $selected_taxes);
                ?>
                <label for="product_name" class="control-label"><?php echo _l('Tax'); ?></label>
                <span style="border: none; background-color: white !important; color: #555 !important;box-shadow: none;" type="text" id="name" name="name" class="form-control ays-ignore" value="<?php echo $tax_string; ?>" aria-invalid="false"><?php echo $tax_string; ?></span>

              </div>
              
            </div>
            <div class="row" style="padding-top: 12px;">
              <div class="col-md-6">
                <label for="product_name" class="control-label"><?php echo _l('quantity'); ?></label>
                <span style="border: none; background-color: white !important; color: #555 !important;box-shadow: none;" type="text" id="name" name="name" class="form-control ays-ignore" value="<?php echo $product->quantity_number ?? ''; ?>" aria-invalid="false"><?php echo $product->quantity_number ?? ''; ?></span>
              </div>
              <div class="col-md-6">
                <label for="is_digital"><?php echo _l('no_qty_digital_product'); ?></label>
                <span style="border: none; background-color: white !important; color: #555 !important;box-shadow: none;" type="text" id="name" name="name" class="form-control ays-ignore" value="<?php echo $product->is_digital == '1' ? 'Yes' : 'No'; ?>" aria-invalid="false"><?php echo $product->is_digital == '1' ? 'Yes' : 'No'; ?></span>
              </div>
            </div>
            <?php
              $existing_image_class = 'col-md-6';
              $input_file_class     = 'col-md-6';
              if (empty($product->product_image)) {
                $existing_image_class = 'col-md-6';
                $input_file_class     = 'col-md-6';
              }
            ?>
            
            <div class="row" style="padding-top: 12px;">
              <?php if (!empty($product->product_image)) { ?>
                <div class="<?php echo htmlspecialchars($existing_image_class); ?>">
                  <div class="existing_image">
                    <label class="control-label">Image</label>
                    <img src="<?php echo base_url('modules/'.PRODUCTS_MODULE.'/uploads/'.$product->product_image); ?>" class="img img-responsive img-thubnail zoom"/>
                  </div>
                </div>
              <?php } ?>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="is_variation" class="control-label">
                    <?php echo _l('product_add_edit_variation'); ?>
                  </label>
                  <input style="border: none; background-color: white !important; color: #555 !important; box-shadow: none;" readonly type="text" id="name" name="name" class="form-control ays-ignore" value="<?php echo $product->is_variation == 1 ? 'Yes' : 'No'; ?>" aria-invalid="false">

                </div>
              </div>
            </div>
            <hr />
            <div class="row">
              <div class="col-md-6"></div>
              <div class="col-md-6">
              <!-- <label for="product_name" class="control-label"></label> -->
                <div id="filter_product_html"></div>
              </div>
            </div>
            <div class="row">
              
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
                      
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- <button type="submit" class="btn btn-info pull-right"><?php //echo _l('submit'); ?></button> -->
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php //init_tail(); ?>
<script type="text/javascript" src="<?php echo module_dir_url('products', 'assets/js/client_products.js'); ?>"></script>

<script type="text/javascript">
  var mode = '<?php echo $this->uri->segment(3, 0); ?>';
  (mode == 'add_product') ? $('input[type="file"]').prop('required',true) : $('input[type="file"]').prop('required',false);
  $(function () {
    var product_id = $('#product_id').val();
    // alert(product_id);
    filter_product_data({'product_id': product_id});
    $(".form-group").removeClass("select-placeholder");
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
    // change_variation_values();
    // change_variation_quantity_event();
  });
  function filter_product_data(post_data = {}) {
    $(".no_product").addClass('hidden');
    $("#filter_html").html("");
    $(".image_loader").show();
    $.ajax({
        url: site_url+'products/client/product_filter',
        type: 'POST',
        dataType: 'json',
        data : post_data,
        success : function (data) {
          console.log("data", data);
            render_single_product_data(data);
        }
    });
  }

  var product_variations = [];
  function render_single_product_data(data) {
    var html = "";
    var total_taxes = "";
    cart_items = [];
    $.each(data, function(index, val) {
        var cart_data_quantity = "";
        var button = "";
        var product_class = "";

        if (val.cart_data) {
            cart_items.push(val.cart_data);
        }

        if (val.total_tax != 0) {
            total_taxes = `<span class='total_taxes text-warning'>(+ ${val.total_tax}% taxes)</span>`;
        } else {
            total_taxes = "";
        }

        if (parseInt(val.quantity_number) < 1 && val.is_digital != 1) {
            button = `<button class="btn btn-danger pull-right">${val.out_of_stock}</button>`;
        } else {
            var label  = val.add_to_cart;
            if (val.cart_data && !val.cart_data.product_variation_id && val.cart_data.quantity) {
                label  = val.update_cart;
                cart_data_quantity = val.cart_data.quantity;
            }
            button = `<button class="btn btn-warning add_cart pull-right">${label}</button>`
        }

        var max_attr = "";
        if (val.is_digital != 1) {
            max_attr = `max="${val.quantity_number}"`;
        }

        var recurring_type = "";
        var cycles_text = "&nbsp;";
        if (val.recurring != 0) {
            recurring_type = val.recurring_type;
            if (val.recurring_type == "") {
                recurring_type = "month";
            }
            recurring_type = "/ "+ ((val.recurring != 1) ? val.recurring:"") + ' '+ recurring_type;
            if (val.cycles == 0) {
                cycles_text = "Infinite recurring";
            }
            if (val.cycles == 1) {
                cycles_text = "1 time totally";
            }
            if (val.cycles > 1) {
                cycles_text = val.cycles+" times totally";
            }
        }
        var variations_content = "";
        var product_variation_ids = [];
        if (val.variations) {
            if (val.variations.length) {
                variations_content += '<div class="row variations">';
                variations_content += '<div class="col-md-6 col-sm-6 col-xs-6">';
                variations_content += '<select class="selectpicker variation_id">';
                variations_content += '<option value=""></option>';
            }
            for (var variation_index = 0; variation_index < val.variations.length; variation_index++) {
              if (!product_variation_ids.includes(val.variations[variation_index]['variation_id'])) {
                product_variation_ids.push(val.variations[variation_index]['variation_id']);
                variations_content += '<option value="' + val.variations[variation_index]['variation_id'] + '">' + val.variations[variation_index]['variation_name'] + '</option>';
              }
            }
            if (val.variations.length) {
                variations_content += '</select>';
                variations_content += '</div>';
                variations_content += '<div class="col-md-6 col-sm-6 col-xs-6">';
                variations_content += '<select class="selectpicker variation_value_id">';
                variations_content += '</select>';
                variations_content += '</div>';
                variations_content += '</div>';
            }
        } else {
            product_class = 'without-variations';
        }
        html += `<div class="row input_data" id="">
                      <div class="col-md-6 col-sm-6 col-xs-6 products-pricing">
                          <input type="number" name="quantity" min="1" ${max_attr} value="${cart_data_quantity}" class="form-control" placeholder="${val.qty}">
                          <input type="hidden" name="product_id" value="${val.id}" class="form-control">
                          <input type="hidden" name="product_variation_id" value="" class="form-control">
                          <input type="hidden" min="1" ${max_attr} class="form-control variation_quantity">
                      </div>
                      <div class="col-md-6 col-sm-6 col-xs-6 products-pricing">
                          ${button}
                      </div>
                  </div>
               `;
    });
    $("#filter_product_html").hide();
    $(".image_loader").hide();
    $("#filter_product_html").html(html).fadeIn('slow');
    if (html=="") {
        $(".no_product").removeClass('hidden');
    }
    
    $(document).on('change', '.selectpicker.variation_id' , function () {
        var that = this;
        $(this).parents('.product-row').find('input[name="product_variation_id"]').val('');
        $.ajax({
            url: site_url+'products/client/variation_values',
            type: 'POST',
            dataType: 'json',
            data : {
                'product_id': $(this).parents('.product-row').find('input[name="product_id"]').val(),
                'variation_id': $(this).parents('.product-row').find('.selectpicker.variation_id').val()
            },
            success : function (data) {
                render_product_variation_values_data(that, data);
            }
        });
    });
    
    appSelectPicker();
}
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