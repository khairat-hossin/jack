<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reportplus extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('reportplus_model');
    }

    public function index()
    {
        show_404();
    }

    public function manage()
    {
        if (!has_permission('reportplus', '', 'view')) {
            access_denied('reportplus');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('reportplus', 'table'));
        }

        $data['title'] = _l('reportplus') . ' - ' . _l('reportplus_generate_report');
        $this->load->view('manage', $data);
    }

    public function generate_report()
    {
        if (!has_permission('reportplus', '', 'create')) {
            access_denied('reportplus');
        }

        if ($this->input->post()) {

            $emailTo = $this->input->post('email_to');
            $assignedReports = $this->input->post('assigned_reports');
            $fromDate = to_sql_date($this->input->post('generate_from_date'));
            $toDate = to_sql_date($this->input->post('generate_to_date'));

            $generatedForDates = $this->input->post('generate_from_date') . ' - ' . $this->input->post('generate_to_date');

            $generatedReport = $this->reportplus_model->generate_report($fromDate, $toDate, $assignedReports);
            $emailTemplate = $this->load->view('email_template', ['generatedReport' => $generatedReport, 'generatedDates' => $generatedForDates], true);

            //save database
            $data = [
                'email_to' => $emailTo,
                'assigned_reports' => json_encode($assignedReports),
                'generated_reports' => json_encode($generatedReport),
                'generate_dates' => $generatedForDates,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $reportId = $this->reportplus_model->add($data);

            //send email
            $explodeEmails = explode(',', $emailTo);
            $this->load->model('emails_model');
            $emailSubject = get_option('companyname') . ' - Report For ' . $generatedForDates . ' - ReportPlus';

            foreach ($explodeEmails as $email) {
                if ($this->emails_model->send_simple_email($email, $emailSubject, $emailTemplate)) {
                    log_activity('Report Mail Sent [To : ' . $email . ']');
                } else {
                    log_activity('Report Mail Failed [To : ' . $email . ']');
                }
            }

            //return response
            if ($reportId) {
                set_alert('success', _l('added_successfully', _l('reportplus')));
                echo json_encode([
                    'report_id' => $reportId,
                    'url' => admin_url('reportplus/view_report/' . $reportId)
                ]);
                die;
            }
            echo json_encode([
                'url' => admin_url('reportplus/manage'),
            ]);
            die;

        }

    }

    public function view_report($report_id)
    {
        if (!has_permission('reportplus', '', 'view')) {
            access_denied('reportplus');
        }

        $getReport = $this->reportplus_model->get($report_id);

        $toArrayReports = (array)json_decode($getReport->generated_reports, true);

        $data['report_data'] = $getReport;
        $data['generated_report'] = $this->load->view('email_template', ['generatedReport' => $toArrayReports, 'generatedDates' => $getReport->generate_dates], true);

        $data['title'] = _l('reportplus') . ' - ' . _l('reportplus_generate_report');
        $this->load->view('view_report', $data);
    }

    public function delete_report($report_id)
    {
        if (!has_permission('reportplus', '', 'delete')) {
            access_denied('reportplus');
        }

        if (!$report_id) {
            redirect(admin_url('reportplus/manage'));
        }

        $response = $this->reportplus_model->delete($report_id);

        if ($response == true) {
            set_alert('success', _l('deleted', _l('reportplus')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('reportplus')));
        }

        redirect(admin_url('reportplus/manage'));

    }

    public function settings()
    {
        if (!is_admin()) {
            access_denied('reportplus');
        }

        if ($this->input->post()) {
            if (!is_admin()) {
                access_denied('settings');
            }
            $this->load->model('payment_modes_model');
            $this->load->model('settings_model');

            $post_data = $this->input->post();
            $tmpData = $this->input->post(null, false);

            if (isset($post_data['settings']['reportplus_automatic_assigned_reports'])) {
                $post_data['settings']['reportplus_automatic_assigned_reports'] = json_encode($post_data['settings']['reportplus_automatic_assigned_reports']);
            }

            $success = $this->settings_model->update($post_data);

            if ($success > 0) {
                set_alert('success', _l('settings_updated'));
            }

            redirect(admin_url('reportplus/settings'), 'refresh');
        }

        $data['title'] = _l('reportplus') . ' - ' . _l('settings');
        $this->load->view('settings', $data);
    }

}
