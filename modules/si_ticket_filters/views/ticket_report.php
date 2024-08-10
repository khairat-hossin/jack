<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$report_heading = '';
?>
<link href="<?php echo module_dir_url('si_ticket_filters','assets/css/si_ticket_filters_style.css'); ?>" rel="stylesheet" />
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo form_open($this->uri->uri_string() . ($this->input->get('filter_id') ? '?filter_id='.$this->input->get('filter_id') : ''),"id=si_form_ticket_filter"); ?>
						<h4 class="pull-left"><?php echo _l('si_ticket_filters')." - "._l('si_tf_filters_menu'); ?> <small class="text-success"><?php echo ($saved_filter_name);?></small></h4>
						<div class="btn-group pull-right mleft4 btn-with-tooltip-group" data-toggle="tooltip" data-title="<?php echo _l('si_tf_filter_templates'); ?>" data-original-title="" title="">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-list"></i>
							</button>
							<ul class="row dropdown-menu notifications width400">
							<?php
							if(!empty($filter_templates))
							{
								foreach($filter_templates as $row)
								{
									echo "<li><a href='tickets_report?filter_id=$row[id]'>$row[filter_name]</a></li>";
								}
							}
							else
								echo '<li><a >'._l('si_tf_no_filter_template').'</a></li>';
							?>
							</ul>
						</div>
						<button type="submit" data-toggle="tooltip" data-title="<?php echo _l('si_tf_apply_filter'); ?>" class=" pull-right btn btn-info mleft4"><?php echo _l('filter'); ?></button>
						<a href="tickets_report" class=" pull-right btn btn-info mleft4"><?php echo _l('new'); ?></a>
						<div class="clearfix"></div>
						<hr />
						<div class="row">
							<?php if(has_permission('si_ticket_filters', '', 'view')){?>
							<div class="col-md-2 border-right">
								<label for="member" class="control-label"><?php echo _l('staff_members'); ?></label>
								<?php echo render_select('member',$members,array('staffid',array('firstname','lastname')),'',$staff_id,array('data-none-selected-text'=>_l('all_staff_members')),array(),'no-margin'); ?>
							</div>
							<?php } ?>
							<div class="col-md-2 text-center1 border-right">
								<label for="status" class="control-label"><?php echo _l('ticket_dt_status'); ?></label>		
								<div class="form-group no-margin select-placeholder">
									<select name="status[]" id="status" class="selectpicker no-margin" data-width="100%" data-title="<?php echo _l('ticket_dt_status'); ?>" multiple>
										<option value="" <?php if(in_array('',$statuses)){echo 'selected'; } ?>><?php echo _l('all'); ?></option>
										<?php foreach($ticket_statuses as $status){ ?>
										<option value="<?php echo ($status['ticketstatusid']); ?>" <?php if(in_array($status['ticketstatusid'],$statuses)){echo 'selected'; } ?>>
										<?php echo ($status['name']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<!--start department select -->
							<div class="col-md-2 border-right">
								<label for="department" class="control-label"><?php echo _l('departments'); ?></label>
								<?php echo render_select('department',$departments,array('departmentid','name'),'',$department,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')),array(),'no-margin'); ?>
							</div>
							<!--end department select-->
							<!--start rel type-->
							<div class="col-md-2 border-right">
								<label for="rel_type" class="control-label"><?php echo _l('si_tf_ticket_related_to'); ?></label>
								<select name="rel_type" class="selectpicker" id="si_tf_rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<option value=""></option>
									<option value="project" <?php if(isset($rel_type)){if($rel_type == 'project'){echo 'selected';}} ?>><?php echo _l('project'); ?></option>
									<option value="customer" <?php if(isset($rel_type)){if($rel_type == 'customer'){echo 'selected';}} ?>><?php echo _l('client'); ?></option>
									
									
								</select>
							</div>
							<!--end of list of rel type-->
							<!--start rel_id select from rel_type-->
							<div class="col-md-2 border-right form-group<?php if($rel_id == '' && $rel_type==''){echo ' hide';} ?>" id="si_tf_rel_id_wrapper">
								<label for="rel_id" class="control-label"><span class="si_tf_rel_id_label"></span></label>
								<div id="si_tf_rel_id_select">
									<select name="rel_id" id="si_tf_rel_id" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<?php if($rel_id != '' && $rel_type != ''){
									$rel_data = get_relation_data($rel_type,$rel_id);
									$rel_val = get_relation_values($rel_data,$rel_type);
									echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
									if($group_by=='')
									$report_heading.=" - ".$rel_val['name'];
									} ?>
									</select>
								</div>
							</div>
							<!--end rel_id select-->
							<!--start group_id select from rel_id if rel_type is customer-->
							<div class="col-md-2 border-right form-group<?php if($rel_type !== 'customer'){echo ' hide';} ?>" id="si_tf_group_id_wrapper">
								<label for="group_id" class="control-label"><span class="control-label"><?php echo _l('customer_groups'); ?></span></label>
								<div id="group_id_select">
									<select name="group_id" id="group_id" class="selectpicker no-margin" data-width="100%" >
										<option value="" selected><?php echo _l('dropdown_non_selected_tex'); ?></option>
										<?php if(!empty($groups)){
											foreach($groups as $group)
											{
												echo '<option value="'.$group['id'].'" '.($group_id!='' && $group_id==$group['id']?'selected':'').'>'.$group['name'].'</option>';
												if($group_id==$group['id'])
													$report_heading.=" (Group:".$group['name'].")";
											}
											} 
										?>
									</select>
								</div>
							</div>
							<!--end group_id select-->
						</div>
						<div class="row">
							<!--start group_by select -->
							<!--start priority select -->
							<div class="col-md-2 border-right form-group">
								<label for="priority" class="control-label"><?php echo _l('ticket_form_priority'); ?></label>
								<?php echo render_select('priority',$priorities,array('priorityid','name'),'',$priority,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')),array(),'no-margin'); ?>
							</div>
							<!--end priority select-->
							<!--start services select -->
							<div class="col-md-2 border-right form-group">
								<label for="service" class="control-label"><?php echo _l('ticket_form_service'); ?></label>
								<?php echo render_select('service',$services,array('serviceid','name'),'',$service,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')),array(),'no-margin'); ?>
							</div>
							<!--end priority select-->
							<div class="col-md-2 form-group border-right" id="report-time">
								<label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
								<select class="selectpicker" name="report_months" id="report_months" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
									<option value="today"><?php echo _l('today'); ?></option>
									<option value="this_week"><?php echo _l('this_week'); ?></option>
									<option value="last_week"><?php echo _l('last_week'); ?></option>
									<option value="this_month"><?php echo _l('this_month'); ?></option>
									<option value="1"><?php echo _l('last_month'); ?></option>
									<option value="this_year"><?php echo _l('this_year'); ?></option>
									<option value="last_year"><?php echo _l('last_year'); ?></option>
									<option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
									<option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
									<option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
									<option value="custom"><?php echo _l('period_datepicker'); ?></option>
								</select>
								<?php
									if($report_months !== '')
									{
										$report_heading.=' for '._l('period_datepicker')." ";
										switch($report_months)
										{
											case 'today':$report_heading.=_d(date('d-m-Y'))." To "._d(date('d-m-Y'));break;
											case 'this_week':$report_heading.=_d(date('d-m-Y', strtotime('monday this week')))." To "._d(date('d-m-Y', strtotime('sunday this week')));break;
											case 'last_week':$report_heading.=_d(date('d-m-Y', strtotime('monday last week')))." To "._d(date('d-m-Y', strtotime('sunday last week')));break;
											case 'this_month':$report_heading.=_d(date('01-m-Y'))." To "._d(date('t-m-Y'));break;
											case '1'         :$report_heading.=_d(date('01-m-Y',strtotime('-1 month')))." To "._d(date('t-m-Y',strtotime('-1 month')));break;
											case 'this_year' :$report_heading.=_d(date('01-01-Y'))." To "._d(date('31-12-Y'));break;
											case 'last_year' :$report_heading.=_d(date('01-01-Y',strtotime('-1 year')))." To "._d(date('31-12-Y',strtotime('-1 year')));break;
											case '3'         :$report_heading.=_d(date('01-m-Y',strtotime('-2 month')))." To "._d(date('t-m-Y'));break;
											case '6'         :$report_heading.=_d(date('01-m-Y',strtotime('-5 month')))." To "._d(date('t-m-Y'));break;
											case '12'        :$report_heading.=_d(date('01-m-Y',strtotime('-11 month')))." To "._d(date('t-m-Y'));break;
											case 'custom'    :$report_heading.=$report_from." To ".$report_to;break;
											default          :$report_heading.='All Time';
										}
									}
								?>
							</div>
							<!--start filter_by select -->
							<div class="col-md-2 border-right form-group">
								<label for="date_by" class="control-label"><span class="control-label"><?php echo _l('si_tf_filter_by_date'); ?></span></label>
								<select name="date_by" id="date_by" class="selectpicker no-margin" data-width="100%" >
									<option value="date"><?php echo _l('si_tf_create_date'); ?></option>
									<option value="lastreply" <?php echo ($date_by!='' && $date_by=='lastreply'?'selected':'')?>><?php echo _l('si_tf_answered_date'); ?></option>
								</select>
							</div>
							<!--end filter_by select-->
							<div id="date-range" class="col-md-4 hide mbot15">
								<div class="row">
									<div class="col-md-6">
										<label for="report_from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="report_from" name="report_from" value="<?php echo ($report_from);?>" autocomplete="off">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
									<div class="col-md-6 border-right">
										<label for="report_to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="report_to" name="report_to" autocomplete="off">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!--end date time div-->
							<!--start tags -->
							<div class="col-md-2 text-center1 border-right">
								<label for="rel_type" class="control-label"><?php echo _l('tags'); ?></label>		
								<?php echo render_select('tags[]',get_tags(),array('id','name'),'',$tags,array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>false),array(),'no-mbot','',false);?>
							</div>
							<!--end tags-->
							<!--start group_by select -->
							<div class="col-md-2 border-right form-group">
								<label for="group_id" class="control-label"><span class="control-label"><?php echo _l('si_tf_group_by'); ?></span></label>
								<select name="group_by" id="group_by" class="selectpicker no-margin" data-width="100%">
									<option value="" selected><?php echo _l('dropdown_non_selected_tex'); ?></option>
									<option value="contact" <?php echo ($group_by!='' && $group_by=='contact'?'selected':'')?>><?php echo _l('contact'); ?></option>
									<option value="service" <?php echo ($group_by!='' && $group_by=='service'?'selected':'')?>><?php echo _l('ticket_dt_service'); ?></option>
									<option value="department_name" <?php echo ($group_by!='' && $group_by=='department_name'?'selected':'')?>><?php echo _l('ticket_dt_department'); ?></option>
									<option value="subject_name" <?php echo ($group_by!='' && $group_by=='subject_name'?'selected':'')?>><?php echo _l('ticket_dt_subject'); ?></option>
									<option value="status" <?php echo ($group_by!='' && $group_by=='status'?'selected':'')?>><?php echo _l('ticket_dt_status'); ?></option>
								</select>
							</div>
							<!--end group_by select-->
							<!--start hide_export_columns select -->
							<div class="col-md-2 border-right form-group">
								<label for="hide_columns" class="control-label">
									<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('si_tf_hide_export_columns_info');?>"></i>
									<span class="control-label"><?php echo _l('si_tf_hide_export_columns'); ?></span>
								</label>
								<select name="hide_columns[]" id="hide_columns" class="selectpicker no-margin" data-width="100%" multiple>
									<option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
									<option value="subject" <?php echo (in_array('subject',$hide_columns)?'selected':'')?>><?php echo _l('ticket_dt_subject'); ?></option>
									<option value="assigned" <?php echo (in_array('assigned',$hide_columns)?'selected':'')?>><?php echo _l('ticket_assigned'); ?></option>
									<option value="department" <?php echo (in_array('department',$hide_columns)?'selected':'')?>><?php echo _l('ticket_dt_department'); ?></option>
									<option value="service" <?php echo (in_array('service',$hide_columns)?'selected':'')?>><?php echo _l('ticket_dt_service'); ?></option>
									<option value="client" <?php echo (in_array('client',$hide_columns)?'selected':'')?>><?php echo _l('ticket_dt_submitter'); ?></option>
									<option value="status" <?php echo (in_array('status',$hide_columns)?'selected':'')?>><?php echo _l('ticket_dt_status'); ?></option>
									<option value="priority" <?php echo (in_array('priority',$hide_columns)?'selected':'')?>><?php echo _l('ticket_dt_priority'); ?></option>
									<option value="last_reply" <?php echo (in_array('last_reply',$hide_columns)?'selected':'')?>><?php echo _l('ticket_dt_last_reply'); ?></option>
									<option value="created_date" <?php echo (in_array('created_date',$hide_columns)?'selected':'')?>><?php echo _l('ticket_date_created'); ?></option>
									<?php
									$custom_fields = get_custom_fields('tickets', ['show_on_table' => 1,]);
									foreach($custom_fields as $field)
										echo "<option value='$field[slug]' ".(in_array($field['slug'],$hide_columns)?'selected':'').">$field[name]</option>";
									?>
									<option value="tags" <?php echo (in_array('tags',$hide_columns)?'selected':'')?>><?php echo _l('tags'); ?></option>
								</select>
							</div>
							<!--end hide_export_columns select-->
							<!--start save filter-->
							<div class="col-md-6">
								<div class="checklist relative">
									<div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="<?php echo _l('si_tf_save_filter_template'); ?>">
										<input type="checkbox" id="si_save_filter" name="save_filter" value="1" title="<?php echo _l('si_tf_save_filter_template'); ?>" <?php echo ($this->input->get('filter_id')?'checked':'')?>>
										<label for=""><span class="hide"><?php echo _l('si_tf_save_filter_template'); ?></span></label>
										<textarea id="si_filter_name" name="filter_name" rows="1" placeholder="<?php echo _l('si_tf_filter_template_name'); ?>" <?php echo ($this->input->get('filter_id')?'':'disabled="disabled"')?> maxlength='200'><?php echo ($this->input->get('filter_id')?$saved_filter_name:'');?></textarea>
									</div>
								</div>
							</div>
							<!--end save filter-->
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
					<?php
					foreach($overview as $month =>$data){ if(count($data) == 0){continue;} $no=1;?>
						<h4 class="bold text-success"><?php echo ($month); ?>
						<?php if($this->input->get('project_id')){ echo ' - ' . get_project_name_by_id($this->input->get('project_id'));} ?>
						<?php if(is_numeric($staff_id) && has_permission('si_ticket_filters','','view')) { echo ' ('.get_staff_full_name($staff_id).')';} ?>
						</h4>
						<table class="table dt-table scroll-responsive" data-order-col="0" data-order-type="desc">
							<caption class="si_caption"><?php echo ($month.$report_heading);?></caption>
							<thead>
								<tr>
									<th>#</th>
									<th class="<?php echo (in_array('subject',$hide_columns)?'not-export':'')?>"><?php echo _l('ticket_dt_subject'); ?></th>
									<th class="<?php echo (in_array('assigned',$hide_columns)?'not-export':'')?>"><?php echo _l('ticket_assigned'); ?></th>
									<th class="<?php echo (in_array('department',$hide_columns)?'not-export':'')?>"><?php echo _l('ticket_dt_department'); ?></th>
									<th class="<?php echo (in_array('service',$hide_columns)?'not-export':'')?>"><?php echo _l('ticket_dt_service')?></th>
									<th class="<?php echo (in_array('client',$hide_columns)?'not-export':'')?>"><?php echo _l('ticket_dt_submitter')?></th>
									<th class="<?php echo (in_array('status',$hide_columns)?'not-export ':'')?>"><?php echo _l('ticket_dt_status'); ?></th>
									<th class="<?php echo (in_array('priority',$hide_columns)?'not-export':'')?>"><?php echo _l('ticket_dt_priority'); ?></th>
									<th class="<?php echo (in_array('last_reply',$hide_columns)?'not-export':'')?>"><?php echo _l('ticket_dt_last_reply'); ?></th>
									<th class="<?php echo (in_array('created_date',$hide_columns)?'not-export':'')?>"><?php echo _l('ticket_date_created'); ?></th>
									
								<?php
									$custom_fields = get_custom_fields('tickets', ['show_on_table' => 1,]);
									foreach($custom_fields as $field)
									{
										echo '<th class="'.(in_array($field['slug'],$hide_columns)?'not-export':'').'">'.$field['name'].'</th>';	
									}
								?>
									<th class="<?php echo (in_array('tags',$hide_columns)?'not-export':'')?>"><?php echo _l('tags'); ?></th>
								</tr>
							</thead>
						<tbody>
							<?php
								foreach($data as $ticket){ ?>
								<tr class="<?php if ($ticket['adminread'] == 0) echo 'text-danger';?>">
									<td data-order="<?php echo $ticket['ticketid'];?>"><a href="<?php echo admin_url('tickets/ticket/'.$ticket['ticketid']); ?>"><?php echo $ticket['ticketid'];?></a></td>
									<td data-order="<?php echo htmlentities($ticket['subject']); ?>"><a href="<?php echo admin_url('tickets/ticket/'.$ticket['ticketid']); ?>"><?php echo ($ticket['subject']); ?></a>
									</td>
									<td>
									<?php if ($ticket['assigned'] != 0) {
										$full_name = get_staff_full_name($ticket['assigned']);
										echo '<a href="' . admin_url('profile/' . $ticket['assigned']) . '" data-toggle="tooltip" title="' . $full_name . '" class="pull-left mright5">' . staff_profile_image($ticket['assigned'], ['staff-profile-image-xs',]) . '<br/>'.$full_name.'</a>'; }?>
									</td>
									<td data-order="<?php echo ($ticket['department_name']); ?>"><?php echo ($ticket['department_name']); ?></td>
									<td data-order="<?php echo ($ticket['service_name']); ?>"><?php echo ($ticket['service_name']); ?></td>
									<td data-order="<?php echo ($ticket['service_name']); ?>">
									<?php
									if ($ticket['userid'] != 0) {
										echo '<a href="' . admin_url('clients/client/' . $ticket['userid'] . '?group=contacts') . '">' . $ticket['contact_full_name'].(!empty($ticket['rel_name'])?' (' . $ticket['rel_name'] . ')':'').'</a>';
									
									} else {
										echo ($ticket['name']);
									}?>
									</td>
									<td>
									<?php echo '<span class="label inline-block" style="border:1px solid ' . $ticket['statuscolor'] . '; color:' . $ticket['statuscolor'] . '">' . ticket_status_translate($ticket['status']) . '</span>';?>
									</td>
									<td><?php echo ticket_priority_translate($ticket['priority']);?></td>
									<td><?php echo ($ticket['lastreply']==null ? _l('ticket_no_reply_yet'):_dt($ticket['lastreply']));?></td>
									<td data-order="<?php echo ($ticket['date']); ?>"><?php echo _d($ticket['date']); ?></td>
									
									
								<?php
									foreach($custom_fields as $field)
									{
										$current_value = get_custom_field_value($ticket['ticketid'], $field['id'], 'tickets', false);
										echo '<td>'.(($field['type']=='date_picker' || $field['type']=='date_picker_time') && $current_value!='' ? date('d-m-Y',strtotime($current_value)):$current_value).'</td>';
									}
								?>
									
									<td><?php echo  render_tags(prep_tags_input(get_tags_in($ticket['ticketid'],'ticket'))); ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<hr />
					<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script src="<?php echo module_dir_url('si_ticket_filters','assets/js/si_ticket_filters_ticket_report.js'); ?>"></script>
<script>
(function($) {
"use strict";
<?php  if($report_months !== ''){ ?>
	$('#report_months').val("<?php echo ($report_months);?>");
	$('#report_months').change();		
<?php }
	if($report_from !== ''){ 
?>
	$('#report_from').val("<?php echo ($report_from);?>");
<?php
	}
	if($report_to !== ''){ 
?>
	$('#report_to').val("<?php echo ($report_to);?>");
<?php
	}
?>
})(jQuery);				  
</script>

