<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s section-heading section-tickets">
  <div class="panel-body">
    <h4 class="no-margin section-text"><?php echo _l('mention'); ?></h4>
    <hr>
    <div class="row">
      <div class="col-md-12">
          <a ><img src="<?php echo contact_profile_image_url($contact->id,'thumb'); ?>" data-toggle="tooltip" data-title="<?php echo html_entity_decode($contact->firstname . ' ' .$contact->lastname);?>" class="client-profile-image-small"></a>
          <?php echo html_entity_decode($contact->firstname . ' ' .$contact->lastname);?>
          <div id="inputor_post" class="inputor inputor_post_top" contentEditable="true" data-placeholder="<?php echo _l('post_placeholder')?>"></div>              
          <a href="#" onclick="post_inputor(); return false;" class="px-0 btn btn-info pull-right"><?php echo _l('post'); ?></a>       
      </div>
    </div>
  </div>
</div>
<div class="panel_s">
  <div class="panel-body">
    <div id="newsfeed_data_internal">
      <?php echo html_entity_decode($list_mention); ?>
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