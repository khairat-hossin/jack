<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Human Resources Management
Description: Human Resource Management module for Perfex
Version: 2.3.0
Requires at least: 2.3.*
Author: Themesic Interactive
*/

define('HRM_MODULE_NAME', 'hrm');

define('HRM_MODULE_UPLOAD_FOLDER', module_dir_path(HRM_MODULE_NAME, 'uploads'));

hooks()->add_action('admin_init', 'hrm_permissions');
hooks()->add_action('app_admin_head', 'hrm_add_head_components');
hooks()->add_action('app_admin_footer', 'hrm_add_footer_components');
hooks()->add_action('admin_init', 'hrm_module_init_menu_items');

/**
* Register activation module hook
*/
register_activation_hook(HRM_MODULE_NAME, 'hrm_module_activation_hook');

function hrm_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(HRM_MODULE_NAME, [HRM_MODULE_NAME]);


$CI = & get_instance();
$CI->load->helper(HRM_MODULE_NAME . '/hrm');

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function hrm_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission('hrm', '', 'view')) {

        $CI->app_menu->add_sidebar_menu_item('HRM', [
                'name'     => _l('hrm'),
                'icon'     => 'fa fa-user-circle',
                'href'     => admin_url('#'),
        ]);
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_dashboard',
                'name'     => _l('dashboard'),
                'icon'     => 'fa fa-home',
                
                'href'     => admin_url('hrm'),
        ]);
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_staff',
                'name'     => _l('staff'),
                'icon'     => 'fa fa-address-book',
                
                'href'     => admin_url('hrm/staff_infor'),
        ]);
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_staff_contract',
                'name'     => _l('staff_contract'),
                'icon'     => 'fa fa-file',
                'href'     => admin_url('hrm/contracts'),
        ]);
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_insurrance',
                'name'     => _l('insurrance'),
                'icon'     => 'fa fa-medkit',
                'href'     => admin_url('hrm/insurances'),
        ]);
        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('HRM', [
                    'slug'     => 'hrm_timekeeping',
                    'name'     => _l('timekeeping'),
                    'icon'     => 'fa fa fa-pencil',
                    'href'     => admin_url('hrm/timekeeping'),
            ]);
        }

        if (is_admin()) {
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_payroll',
                'name'     => _l('payroll'),
                'icon'     => 'fa fa-dollar',
                'href'     => admin_url('hrm/payroll'),
        ]);
        }

        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('HRM', [
                    'slug'     => 'hrm_setting',
                    'name'     => _l('setting'),
                    'icon'     => 'fa fa-cog',
                    'href'     => admin_url('hrm/setting'),
            ]);
        }
    }
}


function hrm_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
            'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('hrm', $capabilities, _l('hrm'));
}


function hrm_add_head_components(){
    $CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	
	echo '<link href="' . module_dir_url('hrm','assets/css/style.css') .'"  rel="stylesheet" type="text/css" />';
	echo '<link href="' . module_dir_url('hrm','assets/plugins/ComboTree/style.css') .'"  rel="stylesheet" type="text/css" />';

	if ($viewuri == '/admin/hrm') {
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/highcharts/highcharts.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/highcharts/modules/variable-pie.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/highcharts/modules/export-data.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/highcharts/modules/accessibility.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/highcharts/modules/exporting.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/highcharts/highcharts-3d.js').'"></script>';
	}
	
	if ($viewuri == '/admin/hrm/timekeeping?group=allocate_shiftwork' || $viewuri == '/admin/hrm/payroll?group=payroll_type' || $viewuri == '/admin/hrm/timekeeping?group=table_shiftwork' || $viewuri == '/admin/hrm/insurances' || strpos($viewuri, 'payroll') !== false ) {
		echo '<script src="'.module_dir_url('hrm', 'assets/plugins/handsontable/handsontable.full.min.js').'"></script>';
		echo '<link href="' . base_url('modules/hrm/assets/plugins/handsontable/handsontable.full.min.css') .'"  rel="stylesheet" type="text/css" />';
	}

	if ($viewuri == '/admin/hrm/insurances') {
		echo '<link href="' . base_url('modules/hrm/assets/css/datepicker.css') .'"  rel="stylesheet" type="text/css" />';
	}
	
	if (strpos($viewuri, '/admin/hrm/member/') !== false) {
		echo '<link href="' . base_url('modules/hrm/assets/css/member.css') .'"  rel="stylesheet" type="text/css" />';
	}
	
	if ($viewuri == '/admin/hrm/payroll?group=payroll_type') {
		echo '<link href="' . base_url('modules/hrm/assets/css/newpayrolltype.css') .'"  rel="stylesheet" type="text/css" />';
	}
	
	if (strpos($viewuri, '/admin/hrm/payroll_table') !== false) {
		echo '<link href="' . base_url('modules/hrm/assets/css/newpayrolltable.css') .'"  rel="stylesheet" type="text/css" />';
	}
	
	if (strpos($viewuri, '/admin/hrm/profile/') !== false) {
		echo '<link href="' . base_url('modules/hrm/assets/css/profile.css') .'"  rel="stylesheet" type="text/css" />';
	}
	
}


