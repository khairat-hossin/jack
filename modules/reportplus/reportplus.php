<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: ReportPlus
Description: Effortlessly receive essential reports on customers, leads, expenses, contracts, projects, tasks, tickets, conversions, payments, and more. Stay on top of your business with automated email reports tailored to your schedule. Optimize your decision-making process with ReportPlus for Perfex CRM.
Version: 1.1.0
Author: LenzCreative
Author URI: https://codecanyon.net/user/lenzcreativee/portfolio
Requires at least: 1.0.*
*/

define('REPORTPLUS_MODULE_NAME', 'reportplus');

hooks()->add_action('admin_init', 'reportplus_module_init_menu_items');
hooks()->add_action('admin_init', 'reportplus_permissions');
hooks()->add_action('before_cron_run', 'reportplus_auto_generate_report');

/**
 * Load the module helper
 */
$CI = &get_instance();
$CI->load->helper(REPORTPLUS_MODULE_NAME . '/reportplus'); //on module main file

function reportplus_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete')
    ];

    register_staff_capabilities('reportplus', $capabilities, _l('reportplus'));
}

/**
 * Register activation module hook
 */
register_activation_hook(REPORTPLUS_MODULE_NAME, 'reportplus_module_activation_hook');

function reportplus_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(REPORTPLUS_MODULE_NAME, [REPORTPLUS_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function reportplus_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('reportplus', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('reportplus', [
            'slug' => 'reportplus',
            'name' => _l('reportplus'),
            'position' => 6,
            'href' => admin_url('reportplus'),
            'icon' => 'fas fa-chart-bar'
        ]);
    }

    if (has_permission('reportplus', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('reportplus', [
            'slug' => 'reportplus-manage',
            'name' => _l('reportplus_reports'),
            'href' => admin_url('reportplus/manage'),
            'position' => 11,
        ]);
    }

    if (is_admin()) {
        $CI->app_menu->add_sidebar_children_item('reportplus', [
            'slug' => 'reportplus-settings',
            'name' => _l('settings'),
            'href' => admin_url('reportplus/settings'),
            'position' => 11,
        ]);
    }

}

function reportplus_auto_generate_report()
{
    if (get_option('reportplus_enable_automatic_reports') == '0') {
        return;
    }

    $CI = & get_instance();

    $specificDate = get_option('reportplus_last_time_sent_report_via_cron');

    if (empty($specificDate)) {

        $CI->load->model('reportplus/reportplus_model');

        $now = new DateTime();

        $sevenDaysAgo = clone $now;
        $sevenDaysAgo->modify('-7 days');

        $sevenDaysBefore = $sevenDaysAgo->format('Y-m-d');

        $generatedForDates = $sevenDaysBefore . ' - ' . $now->format('Y-m-d');

        if (empty(get_option('reportplus_automatic_assigned_reports'))) {
            return;
        }

        if (empty(get_option('reportplus_settings_emails_to'))) {
            return;
        }

        $generatedReport = $CI->reportplus_model->generate_report($sevenDaysBefore, $now->format('Y-m-d'), json_decode(get_option('reportplus_automatic_assigned_reports')));
        $emailTemplate = $CI->load->view('reportplus/email_template', ['generatedReport' => $generatedReport], true);

        //save database
        $data = [
            'email_to' => get_option('reportplus_settings_emails_to'),
            'assigned_reports' => json_encode(get_option('reportplus_automatic_assigned_reports')),
            'generated_reports' => json_encode($generatedReport),
            'generate_dates' => $generatedForDates,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $CI->reportplus_model->add($data);

        //send email
        $explodeEmails = explode(',', get_option('reportplus_settings_emails_to'));
        $CI->load->model('emails_model');
        $emailSubject = get_option('companyname') . ' - Report For ' . $generatedForDates . ' - ReportPlus';

        foreach ($explodeEmails as $email) {
            if ($CI->emails_model->send_simple_email($email, $emailSubject, $emailTemplate)) {
                log_activity('Report Mail Sent [To : ' . $email . ']');
            } else {
                log_activity('Report Mail Failed [To : ' . $email . ']');
            }
        }

        update_option('reportplus_last_time_sent_report_via_cron', $now->format('Y-m-d'));

    } else {

        $specificDateTime = new DateTime($specificDate);

        $currentDateTime = new DateTime();
        $interval = $currentDateTime->diff($specificDateTime);

        $sendReports = '1';

        if (get_option('send_reports_every') == 'daily') {
            $sendReports = '1';
        }
        if (get_option('send_reports_every') == 'weekly') {
            $sendReports = '7';
        }
        if (get_option('send_reports_every') == 'biweekly') {
            $sendReports = '14';
        }
        if (get_option('send_reports_every') == 'monthly') {
            $sendReports = '30';
        }

        if ($interval->days >= $sendReports) {
            $CI->load->model('reportplus/reportplus_model');

            $generatedForDates = $specificDateTime->format('Y-m-d') . ' - ' . $currentDateTime->format('Y-m-d');

            if (empty(get_option('reportplus_automatic_assigned_reports'))) {
                return;
            }

            if (empty(get_option('reportplus_settings_emails_to'))) {
                return;
            }

            $generatedReport = $CI->reportplus_model->generate_report($specificDateTime->format('Y-m-d'), $currentDateTime->format('Y-m-d'), json_decode(get_option('reportplus_automatic_assigned_reports')));
            $emailTemplate = $CI->load->view('reportplus/email_template', ['generatedReport' => $generatedReport], true);

            //save database
            $data = [
                'email_to' => get_option('reportplus_settings_emails_to'),
                'assigned_reports' => json_encode(get_option('reportplus_automatic_assigned_reports')),
                'generated_reports' => json_encode($generatedReport),
                'generate_dates' => $generatedForDates,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $CI->reportplus_model->add($data);

            //send email
            $explodeEmails = explode(',', get_option('reportplus_settings_emails_to'));
            $CI->load->model('emails_model');
            $emailSubject = get_option('companyname') . ' - Report For ' . $generatedForDates . ' - ReportPlus';

            foreach ($explodeEmails as $email) {
                if ($CI->emails_model->send_simple_email($email, $emailSubject, $emailTemplate)) {
                    log_activity('Report Mail Sent [To : ' . $email . ']');
                } else {
                    log_activity('Report Mail Failed [To : ' . $email . ']');
                }
            }

            update_option('reportplus_last_time_sent_report_via_cron', $currentDateTime->format('Y-m-d'));

        }
    }
}