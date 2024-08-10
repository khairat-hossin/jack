<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s " id="TableData">
          <div class="panel-body">
            <?php if (has_permission('products', '', 'create')) { ?>
              <a href="<?php echo admin_url('products/coupons/add'); ?>" class="btn btn-info pull-left display-block">
                <?php echo _l('new_coupon'); ?>
              </a>
            <?php } ?>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12" id="panel">
           <div class="panel_s">
              <div class="panel-body">
                <?php
                $table_data = [
                  _l('coupon_code'),
                  _l('coupon_type'),
                  _l('coupon_amount'),
                  _l('coupon_max_uses'),
                  _l('coupon_max_uses_per_client'),
                  _l('coupon_start_date'),
                  _l('coupon_end_date'),
                  _l('coupon_used_times'),
                  ];
                  render_datatable($table_data, 'coupons'); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  <?php init_tail(); ?>
<script type="text/javascript">
  $(function(){
    initDataTable('.table-coupons', window.location.href, 'undefined', 'undefined', '');
  });
</script>
