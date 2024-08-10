 <div class="row">
 	
 	<?php if( (has_permission('team_password','','create') || is_admin()) && $cate != 'all' ){ ?>	
 		<div class="col-md-1"> 	
 			<a href="javascript:void(0)" onclick="add_share();" class="btn btn-info pull-left">
 				<?php echo _l('add'); ?>
 			</a>
 			<div class="clearfix"></div>
 		</div>
 	<?php } ?>
 	<input type="hidden" name="cate" value="<?php echo html_entity_decode($cate); ?>">

 	<?php if(is_admin() || has_permission('team_password','','view')){ ?>
	 	<div class="col-md-3">
	 		<select name="client_group_filter[]" id="client_group_filter" class="selectpicker" data-actions-box="true" multiple data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('client_group'); ?>" >
	 			<?php foreach($customer_groups as $gr){ ?>
	 				<option value="<?php echo html_entity_decode($gr['id']); ?>"><?php echo html_entity_decode($gr['name']); ?></option>
	 			<?php } ?>
	 		</select> 
	 		<div class="clearfix"></div>
	 	</div>

	 	<div class="col-md-3">
	 		<select name="client_filter[]" class="selectpicker" id="client_filter" multiple data-live-search="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('contact'); ?>"> 
	 			<?php foreach($contact as $s){ ?>
	 				<option value="<?php echo html_entity_decode($s['email']); ?>" ><?php echo get_company_name(get_user_id_by_contact_id($s['id'])).' - '.html_entity_decode($s['lastname']).' '.html_entity_decode($s['firstname']); ?></option>
	 			<?php } ?>
	 		</select> 
	 	</div>
	 <?php } ?>

 	<div class="clearfix"></div>
 	<div class="col-md-12">
 	<hr/>
 		<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-share" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
              <?php render_datatable(array(
                '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="share"><label></label></div>',
                _l('id'),
                _l('client'),
                _l('name'),
                _l('type'),
                _l('category'),
                _l('date_create'),
				_l('effective_time'),
				_l('options'),
                ),'share',[],
                  array(
                     'id'=>'table-share',
                     'data-last-order-identifier'=>'table-share',
                     'data-default-order'=>get_table_last_order('table-share'),
                   )); ?>

 	</div>

 	<div class="modal bulk_actions" id="table_share_list_bulk_actions" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
               <?php if(has_permission('rec_proposal','','delete') || is_admin()){ ?>
               <div class="checkbox checkbox-danger">
                  <input type="checkbox" name="mass_delete" id="mass_delete">
                  <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
               </div>
              
               <?php } ?>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

               <?php if(has_permission('purchase','','delete') || is_admin()){ ?>
               <a href="#" class="btn btn-info" onclick="tp_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                <?php } ?>
            </div>
         </div>
      </div>
    </div>
 </div>
 