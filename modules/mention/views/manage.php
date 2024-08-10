<?php init_head();?>
<div id="wrapper" class="mention">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
              <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
            <hr />
            <div class="col-md-12">
              <a href="<?php echo admin_url('profile'); ?>">
              <?php echo staff_profile_image($current_user->staffid,array('staff-profile-image-small')); ?>
              <?php echo html_entity_decode($current_user->firstname . ' ' . $current_user->lastname);?></a>
              <div id="inputor_post" class="inputor inputor_post_top" contentEditable="true" data-placeholder="<?php echo _l('post_placeholder')?>"></div>              
              <a href="#" onclick="post_inputor(); return false;" class="px-0 btn btn-info pull-right"><?php echo _l('post'); ?></a>              
            </div>
            <div class="col-md-12">
            <hr />
              <div id="newsfeed_data_internal">
                <?php echo html_entity_decode($list_mention); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal" id="show_post_detail" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
       </div>
   </div>
</div>
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/mention/assets/js/manage_js.php';?>