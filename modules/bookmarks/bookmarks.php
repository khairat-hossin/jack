<?php
/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */
defined('BASEPATH') or exit('No direct script access allowed');
/*
Module Name: Bookmarks
Description: Module for bookmarking links
Version: 1.1.0
Author: Weeb Digital
Requires at least: 2.3.*
*/
define('BOOKMARKS_MODULE_NAME', 'bookmarks');
hooks()->add_action('admin_init', 'bookmarks_module_init_menu_items');
hooks()
    ->add_action('admin_init', 'bookmarks_permissions');
/**
 * Register module activation hook
 * @param  string $module   module system name
 * @param  mixed  $function function for the hook
 * @return mixed
 */
register_activation_hook(BOOKMARKS_MODULE_NAME, 'bookmarks_module_activation_hook');
/**
 * Register module deactivation hook
 * @param  string $module   module system name
 * @param  mixed  $function function for the hook
 * @return mixed
 */
//register_deactivation_hook($module, $function);
/**
 * Register module uninstall hook
 * @param  string $module   module system name
 * @param  mixed  $function function for the hook
 * @return mixed
 */
//register_uninstall_hook($module, $function);
/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(BOOKMARKS_MODULE_NAME, [BOOKMARKS_MODULE_NAME]);
function bookmarks_module_activation_hook()
{
    $CI = & get_instance();
    require_once (__DIR__ . '/install.php');
}
/**
 * Init bookmarks module menu items in setup in admin_init hook
 * @return null
 */
function bookmarks_module_init_menu_items()
{
    $CI = & get_instance();
    $CI
        ->app_menu
        ->add_sidebar_menu_item(BOOKMARKS_MODULE_NAME, [
    'name' => _l('bookmarks') ,
    'href' => admin_url('bookmarks') ,
    'icon' => 'fa fa-bookmark',
    'position' => 9,
    ]);
    $CI
        ->app
        ->add_quick_actions_link([
    'name' => _l('bookmark') ,
    'url' => 'new_bookmark',
    'position' => 26,
    ]);
}
function bookmarks_permissions()
{
    $capabilities = [];
    $capabilities['capabilities'] = [
    'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
    'create' => _l('permission_create') ,
    'edit' => _l('permission_edit') ,
    'delete' => _l('permission_delete') ,
    ];
    register_staff_capabilities('bookmarks', $capabilities, _l('bookmarks'));
}