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
              <div class="col-md-12">
                <?php echo render_input('variation_name', 'variation_name', $variation->name ?? ''); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <?php echo render_textarea('variation_description', 'variation_description', $variation->description ?? ''); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive s_table">
                  <table class="table variation-values-table items table-main-variation-edit has-calculations no-mtop">
                    <thead>
                      <tr>
                        <th></th>
                        <th width="30%" align="left">
                          <?php echo _l('variation_table_value_heading'); ?>
                        </th>
                        <th width="50%" align="left"><?php echo _l('variation_table_value_description'); ?></th>
                        <th align="center"><i class="fa fa-cog"></i></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="main">
                        <td></td>
                        <td>
                          <input type="text" name="name" class="form-control" placeholder="<?php echo _l('variation_value_placeholder'); ?>" />
                        </td>
                        <td>
                          <textarea name="description" rows="4" class="form-control" placeholder="<?php echo _l('variation_value_description_placeholder'); ?>"></textarea>
                        </td>
                        <td>
                          <button type="button" onclick="add_variation_value_to_table(); return false;" class="btn pull-right btn-primary"><i class="fa fa-check"></i></button>
                        </td>
                      </tr>

                      <?php
                        $variation_values   = [];
                        $i                = 1;
                        $values_indicator = 'values';
                        if (isset($variation)) {
                          $variation_values       = $variation->values;
                        }
                        foreach ($variation_values as $value) {
                          $table_row = '<tr class="sortable item">';
                          $table_row .= '<td class="dragger">';

                          $table_row .= form_hidden('' . $values_indicator . '[' . $i . '][valueid]', $value['id']);

                          // order input
                          $table_row .= '<input type="hidden" class="order" name="' . $values_indicator . '[' . $i . '][order]">';
                          $table_row .= '</td>';
                          $table_row .= '<td><input name="' . $values_indicator . '[' . $i . '][value]" class="form-control" value="' . $value['value'] . '" /></td>';
                          $table_row .= '<td><textarea name="' . $values_indicator . '[' . $i . '][description]" class="form-control" rows="4">' . clear_textarea_breaks($value['description']) . '</textarea></td>';
                          $table_row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="delete_variation_value(this); return false;"><i class="fa fa-times"></i></a></td>';

                          $table_row .= '</tr>';
                          echo $table_row;
                          $i++;
                        }
                      ?>
                    </tbody>
                  </table>
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
  $(function () {
    appValidateForm($('form'), {
      variation_name : "required",
    });
    
    init_items_sortable();
    reorder_items();
  });

  var lastAddedValueKey = null;
  function get_variation_value_preview_values() {
    var response = {};
    response.name = $('.main input[name="name"]').val();
    response.description = $('.main textarea[name="description"]').val();
    return response;
  }

  function add_variation_value_to_table(data, valueid) {
    data = typeof data == "undefined" || data == "undefined" ? get_variation_value_preview_values() : data;

    if (data.name === "") {
      return;
    }
    
    var rows = $(".table.items tbody tr.item");
    for (var row_index = 0; row_index < rows.length; row_index++) {
      if ($(this).find("input.name").val() == data.name) {
        return;
      }
    }

    var table_row = "";
    var value_key = lastAddedValueKey ? (lastAddedValueKey += 1) : $("body").find(".table.items tbody tr.item").length + 1;
    lastAddedValueKey = value_key;

    table_row += '<tr class="sortable item">';
    table_row += '<td class="dragger">';
    table_row += '<input type="hidden" class="order" name="values[' + value_key + '][order]" />';
    table_row += '</td>';
    table_row += '<td>';
    table_row += '<input type="text" name="values[' + value_key + '][value]" class="form-control name" value="' + data.name + '" />';
    table_row += '</td>';
    table_row += '<td>';
    table_row += '<textarea name="values[' + value_key + '][description]" class="form-control" rows="4">' + data.description + '</textarea>';
    table_row += '</td>';
    table_row += '<td><a href="#" class="btn btn-danger pull-right" onclick="delete_variation_value(this); return false;"><i class="fa fa-times"></i></a></td>';
    table_row += '</tr>';
    $("table.items tbody").append(table_row);

    $('.main input[name="name"]').val('');
    $('.main textarea[name="description"]').val('');

    reorder_items();
  }
  
  function delete_variation_value(row) {
    $(row)
      .parents("tr")
      .addClass("animated fadeOut", function () {
        setTimeout(function () {
          $(row).parents("tr").remove();
        }, 50);
      });
    
      reorder_items();
  }
</script>