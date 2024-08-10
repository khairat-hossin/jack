<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
Module Name: Stripe Checkout
Description: Recurring Invoice payment by stripe checkout
Author: Protovo Solutions
Version: 3.0.4
Requires at least: 3.0.*
*/

define('STRIPE_MODULE_NAME', 'stripe_checkout');

hooks()->add_action('app_customers_head', 'stripe_module_load_js');
register_activation_hook(STRIPE_MODULE_NAME, 'stripe_module_activation_hook');
register_deactivation_hook(STRIPE_MODULE_NAME,'stripe_module_deactivation_hook');
register_uninstall_hook(STRIPE_MODULE_NAME,'stripe_module_uninstall_hook');

function stripe_module_load_js()
{
  require_once('modules/'.STRIPE_MODULE_NAME.'/views/custom_js.php');
  echo '<link href="'.module_dir_url(STRIPE_MODULE_NAME, 'assets/css/custom.css?version='.time()).'" type="text/css" rel="stylesheet" >';
}

function stripe_module_activation_hook()
{
    $CI = &get_instance();
    $CI->load->helper('file');

    $new_view_file = APPPATH.'libraries/Stripe_core.php';
    $module_view_file = FCPATH.'modules/stripe_checkout/Stripe_core.php';
    copy($module_view_file, $new_view_file) or die("Unable to backup");

	  $new_view_file1 = APPPATH.'libraries/gateways/Stripe_gateway.php';
    $module_view_file1 = FCPATH.'modules/stripe_checkout/Stripe_gateway.php';
    copy($module_view_file1, $new_view_file1) or die("Unable to backup");

    $new_view_file2 = APPPATH.'models/Cron_model.php';
    $module_view_file2 = FCPATH.'modules/stripe_checkout/Cron_model.php';
    copy($module_view_file2, $new_view_file2) or die("Unable to backup");

    $new_view_file3 = APPPATH.'libraries/Stripe_core_gpw.php';
    $module_view_file3 = FCPATH.'modules/stripe_checkout/Stripe_core_gpw.php';
    copy($module_view_file3, $new_view_file3) or die("Unable to backup");

    $new_view_file4 = APPPATH.'libraries/gateways/Stripe_gateway_account2.php';
    $module_view_file4 = FCPATH.'modules/stripe_checkout/Stripe_gateway_account2.php';
    copy($module_view_file4, $new_view_file4) or die("Unable to backup");

    $new_view_file5 = APPPATH.'controllers/gateways/Stripe.php';
    $module_view_file5 = FCPATH.'modules/stripe_checkout/Stripe.php';
    copy($module_view_file5, $new_view_file5) or die("Unable to backup");

    $new_view_file6 = APPPATH.'views/themes/perfex/views/credit_card.php';
  $module_view_file6 = FCPATH.'modules/stripe_checkout/credit_card.php';
  copy($module_view_file6, $new_view_file6) or die("Unable to backup");

  $new_view_file7 = APPPATH.'views/themes/perfex/template_parts/navigation.php';
  $module_view_file7 = FCPATH.'modules/stripe_checkout/navigation.php';
  copy($module_view_file7, $new_view_file7) or die("Unable to backup");

  $new_view_file8 = APPPATH.'controllers/Clients.php';
  $module_view_file8 = FCPATH.'modules/stripe_checkout/Clients.php';
  copy($module_view_file8, $new_view_file8) or die("Unable to backup");

   require_once(__DIR__ . '/install.php');
}

