<?php

function reportplus_report_types()
{
    return [
        [
            'value' => 'total_customers',
            'name' => _l('reportplus_total_customers')
        ],
        [
            'value' => 'total_expenses',
            'name' => _l('reportplus_total_expenses')
        ],
        [
            'value' => 'total_contracts',
            'name' => _l('reportplus_total_contracts')
        ],
        [
            'value' => 'total_leads',
            'name' => _l('reportplus_total_leads')
        ],
        [
            'value' => 'total_converted_leads',
            'name' => _l('reportplus_total_converted_leads')
        ],
        [
            'value' => 'income_vs_expenses',
            'name' => _l('reportplus_income_vs_expenses')
        ],
        [
            'value' => 'tasks_report',
            'name' => _l('reportplus_tasks_report')
        ],
        [
            'value' => 'tickets_report',
            'name' => _l('reportplus_tickets_report')
        ],
        [
            'value' => 'invoices_report',
            'name' => _l('reportplus_invoices_report')
        ],
        [
            'value' => 'proposals_report',
            'name' => _l('reportplus_proposals_report')
        ],
        [
            'value' => 'estimates_report',
            'name' => _l('reportplus_estimates_report')
        ],
        [
            'value' => 'projects_report',
            'name' => _l('reportplus_projects_report')
        ],
        [
            'value' => 'total_staff_converted_leads',
            'name' => _l('reportplus_total_staff_converted_leads')
        ],
        [
            'value' => 'total_staff_tasks_completed',
            'name' => _l('reportplus_total_staff_tasks_completed')
        ],
        [
            'value' => 'total_staff_closed_tickets',
            'name' => _l('reportplus_total_staff_closed_tickets')
        ],
        [
            'value' => 'total_expenses_based_on_categories',
            'name' => _l('reportplus_total_expenses_based_on_categories')
        ],
        [
            'value' => 'count_total_payment_modes_payments',
            'name' => _l('reportplus_count_total_payment_modes_payments')
        ],
        [
            'value' => 'project_logged_hours_and_expenses_report',
            'name' => _l('reportplus_project_logged_hours_and_expenses_report')
        ]
    ];
}

function reportplus_report_interval()
{
    return [
        [
            'value' => 'daily',
            'name' => _l('reportplus_daily')
        ],
        [
            'value' => 'weekly',
            'name' => _l('reportplus_weekly')
        ],
        [
            'value' => 'biweekly',
            'name' => _l('reportplus_biweekly')
        ],
        [
            'value' => 'monthly',
            'name' => _l('reportplus_monthly')
        ]
    ];
}