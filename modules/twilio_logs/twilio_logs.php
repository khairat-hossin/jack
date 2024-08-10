<?php

/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Auto Login
Description: It will auto login with only passing email
Version: 1.0.0
Author: Shahid
Author URI: https://globalpresence.support/
Requires at least: 2.3.*
*/

if (!defined('MODULE_AUTO_LOGIN')) {
	define('MODULE_AUTO_LOGIN', basename(__DIR__));
}

$CI = &get_instance();
hooks()->add_action('admin_init', 'whmcs_client_portal_init_menu_item');
hooks()->add_filter('tasks_related_table_columns', 'tasks_column_addition_function');

/**
* Register activation module hook
*/
register_activation_hook(MODULE_AUTO_LOGIN, 'auto_login_module_activation_hook');

function auto_login_module_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}
/**

 * Register uninstall module hook

 */

register_uninstall_hook(MODULE_LEAD_MANAGER, 'auto_login_module_uninstall_hook');

function auto_login_module_uninstall_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/uninstall.php');
}

register_language_files(MODULE_AUTO_LOGIN, [MODULE_AUTO_LOGIN]);


/**
 * Init api module menu items in setup in admin_init hook
 * @return null
 */
function whmcs_client_portal_init_menu_item()
{
	$CI = &get_instance();
	$CI->app_menu->add_sidebar_menu_item('whmcs_client_portal', [
		'name'     => _l('WHMCS Client Portal'),
		'position' => 1,
		'icon'     => 'fa fa-cogs',
		'href'     => 'https://globalpresence.support/admin',
	]);

	/* $CI->app_menu->add_sidebar_menu_item('HRM', [
		'name'     => _l('hrm'),
		'icon'     => 'fa fa-user-circle',
		'href'     => admin_url('#'),
]); */
}

function tasks_column_addition_function($table_data){
	array_splice( $table_data, 4, 0, array('name' => 'Milestone') ); // splice in at position 4
//	echo '<pre>'; print_r($table_data); exit;
	return $table_data;
}