function stripe_module_deactivation_hook()
{
  $CI = &get_instance();
  $CI->load->helper('file');

  $new_view_file = APPPATH.'libraries/Stripe_core.php';
  $module_view_file = FCPATH.'modules/stripe_checkout/backups/backup_Stripe_core.php';
  copy($module_view_file, $new_view_file) or die("Unable to backup");

  $new_view_file1 = APPPATH.'libraries/gateways/Stripe_gateway.php';
  $module_view_file1 = FCPATH.'modules/stripe_checkout/backups/backup_Stripe_gateway.php';
  copy($module_view_file1, $new_view_file1) or die("Unable to backup");

  $new_view_file2 = APPPATH.'models/Cron_model.php';
  $module_view_file2 = FCPATH.'modules/stripe_checkout/backups/backup_Cron_model.php';
  copy($module_view_file2, $new_view_file2) or die("Unable to backup");

  $new_view_file3 = APPPATH.'libraries/Stripe_core_gpw.php';
  $module_view_file3 = FCPATH.'modules/stripe_checkout/backups/backup_Stripe_core_gpw.php';
  copy($module_view_file3, $new_view_file3) or die("Unable to backup");

  $new_view_file4 = APPPATH.'libraries/gateways/Stripe_gateway_account2.php';
  $module_view_file4 = FCPATH.'modules/stripe_checkout/backups/backup_Stripe_gateway_account2.php';
  copy($module_view_file4, $new_view_file4) or die("Unable to backup");

  $new_view_file5 = APPPATH.'controllers/gateways/Stripe.php';
  $module_view_file5 = FCPATH.'modules/stripe_checkout/backups/backup_Stripe.php';
  copy($module_view_file5, $new_view_file5) or die("Unable to backup");

  $new_view_file6 = APPPATH.'views/themes/perfex/views/credit_card.php';
  $module_view_file6 = FCPATH.'modules/stripe_checkout/backups/backup_credit_card.php';
  copy($module_view_file6, $new_view_file6) or die("Unable to backup");

  $new_view_file7 = APPPATH.'views/themes/perfex/template_parts/navigation.php';
  $module_view_file7 = FCPATH.'modules/stripe_checkout/backups/backup_navigation.php';
  copy($module_view_file7, $new_view_file7) or die("Unable to backup");

  $new_view_file8 = APPPATH.'controllers/Clients.php';
  $module_view_file8 = FCPATH.'modules/stripe_checkout/backups/backup_Clients.php';
  copy($module_view_file8, $new_view_file8) or die("Unable to backup");
}
function stripe_module_uninstall_hook()
{
  $CI = &get_instance();
  $CI->load->helper('file');

  $new_view_file = APPPATH.'libraries/Stripe_core.php';
  $module_view_file = FCPATH.'modules/stripe_checkout/backups/backup_Stripe_core.php';
  copy($module_view_file, $new_view_file) or die("Unable to backup");

  $new_view_file1 = APPPATH.'libraries/gateways/Stripe_gateway.php';
  $module_view_file1 = FCPATH.'modules/stripe_checkout/backups/backup_Stripe_gateway.php';
  copy($module_view_file1, $new_view_file1) or die("Unable to backup");

  $new_view_file2 = APPPATH.'models/Cron_model.php';
  $module_view_file2 = FCPATH.'modules/stripe_checkout/backups/backup_Cron_model.php';
  copy($module_view_file2, $new_view_file2) or die("Unable to backup");

  $new_view_file3 = APPPATH.'libraries/Stripe_core_gpw.php';
  $module_view_file3 = FCPATH.'modules/stripe_checkout/backups/backup_Stripe_core_gpw.php';
  copy($module_view_file3, $new_view_file3) or die("Unable to backup");

  $new_view_file4 = APPPATH.'libraries/gateways/Stripe_gateway_account2.php';
  $module_view_file4 = FCPATH.'modules/stripe_checkout/backups/backup_Stripe_gateway_account2.php';
  copy($module_view_file4, $new_view_file4) or die("Unable to backup");

  $new_view_file5 = APPPATH.'controllers/gateways/Stripe.php';
  $module_view_file5 = FCPATH.'modules/stripe_checkout/backups/backup_Stripe.php';
  copy($module_view_file5, $new_view_file5) or die("Unable to backup");

  $new_view_file6 = APPPATH.'views/themes/perfex/views/credit_card.php';
  $module_view_file6 = FCPATH.'modules/stripe_checkout/backups/backup_credit_card.php';
  copy($module_view_file6, $new_view_file6) or die("Unable to backup");

  $new_view_file7 = APPPATH.'views/themes/perfex/template_parts/navigation.php';
  $module_view_file7 = FCPATH.'modules/stripe_checkout/backups/backup_navigation.php';
  copy($module_view_file7, $new_view_file7) or die("Unable to backup");

  $new_view_file8 = APPPATH.'controllers/Clients.php';
  $module_view_file8 = FCPATH.'modules/stripe_checkout/backups/backup_Clients.php';
  copy($module_view_file8, $new_view_file8) or die("Unable to backup");
}

register_language_files(STRIPE_MODULE_NAME, [STRIPE_MODULE_NAME]);