function hrm_add_footer_components(){
    $CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];

	echo '<script src="'.module_dir_url('hrm', 'assets/plugins/ComboTree/comboTreePlugin.js').'"></script>';
    echo '<script src="'.module_dir_url('hrm', 'assets/plugins/ComboTree/icontains.js').'"></script>';

	if (strpos($viewuri, '/admin/hrm/setting?group=workplace') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/workplace.js').'"></script>';
	}

	if (strpos($viewuri, 'payslip') !== false || $viewuri == '/admin/hrm/payroll') {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/payslip.js').'"></script>';
	}
	
	if (strpos($viewuri, 'payroll') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/payroll.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/js/payrollincludes.js').'"></script>';
		echo '<script src="'.module_dir_url('hrm', 'assets/js/payslip.js').'"></script>';
	}
	
	if (strpos($viewuri, 'job_position') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/jobposition.js').'"></script>';
	}
	
	if (strpos($viewuri, 'contract_type') !== false || $viewuri == '/admin/hrm/setting') {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/contracttype.js').'"></script>';
	}
	
	if (strpos($viewuri, 'allowance_type') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/allowancetype.js').'"></script>';
	}
	
	if (strpos($viewuri, '/admin/hrm/member') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/member.js').'"></script>';
	}

	if (strpos($viewuri, '/admin/hrm/contract/') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/contract.js').'"></script>';
	}

	if (strpos($viewuri, 'manage_staff') !== false || $viewuri == '/admin/hrm/staff_infor') {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/managestaff.js').'"></script>';
	}
	
	if (strpos($viewuri, 'manage_setting') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/managesetting.js').'"></script>';
	}
	
	if (strpos($viewuri, 'manage_dayoff') !== false || strpos($viewuri, 'timekeeping') !== false) {
		echo '<script src="'.module_dir_url('hrm', 'assets/js/managedayoff.js').'"></script>';
	}
}


hooks()->add_action('app_init',HRM_MODULE_NAME.'_actLib');
function hrm_actLib()
{
    $CI = & get_instance();
    $CI->load->library(HRM_MODULE_NAME.'/Envapi');
    $envato_res = $CI->envapi->validatePurchase(HRM_MODULE_NAME);
    if (!$envato_res) {
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }
}

hooks()->add_action('pre_activate_module', HRM_MODULE_NAME.'_sidecheck');
function hrm_sidecheck($module_name)
{
    if ($module_name['system_name'] == HRM_MODULE_NAME) {
        if (!option_exists(HRM_MODULE_NAME.'_verified') && empty(get_option(HRM_MODULE_NAME.'_verified')) && !option_exists(HRM_MODULE_NAME.'_verification_id') && empty(get_option(HRM_MODULE_NAME.'_verification_id'))) {
            $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/env_ver/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.HRM_MODULE_NAME); 
            $data['module_name'] = HRM_MODULE_NAME; 
            $data['title'] = "Module activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }
    }
}

hooks()->add_action('pre_deactivate_module', HRM_MODULE_NAME.'_deregister');
function hrm_deregister($module_name)
{
    if ($module_name['system_name'] == HRM_MODULE_NAME) {
        delete_option(HRM_MODULE_NAME."_verified");
        delete_option(HRM_MODULE_NAME."_verification_id");
        delete_option(HRM_MODULE_NAME."_last_verification");
        if(file_exists(__DIR__."/config/token.php")){
            unlink(__DIR__."/config/token.php");
        }
    }
}
