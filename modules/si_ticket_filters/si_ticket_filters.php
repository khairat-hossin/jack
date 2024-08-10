<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Advanced Support Ticket Filters
Description: Module will Generate Advanced Support Ticket Filters and save filters as Templates for future use.
Author: Sejal Infotech
Version: 1.0.5
Requires at least: 2.3.*
Author URI: http://www.sejalinfotech.com
*/

define('SI_TICKET_FILTERS_MODULE_NAME', 'si_ticket_filters');

$CI = &get_instance();

hooks()->add_action('admin_init', 'si_ticket_filters_init_menu_items');

/**
* Load the module model
*/
$CI->load->model(SI_TICKET_FILTERS_MODULE_NAME . '/si_ticket_filter_model');

/**
* Register activation module hook
*/
register_activation_hook(SI_TICKET_FILTERS_MODULE_NAME, 'si_ticket_filters_activation_hook');

function si_ticket_filters_activation_hook()
{
    $CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(SI_TICKET_FILTERS_MODULE_NAME, [SI_TICKET_FILTERS_MODULE_NAME]);

/**
 * Init menu setup module menu items in setup in admin_init hook
 * @return null
 */
function si_ticket_filters_init_menu_items()
{
	/**
	* If the logged in user is administrator, add custom Reports in Sidebar, if want to add menu in Setup then Write Setup instead of sidebar in menu ceation
	*/
	/** Add Menu for Ticket Report**/
	if (is_admin() || has_permission('si_ticket_filters', '', 'view') || has_permission('si_ticket_filters', '', 'view_own')) {
		$CI = &get_instance();
		$CI->app_menu->add_sidebar_menu_item('si-ticket-filters', [
			'collapse' => true,
			'icon'     => 'fa fa-filter',
			'name'     => _l('si_ticket_filters'),
			'position' => 35,
		]);
		$CI->app_menu->add_sidebar_children_item('si-ticket-filters', [
			'slug'     => 'tickets-report-options',
			'name'     => _l('si_tf_filters_menu'),
			'href'     => admin_url('si_ticket_filters/tickets_report'),
			'position' => 5,
		]);
		$CI->app_menu->add_sidebar_children_item('si-ticket-filters', [
			'slug'     => 'ticket-list-report-options',
			'name'     => _l('si_tf_templates_menu'),
			'href'     => admin_url('si_ticket_filters/list_filters'),
			'position' => 10,
		]);
	}
	
	/*Add customer permissions */
	$capabilities = [];
	$capabilities['capabilities'] = [
		'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
		'view_own' => _l('permission_view_own'),
	];
    register_staff_capabilities('si_ticket_filters', $capabilities, _l('si_ticket_filters'));
}

