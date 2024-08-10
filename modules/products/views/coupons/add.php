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
              <div class="col-md-6">
                <?php echo render_input('code', 'coupon_code', $coupon->code ?? ''); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <?php echo render_input('amount', 'coupon_amount', $coupon->amount ?? '', 'number'); ?>
              </div>
              <div class="col-md-6">
                <label for="type" class="control-label">
                  <?php echo _l('coupon_add_edit_type'); ?>
                </label>
                <select class="selectpicker"
                    data-width="100%"
                    name="type"
                    required="required"
                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                  <?php for ($i = 0; $i <= 1; ++$i) { ?>
                    <?php
                      $selected = '';
                      if (isset($coupon)) {
                        if (('%' == $coupon->type && $i == 0) || ('fixed' == $coupon->type && $i == 1)) {
                          $selected = 'selected';
                        }
                      }
                      if (0 == $i) {
                        $type_string =  _l('coupon_add_edit_type_percent');
                      } elseif (1 == $i) {
                        $type_string =  _l('coupon_add_edit_type_fixed');
                      }
                    ?>
                    <option value="<?php echo ($i == 0 ? '%' : 'fixed'); ?>" <?php echo $selected; ?>><?php echo $type_string; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <?php echo render_input('max_uses', 'coupon_max_uses', $coupon->max_uses ?? '', 'number'); ?>
              </div>
              <div class="col-md-6">
                <?php echo render_input('max_uses_per_client', 'coupon_max_uses_per_client', $coupon->max_uses_per_client ?? '', 'number'); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <?php echo render_date_input('start_date', 'coupon_start_date', $coupon->start_date ?? ''); ?>
              </div>
              <div class="col-md-6">
                <?php echo render_date_input('end_date', 'coupon_end_date', $coupon->end_date ?? ''); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
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
<script type="text/javascript">
  $(function () {
    appValidateForm($('form'), {
      code                    : "required",
      amount                  : {required: true, min:0.01},
      type                    : "required",
      max_uses                : {required: true, min:1},
      max_uses_per_client     : {required: true, min:1},
      start_date              : "required",
      end_date                : "required",
    });
  });
</script>