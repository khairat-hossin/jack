<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_109 extends App_module_migration
{
    public function up()
    {  	
    	$CI = &get_instance();
     	if (!$CI->db->field_exists('unlimited' ,db_prefix() . 'tp_share')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_share`
		  ADD COLUMN `unlimited` int(1) NULL'
		  );
		}

		if ($CI->db->field_exists('valid_from' ,db_prefix() . 'tp_credit_card')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_credit_card`
		  CHANGE COLUMN `valid_from` `valid_from` DATE NULL ;');
		}

		if ($CI->db->field_exists('valid_to' ,db_prefix() . 'tp_credit_card')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_credit_card`
		  CHANGE COLUMN `valid_to` `valid_to` DATE NULL ;');
		}

		if ($CI->db->field_exists('valid_from' ,db_prefix() . 'tp_email')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_email`
		  CHANGE COLUMN `valid_from` `valid_from` DATE NULL ;');
		}

		if ($CI->db->field_exists('valid_to' ,db_prefix() . 'tp_email')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_email`
		  CHANGE COLUMN `valid_to` `valid_to` DATE NULL ;');
		}

		if (!$CI->db->field_exists('customer_group' ,db_prefix() . 'tp_share')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_share`
		  ADD COLUMN `customer_group` int(11) NULL'
		  );
		}

		if (!$CI->db->field_exists('send_notify' ,db_prefix() . 'tp_share')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_share`
		  ADD COLUMN `send_notify` int(1) NULL default "1" '
		  );
		}

		if (row_tp_options_exist('"contact_can_add_password"') == 0){
		  $CI->db->query('INSERT INTO `' . db_prefix() . 'options` (`name`, `value`, `autoload`) VALUES ("contact_can_add_password", "0", "1");
		');
		}

		if (!$CI->db->field_exists('add_by' ,db_prefix() . 'tp_normal')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_normal`
		  ADD COLUMN `add_by` VARCHAR(20) NOT NULL DEFAULT "staff"');
		}

		if (!$CI->db->field_exists('add_by' ,db_prefix() . 'tp_bank_account')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_bank_account`
		  ADD COLUMN `add_by` VARCHAR(20) NOT NULL DEFAULT "staff"');
		}

		if (!$CI->db->field_exists('add_by' ,db_prefix() . 'tp_credit_card')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_credit_card`
		  ADD COLUMN `add_by` VARCHAR(20) NOT NULL DEFAULT "staff"');
		}

		if (!$CI->db->field_exists('add_by' ,db_prefix() . 'tp_email')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_email`
		  ADD COLUMN `add_by` VARCHAR(20) NOT NULL DEFAULT "staff"');
		}

		if (!$CI->db->field_exists('add_by' ,db_prefix() . 'tp_server')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_server`
		  ADD COLUMN `add_by` VARCHAR(20) NOT NULL DEFAULT "staff"');
		}

		if (!$CI->db->field_exists('add_by' ,db_prefix() . 'tp_software_license')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'tp_software_license`
		  ADD COLUMN `add_by` VARCHAR(20) NOT NULL DEFAULT "staff"');
		}

		create_email_template('Share password', '<span style=\"font-size: 12pt;\"> Hello {contact_name}. </span><br /><br /><span style=\"font-size: 12pt;\"> We would like to share with you list of passwords </span><br /><br /><span style=\"font-size: 12pt;\"><br />Please click on the link to view information: {link} </span><br /><br />', 'teampassword', 'Teampassword mail to new contact', 'team-password-mail-to-new-contact');
    }
}
