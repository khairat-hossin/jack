<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: @Mention
Description: Use @ to mention someone, use # to refer to objects such as projects, tasks, contracts, invoices, supports, ... Can use multiple @ or #.
Version: 1.0.2
Requires at least: 2.3.*
Author: GreenTech Solutions
*/


define('MENTION_MODULE_NAME', 'mention');

hooks()->add_action('admin_init', 'mention_module_init_menu_items');
hooks()->add_action('admin_init', 'mention_permissions');
hooks()->add_action('app_admin_head', 'mention_add_head_components');
hooks()->add_action('app_admin_footer', 'mention_add_footer_components');
hooks()->add_action('customers_navigation_end', 'mention_module_init_client_menu_items');
hooks()->add_action('app_customers_head', 'mention_client_add_head_components');
hooks()->add_action('app_customers_footer', 'mention_client_add_footer_components');


/**
* Register activation module hook
*/
register_activation_hook(MENTION_MODULE_NAME, 'mention_module_activation_hook');

/**
* Functions of the module
*/
function mention_add_head_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if(!(strpos($viewuri, '/admin/mention') === false)){
        echo '<link href="' . base_url('modules/mention/assets/css/styles.css') .'"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . base_url('modules/mention/assets/css/jquery.atwho.css') .'"  rel="stylesheet" type="text/css" />';        
        echo '<link href="' . base_url('modules/mention/assets/css/jquery.mentionsInput.css') .'"  rel="stylesheet" type="text/css" />';        
        echo "<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>";    
    }
}

function mention_add_footer_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if(!(strpos($viewuri, '/admin/mention') === false)){
        echo '<script src="' . base_url('modules/mention/assets/js/mention.js') . '"></script>';
        echo '<script src="' . base_url('modules/mention/assets/third-party/jquery.caret.js') . '"></script>';
        echo '<script src="' . base_url('modules/mention/assets/third-party/jquery.atwho.js') . '"></script>';  
    }
}

function mention_client_add_head_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if(!(strpos($viewuri, '/mention') === false)){
        echo '<link href="' . base_url('modules/mention/assets/css/styles.css') .'"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . base_url('modules/mention/assets/css/jquery.atwho.css') .'"  rel="stylesheet" type="text/css" />';        
        echo '<link href="' . base_url('modules/mention/assets/css/jquery.mentionsInput.css') .'"  rel="stylesheet" type="text/css" />';        
        echo "<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>";    
    }
}

function mention_client_add_footer_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if(!(strpos($viewuri, '/mention') === false)){
        echo '<script src="' . base_url('modules/mention/assets/js/mention_client.js') . '"></script>';
        echo '<script src="' . base_url('modules/mention/assets/third-party/jquery.caret.js') . '"></script>';
        echo '<script src="' . base_url('modules/mention/assets/third-party/jquery.atwho.js') . '"></script>';  
    }
}

function mention_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(MENTION_MODULE_NAME, [MENTION_MODULE_NAME]);

/**
 * Init mention module menu items in setup in admin_init hook
 * @return null
 */
function mention_module_init_menu_items()
{
    if (has_permission('mention', '', 'view')) {
        $CI = &get_instance();
        $CI->app_menu->add_sidebar_menu_item('mention', [
                'name'     => _l('mention'),
                'href'     => admin_url('mention'),
                'icon'     => 'fa fa-hashtag',
                'position' => 30
        ]);
    }
}

/**
 * Init mention module permissions in setup in admin_init hook
 */
function mention_permissions()
{
    $capabilities = [];
    $capabilities['capabilities'] = [
            'view'   => _l('permission_view'),
    ];
    register_staff_capabilities('mention', $capabilities, _l('mention'));
}

/**
 * Init mention module menu items in setup in customers_navigation_end hook
 */
function mention_module_init_client_menu_items()
{
    $menu = '';
    if (is_client_logged_in()) {
        $menu .= '<li class="customers-nav-item-Insurances-plan">
                  <a href="'.site_url('mention/mention_client').'">
                    <i class=""></i> '
                    . _l('mention').'
                  </a>
               </li>';
    }
    echo html_entity_decode($menu);
}