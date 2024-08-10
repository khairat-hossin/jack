<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Staff_order extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['currencies_model', 'Products_model', 'invoices_model', 'coupons_model', 'products/order_model']);
    }

    public function index()
    {
        if (!has_permission('products', '', 'create')) {
            access_denied('products');
        }
        $message = "";
        if (!empty($this->input->post())) {
        	$post             = $this->input->post();
            $return_data = $this->order_model->add_invoice_order($post);
            if ($return_data['status']) {
            	set_alert('success', _l('order_success'));
            	if ($return_data['single_invoice']) {
            		redirect(admin_url('invoices/list_invoices/'.$return_data['invoice_id']), 'refresh');
            	}
            	redirect(admin_url('invoices'), 'refresh');
            }
            if (!$return_data['status']) {
            	$message = $return_data['message'];
            }
        }
        $data['message']	    = $message;
        $data['products']       = $this->products_model->get_by_id_product();
        $data['title']          = _l('add_new_order');
        $this->load->view('add_new_order', $data);
    }

    public function get_available_coupons($client_id = false)
    {
        if (empty($client_id)) {
            $client_id = $this->input->post('client_id');
        }
    	$res = $this->coupons_model->get_availables($client_id);
    	echo json_encode($res);
    }

    public function get_product_data() {
    	$id = $this->input->post('product_id');
    	$res = $this->products_model->get_by_id_product($id);
    	echo json_encode($res);
    }
}
