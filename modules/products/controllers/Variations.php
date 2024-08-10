<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Variations extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model(['variations_model']);
    }

    public function index()
    {
        if (!is_admin()) {
            access_denied('Variations');
        }
        close_setup_menu();
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('products', 'tables/variation'));
        }
        $data['title']         = _l('variations');
        $this->load->view('variations/variations', $data);
    }

    public function add()
    {
        if (!has_permission('products', '', 'view')) {
            access_denied('products View');
        }
        close_setup_menu();
        if (has_permission('products', '', 'view')) {
            $post          = $this->input->post();
            if (!empty($post)) {
                $this->form_validation->set_rules('variation_name', 'variation name', 'required|is_unique[variations.name]');
                
                if (false == $this->form_validation->run()) {
                    set_alert('danger', preg_replace("/\r|\n/", '', validation_errors()));
                } else {
                    $data = [
                        'name'        => $post['variation_name'],
                        'description' => $post['variation_description'],
                    ];
                    $inserted_id    = $this->variations_model->add_variation($data);
                    
                    if ($inserted_id) {
                        if (isset($post['values'])) {
                            $this->variations_model->add_variation_values($inserted_id, $post['values']);
                        }
                        set_alert('success', 'Variation Added successfully');
                        redirect(admin_url('products/variations'), 'refresh');
                    } else {
                        set_alert('warning', _l('Error Found - Variation not inserted'));
                    }
                }
            }
            $data['title']              = _l('add_new', 'variation');
            $data['action']             = _l('variations');

            $this->load->view('variations/add', $data);
        } else {
            access_denied('variations');
        }
    }

    public function edit($id)
    {
        if (!has_permission('products', '', 'view')) {
            access_denied('products View');
        }
        close_setup_menu();
        if (has_permission('products', '', 'view')) {
            $original_variation = $data['variation'] = $this->variations_model->get($id, true);
            if (empty($original_variation)) {
                set_alert('danger', _l('not_found_products'));
                redirect(admin_url('products/variations'), 'refresh');
            }
            $post = $this->input->post();
           
            if (!empty($post)) {
                $this->form_validation->set_rules('variation_name', 'variation name', 'required');
                if ($original_variation->name != $post['variation_name']) {
                    $this->form_validation->set_rules('variation_name', 'variation name', 'required|is_unique[variations.name]');
                }
                if (false == $this->form_validation->run()) {
                    set_alert('danger', preg_replace("/\r|\n/", '', validation_errors()));
                } else {
                    $data = [
                        'name'        => $post['variation_name'],
                        'description' => $post['variation_description'],
                    ];
                    $result = $this->variations_model->edit($data, $id);
                    if (isset($post['values'])) {
                        $this->variations_model->edit_variation_values($id, $post['values']);
                    }
                    if ($result) {
                        set_alert('success', 'Variation Updated successfully');
                        redirect(admin_url('products/variations'), 'refresh');
                    } else {
                        set_alert('warning', _l('Error Found Or You Have not made any changes'));
                    }
                }
            }
            $data['title']              = _l('edit', 'variation');
            $this->load->view('variations/add', $data);
        } else {
            access_denied('products');
        }
    }

    public function values()
    {
        $variation_id     = $this->input->post('variation_id');
        $variation_values = $this->variations_model->get_values($variation_id);
        echo json_encode($variation_values);
    }

    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('Delete Variation');
        }
        if (!$id) {
            redirect(admin_url('products/variations'));
        }
        $response = $this->variations_model->delete($id);
        if (true == $response) {
            set_alert('success', _l('deleted', _l('variations')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('variations')));
        }
        redirect(admin_url('variations'));
    }
}