<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reportplus_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function generate_report($start_date, $end_date, $reportsToGenerate)
    {

        $reportsToBeGenerated = $reportsToGenerate;

        $generatedReports = [];

        if (in_array('total_customers', $reportsToBeGenerated)) {
            $generatedReports['total_customers'] = $this->total_customers_report($start_date, $end_date);
        }

        if (in_array('total_expenses', $reportsToBeGenerated)) {
            $generatedReports['total_expenses'] = $this->total_expenses_report($start_date, $end_date);
        }

        if (in_array('total_contracts', $reportsToBeGenerated)) {
            $generatedReports['total_contracts'] = $this->total_contracts_report($start_date, $end_date);
        }

        if (in_array('total_leads', $reportsToBeGenerated)) {
            $generatedReports['total_leads'] = $this->total_leads_report($start_date, $end_date);
        }

        if (in_array('total_converted_leads', $reportsToBeGenerated)) {
            $generatedReports['total_converted_leads'] = $this->total_converted_leads_report($start_date, $end_date);
        }

        if (in_array('income_vs_expenses', $reportsToBeGenerated)) {
            $generatedReports['income_sum'] = $this->total_payments_sum_report($start_date, $end_date);
            $generatedReports['expense_sum'] = $this->total_expenses_sum_report($start_date, $end_date);
        }

        if (in_array('tasks_report', $reportsToBeGenerated)) {
            $generatedReports['tasks_report'] = $this->total_tasks_based_on_statuses_report($start_date, $end_date);
            $generatedReports['total_tasks_report'] = $this->total_tasks_report($start_date, $end_date);
        }

        if (in_array('tickets_report', $reportsToBeGenerated)) {
            $generatedReports['tickets_report'] = $this->total_tickets_based_on_statuses_report($start_date, $end_date);
            $generatedReports['total_tickets_report'] = $this->total_tickets_report($start_date, $end_date);
        }

        if (in_array('invoices_report', $reportsToBeGenerated)) {
            $generatedReports['invoices_report'] = $this->total_invoices_based_on_statuses_report($start_date, $end_date);
            $generatedReports['total_invoices_report'] = $this->total_invoices_report($start_date, $end_date);
        }

        if (in_array('proposals_report', $reportsToBeGenerated)) {
            $generatedReports['proposals_report'] = $this->total_proposals_based_on_statuses_report($start_date, $end_date);
            $generatedReports['total_proposals_report'] = $this->total_proposals_report($start_date, $end_date);
        }

        if (in_array('estimates_report', $reportsToBeGenerated)) {
            $generatedReports['estimates_report'] = $this->total_estimates_based_on_statuses_report($start_date, $end_date);
            $generatedReports['total_estimates_report'] = $this->total_estimates_report($start_date, $end_date);
        }

        if (in_array('projects_report', $reportsToBeGenerated)) {
            $generatedReports['projects_report'] = $this->total_projects_based_on_statuses_report($start_date, $end_date);
            $generatedReports['total_projects_report'] = $this->total_projects_report($start_date, $end_date);
        }

        if (in_array('total_staff_converted_leads', $reportsToBeGenerated)) {
            $generatedReports['total_staff_converted_leads'] = $this->total_staff_converted_leads($start_date, $end_date);
        }

        if (in_array('total_staff_tasks_completed', $reportsToBeGenerated)) {
            $generatedReports['total_staff_tasks_completed'] = $this->total_staff_tasks_completed($start_date, $end_date);
        }

        if (in_array('total_staff_closed_tickets', $reportsToBeGenerated)) {
            $generatedReports['total_staff_closed_tickets'] = $this->total_staff_closed_tickets($start_date, $end_date);
        }

        if (in_array('total_expenses_based_on_categories', $reportsToBeGenerated)) {
            $generatedReports['total_expenses_based_on_categories'] = $this->total_expenses_based_on_categories($start_date, $end_date);
        }

        if (in_array('count_total_payment_modes_payments', $reportsToBeGenerated)) {
            $generatedReports['count_total_payment_modes_payments'] = $this->count_total_payment_modes_payments($start_date, $end_date);
        }

        if (in_array('project_logged_hours_and_expenses_report', $reportsToBeGenerated)) {
            $generatedReports['project_logged_hours_and_expenses_report'] = $this->projects_list_for_report();
        }

        return $generatedReports;
    }

    public function total_customers_report($from_date, $to_date)
    {
        $sql = "SELECT COUNT(" . db_prefix() . "clients.userid) as total FROM " . db_prefix() . "clients WHERE DATE(datecreated) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'";
        return $this->db->query($sql)->row()->total;
    }

    public function total_leads_report($from_date, $to_date)
    {
        $sql = "SELECT COUNT(" . db_prefix() . "leads.id) as total FROM " . db_prefix() . "leads WHERE DATE(dateadded) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'";
        return $this->db->query($sql)->row()->total;
    }

    public function total_expenses_report($from_date, $to_date)
    {
        $sql = "SELECT COUNT(" . db_prefix() . "expenses.id) as total FROM " . db_prefix() . "expenses WHERE DATE(dateadded) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'";
        return $this->db->query($sql)->row()->total;
    }

    public function total_contracts_report($from_date, $to_date)
    {
        $sql = "SELECT COUNT(" . db_prefix() . "contracts.id) as total FROM " . db_prefix() . "contracts WHERE DATE(dateadded) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'";
        return $this->db->query($sql)->row()->total;
    }

    public function total_projects_report($from_date, $to_date)
    {
        $sql = "SELECT COUNT(" . db_prefix() . "projects.id) as total FROM " . db_prefix() . "projects WHERE DATE(project_created) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'";
        return $this->db->query($sql)->row()->total;
    }

    public function total_invoices_report($from_date, $to_date)
    {
        $sql = "SELECT COUNT(" . db_prefix() . "invoices.id) as total FROM " . db_prefix() . "invoices WHERE DATE(datecreated) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'";
        return $this->db->query($sql)->row()->total;
    }

    public function total_proposals_report($from_date, $to_date)
    {
        $sql = "SELECT COUNT(" . db_prefix() . "proposals.id) as total FROM " . db_prefix() . "proposals WHERE DATE(datecreated) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'";
        return $this->db->query($sql)->row()->total;
    }

    public function total_estimates_report($from_date, $to_date)
    {
        $sql = "SELECT COUNT(" . db_prefix() . "estimates.id) as total FROM " . db_prefix() . "estimates WHERE DATE(datecreated) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'";
        return $this->db->query($sql)->row()->total;
    }

    public function total_tasks_report($from_date, $to_date)
    {
        $sql = "SELECT COUNT(" . db_prefix() . "tasks.id) as total FROM " . db_prefix() . "tasks WHERE DATE(dateadded) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'";
        return $this->db->query($sql)->row()->total;
    }

    public function total_tickets_report($from_date, $to_date)
    {
        $sql = "SELECT COUNT(" . db_prefix() . "tickets.ticketid) as total FROM " . db_prefix() . "tickets WHERE DATE(date) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'";
        return $this->db->query($sql)->row()->total;
    }

    public function total_invoices_based_on_statuses_report($from_date, $to_date)
    {
        $this->db->select('status AS status_id, COUNT(*) AS total_invoice');
        $this->db->from(db_prefix() .'invoices');
        $this->db->where("DATE(datecreated) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");
        $this->db->group_by('status');

        $results = $this->db->get()->result_array();

        $statuses = array(
            1 => 'Unpaid',
            2 => 'Paid',
            3 => 'Partially Paid',
            4 => 'Overdue',
            5 => 'Canceled',
            6 => 'Draft'
        );

        $invoices_by_status = array();

        foreach ($statuses as $status_id => $status_name) {
            $total_invoice = 0;

            foreach ($results as $row) {
                if ($row['status_id'] == $status_id) {
                    $total_invoice = $row['total_invoice'];
                    break;
                }
            }

            $invoices_by_status[] = array(
                'status' => $status_id,
                'total_invoice' => $total_invoice,
                'status_name' => $status_name
            );
        }

        return $invoices_by_status;
    }

    public function total_proposals_based_on_statuses_report($from_date, $to_date)
    {
        $this->db->select('status AS status_id, COUNT(*) AS total_proposals');
        $this->db->from(db_prefix() .'proposals');
        $this->db->where("DATE(datecreated) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");
        $this->db->group_by('status');

        $results = $this->db->get()->result_array();

        $statuses = array(
            1 => 'Open',
            2 => 'Declined',
            3 => 'Accepted',
            4 => 'Sent',
            5 => 'Revised',
            6 => 'Draft'
        );

        $proposals_by_status = array();

        foreach ($statuses as $status_id => $status_name) {
            $total_proposals = 0;

            foreach ($results as $row) {
                if ($row['status_id'] == $status_id) {
                    $total_proposals = $row['total_proposals'];
                    break;
                }
            }

            $proposals_by_status[] = array(
                'status' => $status_id,
                'total_proposals' => $total_proposals,
                'status_name' => $status_name
            );
        }

        return $proposals_by_status;
    }

    public function total_estimates_based_on_statuses_report($from_date, $to_date)
    {
        $this->db->select('status AS status_id, COUNT(*) AS total_estimates');
        $this->db->from(db_prefix() .'estimates');
        $this->db->where("DATE(datecreated) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");
        $this->db->group_by('status');

        $results = $this->db->get()->result_array();

        $statuses = array(
            1 => 'Draft',
            2 => 'Sent',
            3 => 'Declined',
            4 => 'Accepted',
            5 => 'Expired'
        );

        $estimates_by_status = array();

        foreach ($statuses as $status_id => $status_name) {
            $total_estimates = 0;

            foreach ($results as $row) {
                if ($row['status_id'] == $status_id) {
                    $total_estimates = $row['total_estimates'];
                    break;
                }
            }

            $estimates_by_status[] = array(
                'status' => $status_id,
                'total_estimates' => $total_estimates,
                'status_name' => $status_name
            );
        }

        return $estimates_by_status;
    }

    public function total_tasks_based_on_statuses_report($from_date, $to_date)
    {
        $this->db->select('status AS status_id, COUNT(*) AS total_tasks');
        $this->db->from(db_prefix() .'tasks');
        $this->db->where("DATE(dateadded) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");
        $this->db->group_by('status');

        $results = $this->db->get()->result_array();

        $statuses = array(
            1 => 'Not Started',
            2 => 'Awaiting Feedback',
            3 => 'Testing',
            4 => 'In Progress',
            5 => 'Complete'
        );

        $tasks_by_status = array();

        foreach ($statuses as $status_id => $status_name) {
            $total_tasks = 0;

            foreach ($results as $row) {
                if ($row['status_id'] == $status_id) {
                    $total_tasks = $row['total_tasks'];
                    break;
                }
            }

            $tasks_by_status[] = array(
                'status' => $status_id,
                'total_tasks' => $total_tasks,
                'status_name' => $status_name
            );
        }

        return $tasks_by_status;
    }

    public function total_tickets_based_on_statuses_report($from_date, $to_date)
    {
        $this->db->select('status AS status_id, COUNT(*) AS total_tickets');
        $this->db->from(db_prefix() .'tickets');
        $this->db->where("DATE(date) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");
        $this->db->group_by('status');

        $results = $this->db->get()->result_array();

        $statuses = array(
            1 => 'Open',
            2 => 'In Progress',
            3 => 'Answered',
            4 => 'On Hold',
            5 => 'Closed'
        );

        $tickets_by_status = array();

        foreach ($statuses as $status_id => $status_name) {
            $total_tickets = 0;

            foreach ($results as $row) {
                if ($row['status_id'] == $status_id) {
                    $total_tickets = $row['total_tickets'];
                    break;
                }
            }

            $tickets_by_status[] = array(
                'status' => $status_id,
                'total_tickets' => $total_tickets,
                'status_name' => $status_name
            );
        }

        return $tickets_by_status;
    }

    public function total_projects_based_on_statuses_report($from_date, $to_date)
    {
        $this->db->select('status AS status_id, COUNT(*) AS total_projects');
        $this->db->from(db_prefix() .'projects');
        $this->db->where("DATE(project_created) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");
        $this->db->group_by('status');

        $results = $this->db->get()->result_array();

        $statuses = array(
            1 => 'Not Started',
            2 => 'In Progress',
            3 => 'On Hold',
            4 => 'Finished'
        );

        $projects_by_status = array();

        foreach ($statuses as $status_id => $status_name) {
            $total_projects = 0;

            foreach ($results as $row) {
                if ($row['status_id'] == $status_id) {
                    $total_projects = $row['total_projects'];
                    break;
                }
            }

            $projects_by_status[] = array(
                'status' => $status_id,
                'total_projects' => $total_projects,
                'status_name' => $status_name
            );
        }

        return $projects_by_status;
    }

    public function total_converted_leads_report($from_date, $to_date)
    {
        $this->db->select('COUNT(date_converted) AS total_converted');
        $this->db->from(db_prefix() .'leads');
        $this->db->where("DATE(dateadded) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");
        $this->db->where('date_converted IS NOT NULL');

        $result = $this->db->get()->row_array();

        return $result['total_converted'];
    }

    public function total_payments_sum_report($from_date, $to_date)
    {
        $this->db->select('SUM(amount) AS total_amount');
        $this->db->from(db_prefix() .'invoicepaymentrecords');
        $this->db->where("DATE(daterecorded) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");

        $result = $this->db->get()->row_array();

        return $result['total_amount'] ?: 0;
    }

    public function total_expenses_sum_report($from_date, $to_date)
    {
        $this->db->select('SUM(amount) AS total_amount');
        $this->db->from(db_prefix() .'expenses');
        $this->db->where("DATE(dateadded) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");

        $result = $this->db->get()->row_array();

        return $result['total_amount'] ?: 0;
    }

    public function total_staff_converted_leads($from_date, $to_date)
    {
        $this->db->select('CONCAT(firstname, " ", lastname) AS staff, COUNT(*) AS total_converted_leads');
        $this->db->from(db_prefix() .'leads');
        $this->db->join(db_prefix() .'staff', db_prefix() .'leads.assigned = '.db_prefix().'staff.staffid');
        $this->db->where(db_prefix() .'leads.date_converted IS NOT NULL');
        $this->db->where("DATE(".db_prefix()."leads.dateadded) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");
        $this->db->group_by(db_prefix() .'leads.assigned');
        $this->db->order_by('total_converted_leads', 'DESC');
        $this->db->limit(10);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function total_staff_tasks_completed($from_date, $to_date)
    {
        $this->db->select('CONCAT('.db_prefix().'staff.firstname, " ", '.db_prefix().'staff.lastname) AS staff, COUNT(*) AS total_completed_tasks');
        $this->db->from(db_prefix() .'tasks');
        $this->db->join(db_prefix() .'task_assigned', db_prefix() .'tasks.id = '.db_prefix().'task_assigned.taskid');
        $this->db->join(db_prefix() .'staff', db_prefix() .'task_assigned.staffid = '.db_prefix().'staff.staffid');
        $this->db->where(db_prefix() .'tasks.status', 5);
        $this->db->where("DATE(".db_prefix()."tasks.dateadded) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");
        $this->db->group_by(db_prefix() .'task_assigned.staffid');
        $this->db->order_by('total_completed_tasks', 'DESC');
        $this->db->limit(10);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function total_staff_closed_tickets($from_date, $to_date)
    {
        $this->db->select('CONCAT('.db_prefix().'staff.firstname, " ", '.db_prefix().'staff.lastname) AS staff, COUNT(*) AS total_closed_tickets');
        $this->db->from(db_prefix() .'tickets');
        $this->db->join(db_prefix() .'staff', db_prefix() .'tickets.assigned = '.db_prefix().'staff.staffid');
        $this->db->where(db_prefix() .'tickets.status', 5);
        $this->db->where("DATE(".db_prefix()."tickets.date) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");
        $this->db->group_by(db_prefix() .'tickets.assigned');
        $this->db->order_by('total_closed_tickets', 'DESC');
        $this->db->limit(10);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function total_expenses_based_on_categories($from_date, $to_date)
    {
        $this->db->select(db_prefix() .'expenses_categories.name AS category_name, COUNT(*) AS total_expenses');
        $this->db->from(db_prefix() .'expenses');
        $this->db->join(db_prefix() .'expenses_categories', db_prefix() .'expenses.category = '.db_prefix().'expenses_categories.id');
        $this->db->where("DATE(".db_prefix()."expenses.dateadded) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");
        $this->db->group_by(db_prefix() .'expenses.category');
        $this->db->order_by('total_expenses', 'DESC');
        $this->db->limit(10);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function count_total_payment_modes_payments($from_date, $to_date)
    {
        $this->db->select(db_prefix() .'payment_modes.name AS payment_mode, COUNT(*) AS total_payments');
        $this->db->from(db_prefix() .'invoicepaymentrecords');
        $this->db->join(db_prefix() .'payment_modes', db_prefix() .'invoicepaymentrecords.paymentmethod = '.db_prefix().'payment_modes.id');
        $this->db->where(db_prefix() .'invoicepaymentrecords.paymentmethod IS NOT NULL');
        $this->db->where("DATE(".db_prefix()."invoicepaymentrecords.daterecorded) BETWEEN '" . $this->db->escape_str($from_date) . "' AND '" . $this->db->escape_str($to_date) . "'");
        $this->db->group_by(db_prefix() .'invoicepaymentrecords.paymentmode');
        $this->db->order_by('total_payments', 'DESC');
        $this->db->limit(10);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function projects_list_for_report()
    {
        $this->load->model('projects_model');

        $this->db->select('*');
        $this->db->from(db_prefix() .'projects');
        $this->db->where("status=2 OR status=3");

        $availableProjects = $this->db->get()->result_array();

        $generatedReport = [];

        foreach ($availableProjects as $project) {

            $totalLoggedTime = $this->projects_model->total_logged_time_by_billing_type($project['id']);
            $projectCurrencyData = $this->projects_model->get_currency($project['id']);
            $projectBillableTime = $this->projects_model->data_billed_time($project['id']);

            $generatedReport[$project['id']] = [
                'project_name' => $project['name'],
                'logged_hours' => [
                    'hours' => $totalLoggedTime['logged_time'],
                    'amount' => app_format_money($totalLoggedTime['total_money'], $projectCurrencyData)
                ],
                'billed_hours' => [
                    'hours' => $projectBillableTime['logged_time'],
                    'amount' => app_format_money($projectBillableTime['total_money'], $projectCurrencyData)
                ],
                'expenses' => app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project['id']], 'field' => 'amount']), $projectCurrencyData),
                'billed_expenses' => app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project['id'], 'invoiceid !=' => 'NULL', 'billable' => 1], 'field' => 'amount']), $projectCurrencyData)
            ];
        }

        return $generatedReport;
    }

    public function add($data)
    {
        $this->db->insert(db_prefix() . 'reportplus_generated_reports', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'reportplus_generated_reports')->row();
    }

    public function getAll()
    {
        return $this->db->get(db_prefix() . 'reportplus_generated_reports')->result_array();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'reportplus_generated_reports', $data);

        return $this->db->affected_rows() > 0;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'reportplus_generated_reports');

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
}
