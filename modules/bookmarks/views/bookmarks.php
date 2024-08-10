<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                    <div class="_buttons">
                        <a href="#" onclick="init_bookmark_modal(0); return false;" class="btn btn-info pull-left display-block mright5"><?php echo _l('bookmark_add_new_bookmark'); ?></a>
						<a href="<?php echo admin_url('bookmarks/locations') ?>" class="btn btn-info pull-left display-block mright5"><?php echo _l('bookmark_locations'); ?></a>												
						<a href="#" onclick="init_location_modal(0); return false;" class="btn btn-info pull-left display-block mright5"><?php echo _l('bookmark_add_new_location'); ?></a>	
                    </div>
					<div class="display-block text-right">
						<div class="_filters _hidden_inputs">
							<?php
							   foreach($filtered_locations as $_location){
									echo form_hidden('location_'.$_location['id']);
							   }
							   foreach($filtered_staffs as $_staff){
									echo form_hidden('staff_'.$_staff['staffid']);
							   }
							?>
						 </div>
						 <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fa fa-filter" aria-hidden="true"></i>
							</button>
							<ul class="dropdown-menu width300">
							   <li>
								  <a href="#" data-cview="all" onclick="dt_custom_view('','.table-bookmarks',''); return false;">
								  <?php echo _l('all'); ?>
								  </a>
							   </li>							   							   
							   <?php if(!empty($filtered_locations)){ ?>
							   <li class="divider"></li>							   							   
								<?php } ?>
							   <?php foreach($filtered_locations as $location){ ?>
							   <li>
								  <a href="#" data-cview="location_<?php echo $location['id']; ?>" onclick="dt_custom_view('location_<?php echo $location['id']; ?>','.table-bookmarks','location_<?php echo $location['id']; ?>'); return false;">
								  <?php echo html_escape($location['location_name']); ?>
								  </a>
							   </li>
							   <?php } ?>
							   <div class="clearfix"></div>																
							   <?php if(!empty($filtered_staffs)){ ?>								
							   <li class="divider"></li>							   								
								<?php } ?>
							   <?php foreach($filtered_staffs as $staff){ ?>
							   <li>
								  <a href="#" data-cview="staff_<?php echo $staff['staffid']; ?>" onclick="dt_custom_view('staff_<?php echo $staff['staffid']; ?>','.table-bookmarks','staff_<?php echo $staff['staffid']; ?>'); return false;">
								  <?php echo get_staff_full_name($staff['staffid']); ?>
								  </a>
							   </li>
							   <?php } ?>
							</ul>
						 </div>
					</div>
					<a href="#" data-toggle="modal" data-table=".table-bookmarks" data-target="#bookmarks_bulk_actions" class="hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
				   <div class="modal fade bulk_actions" id="bookmarks_bulk_actions" tabindex="-1" role="dialog">
					  <div class="modal-dialog" role="document">
						 <div class="modal-content">
							<div class="modal-header">
							   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							   <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
							</div>
							<div class="modal-body">
							   <?php if(has_permission('leads','','delete')){ ?>
							   <div class="checkbox checkbox-danger">
								  <input type="checkbox" name="mass_delete" id="mass_delete">
								  <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
							   </div>
							   <hr class="mass_delete_separator" />
							   <?php } ?>
							   <div id="bulk_change">
								  <div class="form-group">
									 <?php echo '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
									 <input type="text" class="tagsinput" id="tags_bulk" name="tags_bulk" value="" data-role="tagsinput">
								  </div>
								  <?php echo render_select('move_to_bookmark_location_bulk',$locations,array('id','location_name'),'bookmark_change_location'); ?>
								  <hr />
								  <div class="form-group no-mbot">
									 <div class="radio radio-primary radio-inline">
										<input type="radio" name="bookmarks_bulk_visibility" id="bookmarks_bulk_public" value="public">
										<label for="leads_bulk_public">
										<?php echo _l('bookmark_public'); ?>
										</label>
									 </div>
									 <div class="radio radio-primary radio-inline">
										<input type="radio" name="bookmarks_bulk_visibility" id="bookmarks_bulk_private" value="private">
										<label for="leads_bulk_private">
										<?php echo _l('bookmark_only_me'); ?>
										</label>
									 </div>
								  </div>
							   </div>
							</div>
							<div class="modal-footer">
							   <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
							   <a href="#" class="btn btn-info" onclick="bookmarks_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
							</div>
						 </div>
						 <!-- /.modal-content -->
					  </div>
					  <!-- /.modal-dialog -->
				   </div>
				   <!-- /.modal -->
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <?php render_datatable(array(
						'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="bookmarks"><label></label></div>',
                        _l('bookmark_title'),
                        _l('bookmark_url'),
						_l('bookmark_description'),
						_l('bookmark_location'),
						_l('bookmark_created_by'),
						_l('bookmark_created_date'),
						_l('bookmark_tags'),
						_l('bookmark_privacy'),
                        ),'bookmarks', [], ['data-last-order-identifier'=>'bookmarks', 'data-default-order'=>get_table_last_order('bookmarks')]); 
					?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('bookmarks/modals/bookmark_form'); ?><?php $this->load->view('bookmarks/modals/location_form'); ?>
<script>
	"use strict";
    $(function(){
		var Bookmarks_ServerParams = {};
		$.each($('._hidden_inputs._filters input'),function(){
		   Bookmarks_ServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
		});
        initDataTable('.table-bookmarks', window.location.href, [0], [0], Bookmarks_ServerParams, [6, 'desc']);
    });
	var ClipboardHelper = {
		copyElement: function ($element)
		{
		   this.copyText($element.text())
		},
		copyText:function(text) // Linebreaks with \n
		{
			var $tempInput =  $("<textarea>");
			$("body").append($tempInput);
			$tempInput.val(text).select();
			document.execCommand("copy");
			$tempInput.remove();
			alert_float('success', '<?php echo _l("bookmark_url_has_been_copied") ?>');
		}
	};
	function bookmarks_bulk_action(event) {
		if (confirm_delete()) {
			var mass_delete = $('#mass_delete').prop('checked');
			var ids = [];
			var data = {};
			if (mass_delete == false || typeof(mass_delete) == 'undefined') {
				data.location = $('#move_to_bookmark_location_bulk').val();
				data.tags = $('#tags_bulk').tagit('assignedTags');
				data.visibility = $('input[name="bookmarks_bulk_visibility"]:checked').val();
				data.visibility = typeof(data.visibility) == 'undefined' ? '' : data.visibility;
				if (data.location === '' &&
					data.tags.length == 0 &&
					data.visibility === '') {
					return;
				}
			} else {
				data.mass_delete = true;
			}
			var rows = $('.table-bookmarks').find('tbody tr');
			$.each(rows, function() {
				var checkbox = $($(this).find('td').eq(0)).find('input');
				if (checkbox.prop('checked') === true) {
					ids.push(checkbox.val());
				}
			});
			data.ids = ids;
			$(event).addClass('disabled');
			setTimeout(function() {
				$.post(admin_url + 'bookmarks/bulk_action', data).done(function() {
					window.location.reload();
				}).fail(function(data) {
					$('#bookmarks-modal').modal('hide');
					alert_float('danger', data.responseText);
				});
			}, 200);
		}
	}
</script>
</body>
</html>
