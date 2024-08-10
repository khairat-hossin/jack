<?php

/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Google Authenticator
Description: Google authenticator for 2FA
Version: 1.0.0
Author: Shahid
Requires at least: 2.3.*
*/

// require(__DIR__ . '/vendor/autoload.php');

if (!defined('MODULE_GOOGLE_AUTHENTICATOR')) {
	define('MODULE_GOOGLE_AUTHENTICATOR', basename(__DIR__));
}

hooks()->add_action('after_staff_login', 'google_authenticator_after_staff_login');

/**
* Register activation module hook
*/
register_activation_hook(MODULE_GOOGLE_AUTHENTICATOR, 'google_authenticator_module_activation_hook');

function google_authenticator_module_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}
/**

 * Register uninstall module hook

 */

register_uninstall_hook(MODULE_GOOGLE_AUTHENTICATOR, 'google_authenticator_module_uninstall_hook');

function google_authenticator_module_uninstall_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/uninstall.php');
}

register_language_files(MODULE_GOOGLE_AUTHENTICATOR, [MODULE_GOOGLE_AUTHENTICATOR]);

function google_authenticator_after_staff_login()
{
	// assets/css/style.min.css
	// $CI = &get_instance();
	// $CI->app_css->add(MODULE_GOOGLE_AUTHENTICATOR.'-style-css', base_url('assets/css/style.min.css?v='.time()));

	redirect(admin_url('google_authenticator'));
	exit;
}