<?php defined('BASEPATH') or exit('No direct script access allowed'); 
?>
<?php 
$file_header = array();
$file_header[] = _l('name');
$file_header[] = _l('type');
$file_header[] = _l('user_name');
$file_header[] = _l('password');
$file_header[] = _l('url');
$file_header[] = _l('bank_code');
$file_header[] = _l('bank_name');
$file_header[] = _l('pin');
$file_header[] = _l('host');
$file_header[] = _l('port');
$file_header[] = _l('smtp_host');
$file_header[] = _l('smtp_port');
$file_header[] = _l('smtp_user_name');
$file_header[] = _l('smtp_password');
$file_header[] = _l('credit_card_type');
$file_header[] = _l('card_number');
$file_header[] = _l('card_cvc');
$file_header[] = _l('license_key');
$file_header[] = _l('category');
$file_header[] = _l('email_type');
$file_header[] = _l('auth_method');
?>

<div id ="dowload_file_sample">
</div>

<?php if(!isset($simulate)) { ?>
	<ul>
		<li class="text-danger">1. <?php echo _l('file_xlsx_password'); ?></li>
		<li class="text-danger">2. <?php echo _l('file_xlsx_tp'); ?></li>
		<li class="text-danger">3. <?php echo _l('file_xlsx_type_tp'); ?></li>
		<li class="text-danger">4. <?php echo _l('file_xlsx_format_tp'); ?></li>
	</ul>
	<div class="table-responsive no-dt">
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<?php
					$total_fields = 0;

					for($i=0;$i<count($file_header);$i++){
						if($i == 0  ||$i == 1){
							?>
							<th class="bold"><span class="text-danger">*</span> <?php echo html_entity_decode($file_header[$i]) ?> </th>
							<?php 
						} else {
							?>
							<th class="bold"><?php echo html_entity_decode($file_header[$i]) ?> </th>
							<?php
						} 
						$total_fields++;
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php for($i = 0; $i<1;$i++){
					echo '<tr>';
					for($x = 0; $x<count($file_header);$x++){
						echo '<td>- </td>';
					}
					echo '</tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<hr>

<?php } ?>

<div class="row">
	<div class="col-md-4">
		<?php echo form_open_multipart(admin_url('team_password/import_password_excel'),array('id'=>'import_form')) ;?>
		<?php echo form_hidden('leads_import','true'); ?>
		<?php echo render_input('file_csv','choose_excel_file','','file'); ?> 

		<div class="form-group">
			<button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploadfilecsv();" ><?php echo _l('import'); ?></button>
		</div>
		<?php echo form_close(); ?>
	</div>
	<div class="col-md-8">
		<div class="form-group" id="file_upload_response">
		</div>
	</div>
</div>