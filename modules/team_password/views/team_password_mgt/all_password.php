<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="clearfix"></div>
<div class="col-md-12">
	<div class="row">
		<?php $this->load->model('team_password/team_password_model');
		$user = get_staff_user_id();
		$items['bank_account'] = $this->team_password_model->get_item_tab_all($cate, 'bank_account');
		$items['credit_card'] = $this->team_password_model->get_item_tab_all($cate, 'credit_card');
		$items['email'] = $this->team_password_model->get_item_tab_all($cate, 'email');
		$items['normal'] = $this->team_password_model->get_item_tab_all($cate, 'normal');
		$items['server'] = $this->team_password_model->get_item_tab_all($cate, 'server');
		$items['software_license'] = $this->team_password_model->get_item_tab_all($cate, 'software_license');
		?>
		<div class="col-md-12">
			<hr class="hr-panel-heading" />
			<div class="clearfix"></div>
			<table class="table dt-table scroll-responsive">
				<thead>
					<th><?php echo _l('name'); ?></th>
					<th><?php echo _l('category_managements'); ?></th>
					<th><?php echo _l('type'); ?></th>
					<th><?php echo _l('add_from'); ?></th>
					<th><?php echo _l('notice'); ?></th>
					<th><?php echo _l('options'); ?></th>
				</thead>
				<tbody>
					<?php foreach($items as $type => $value){ ?>
						<?php foreach($value as $val){ 
							$url = admin_url('team_password/view_'.$type.'/'.$val['id']);
							?>
							<tr>
								<td><?php echo html_entity_decode($val['name']); ?></td>
								<td><?php echo get_category_name_tp($val['mgt_id']); ?></td>
								<td><?php echo _l($type); ?></td>
								<td><?php if($val['add_by'] == 'staff'){
									echo  _l('staff').': '.get_staff_full_name($val['add_from']); 
								}else{
									echo  _l('contact').': '.get_contact_full_name($val['add_from']);
								}
								?></td>
								<td><?php echo html_entity_decode($val['notice']); ?></td>
								<td>
									<?php
									$password = $val['password'];
									if($type == 'bank_account'){
										$password = AES_256_Decrypt($val['pin']);
									}
									if($type == 'credit_card'){
										$password = AES_256_Decrypt($val['pin']);
									}
									if($type == 'email'){
										$password = AES_256_Decrypt($val['password']);
									}
									if($type == 'normal'){
										$password = AES_256_Decrypt($val['password']);
									};
									if($type == 'server'){
										$password = AES_256_Decrypt($val['password']);
									}
									if($type == 'software_license'){
										$password = $val['license_key'];
									}
									$option = '';
									if(is_admin()){
										$option .= '<a href="' . admin_url('team_password/view_'.$type.'/'.$val['id']) . '" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="'._l('view').'" >';
										$option .= '<i class="fa fa-eye"></i>';
										$option .= '</a>';
										$option .= '<a href="' . admin_url('team_password/add_'.$type.'/'.$val['id']) . '" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="'._l('edit').'" >';
										$option .= '<i class="fa fa-pencil-square"></i>';
										$option .= '</a>';

										$option .= '<a href="#" data-password="'.$password.'" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="'._l('copy_password_to_clipboard').'" onclick="copyToClipboard(this)">';
										$option .= '<i class="fa fa-copy"></i>';
										$option .= '</a>';

										$option .= '<a href="' . admin_url('team_password/delete_'.$type.'/'.$val['id'].'/'.$cate) . '" class="btn btn-danger btn-icon _delete" data-toggle="tooltip" data-placement="top" title="'._l('delete').'">';
										$option .= '<i class="fa fa-remove"></i>';
										$option .= '</a>';
									}
									else{
										if(has_permission('team_password','','view') || ($val['add_from'] == get_staff_user_id() && $val['add_by'] == "staff")){
											$option .= '<a href="' . admin_url('team_password/view_'.$type.'/'.$val['id']) . '" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="'._l('view').'">';
											$option .= '<i class="fa fa-eye"></i>';
											$option .= '</a>';


											if(has_permission('team_password','','edit') || ($val['add_from'] == get_staff_user_id() && $val['add_by'] == "staff")){
												$option .= '<a href="' . admin_url('team_password/add_'.$type.'/'.$val['id']) . '" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="'._l('edit').'" >';
												$option .= '<i class="fa fa-pencil-square"></i>';
												$option .= '</a>';
											}											
											$option .= '<a href="#" data-password="'.$password.'" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="'._l('copy_password_to_clipboard').'" onclick="copyToClipboard(this)">';
											$option .= '<i class="fa fa-copy"></i>';
											$option .= '</a>';										
										}else{

											if(get_permission(''.$type.'',$val['id'],'r') == 1 &&!get_permission(''.$type.'',$val['id'],'w') == 1){
												$option .= '<a href="' . admin_url('team_password/view_'.$type.'/'.$val['id']) . '" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="'._l('view').'" >';
												$option .= '<i class="fa fa-eye"></i>';
												$option .= '</a>';
												$option .= '<a href="#" data-password="'.$password.'" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="'._l('copy_password_to_clipboard').'" onclick="copyToClipboard(this)">';
												$option .= '<i class="fa fa-copy"></i>';
												$option .= '</a>';
											}
											elseif(get_permission(''.$type.'',$val['id'],'rw') == 1 ||get_permission(''.$type.'',$val['id'],'w') == 1){
												$option .= '<a href="' . admin_url('team_password/view_'.$type.'/'.$val['id']) . '" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="'._l('view').'">';
												$option .= '<i class="fa fa-eye"></i>';
												$option .= '</a>';
												$option .= '<a href="' . admin_url('team_password/add_'.$type.'/'.$val['id']) . '" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="'._l('edit').'" >';
												$option .= '<i class="fa fa-pencil-square"></i>';
												$option .= '</a>';
												$option .= '<a href="#" data-password="'.$password.'" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="top" title="'._l('copy_password_to_clipboard').'" onclick="copyToClipboard(this)">';
												$option .= '<i class="fa fa-copy"></i>';
												$option .= '</a>';
											}
										}

										if(has_permission('team_password','','delete')){
											$option .= '<a href="' . admin_url('team_password/delete_'.$type.'/'.$val['id'].'/'.$cate) . '" class="btn btn-danger btn-icon _delete" data-toggle="tooltip" data-placement="top" title="'._l('delete').'">';
											$option .= '<i class="fa fa-remove"></i>';
											$option .= '</a>';
										}
									}

									echo $option;
									?>
								</td>
							</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>


