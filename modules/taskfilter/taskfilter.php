<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Task filter
Description: Module of status management, work issues
Module URI: https://codecanyon.net/item/advanced-task-filters-module-for-perfex-crm/26773517
Version: 1.0.0
Requires at least: 2.3.*
*/

define('TASKFILTER_MODULE', 'taskfilter');
require_once __DIR__.'/vendor/autoload.php';
// modules\taskfilter\core\Apiinit::the_da_vinci_code(TASKFILTER_MODULE);
// modules\taskfilter\core\Apiinit::ease_of_mind(TASKFILTER_MODULE);

hooks()->add_action('admin_init', 'taskfilter_permissions');
hooks()->add_action('admin_init', 'taskfilter_module_init_menu_items');
hooks()->add_filter('get_dashboard_widgets', 'taskfilter_add_dashboard_widget');



/**
* Register activation module hook
*/
register_activation_hook(TASKFILTER_MODULE, 'taskfilter_module_activation_hook');

function taskfilter_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

function taskfilter_add_dashboard_widget($widgets)
{
    $widgets[] = [
            'path'      => 'taskfilter/taskfilter_widget',
            'container' => 'top-12',
        ];

    return $widgets;
}
/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(TASKFILTER_MODULE, [TASKFILTER_MODULE]);


$CI = & get_instance();
/*$CI->load->helper(TASKFILTER_MODULE . '/taskfilter');*/

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function taskfilter_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission('taskfilter', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('taskfilter', [
                'name'     =>_l('taskfilter'),
                'href'     => admin_url('taskfilter'),
                'icon'     => 'fa fa-filter',
                'position' => 6,
            ]);
    }
}

function taskfilter_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
            'view'   => _l('permission_view'),
    ];

    register_staff_capabilities('taskfilter', $capabilities, _l('taskfilter'));
}

// hooks()->add_action('app_init', TASKFILTER_MODULE.'_actLib');
function taskfilter_actLib()
{
    $CI = &get_instance();
    $CI->load->library(TASKFILTER_MODULE.'/Taskfilter_aeiou');
    $envato_res = $CI->taskfilter_aeiou->validatePurchase(TASKFILTER_MODULE);
    if (!$envato_res) {
        set_alert('danger', 'One of your modules failed its verification and got deactivated. Please reactivate or contact support.');
    }
}

// hooks()->add_action('pre_activate_module', TASKFILTER_MODULE.'_sidecheck');
function taskfilter_sidecheck($module_name)
{
    if (TASKFILTER_MODULE == $module_name['system_name']) {
        modules\taskfilter\core\Apiinit::activate($module_name);
    }
}

// hooks()->add_action('pre_deactivate_module', TASKFILTER_MODULE.'_deregister');
function taskfilter_deregister($module_name)
{
    if (TASKFILTER_MODULE == $module_name['system_name']) {
        delete_option(TASKFILTER_MODULE.'_verification_id');
        delete_option(TASKFILTER_MODULE.'_last_verification');
        delete_option(TASKFILTER_MODULE.'_product_token');
        delete_option(TASKFILTER_MODULE.'_heartbeat');
    }
}
