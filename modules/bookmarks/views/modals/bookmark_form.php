<div class="modal fade bookmarks-modal" id="bookmarks-modal" tabindex="-1" role="dialog" aria-labelledby="bookmarks-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?php echo form_open(admin_url('bookmarks/save')); ?>
      <div class="modal-header">
        <button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo _l('bookmark') ?></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 bookmark_form_container">
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
function init_bookmark_modal(bookmark_id){
	$('.bookmark_form_container').load(admin_url + 'bookmarks/bookmark_form/' + bookmark_id, function(){
		$('#bookmarks-modal').modal();
		init_selectpicker();
		init_tags_inputs();
		appValidateForm($('form'), {
			url: 'required',
			title: 'required'
		});
	});
}
</script>