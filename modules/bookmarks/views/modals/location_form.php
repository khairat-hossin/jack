<div class="modal fade bookmark-locations-modal" id="bookmark-locations-modal" tabindex="-1" role="dialog" aria-labelledby="bookmark-locations-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?php echo form_open(admin_url('bookmarks/save_location')); ?>
      <div class="modal-header">
        <button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo _l('bookmark_location') ?></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 location_form_container">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<script>
"use strict";
function init_location_modal(location_id){
	$('.location_form_container').load(admin_url + 'bookmarks/location_form/' + location_id, function(){
		$('#bookmark-locations-modal').modal();
		appValidateForm($('form'), {
			location_name: 'required'
		});
	});
}
</script>