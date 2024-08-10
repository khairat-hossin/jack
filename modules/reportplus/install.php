<?php

defined('BASEPATH') or exit('No direct script access allowed');

add_option('reportplus_settings_emails_to');
add_option('reportplus_automatic_assigned_reports', '["total_customers","total_expenses","total_contracts","total_leads","total_converted_leads","income_vs_expenses","tasks_report","tickets_report","invoices_report","proposals_report","estimates_report","projects_report","total_staff_converted_leads","total_staff_tasks_completed","total_staff_closed_tickets","total_expenses_based_on_categories","count_total_payment_modes_payments"]');
add_option('reportplus_enable_automatic_reports', '0');
add_option('reportplus_automatic_report_interval', 'weekly');
add_option('reportplus_last_time_sent_report_via_cron', '');

if (!$CI->db->table_exists(db_prefix() . 'reportplus_generated_reports')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "reportplus_generated_reports` (
  `id` int(11) NOT NULL,
  `email_to` text,
  `assigned_reports` text,
  `generated_reports` text,
  `generate_dates` text,
  `created_at` datetime
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'reportplus_generated_reports`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'reportplus_generated_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}
