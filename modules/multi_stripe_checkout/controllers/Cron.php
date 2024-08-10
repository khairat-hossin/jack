<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Stripe_model');
        $this->load->model('emails_model');
        $this->load->model('staff_model');
        $this->load->library('stripe_core');
    }
    public function index($key = '')
    {
       
    }
    
    public function recurringinvoices()
    {
        $this->ci             = &get_instance();
        $invoice_hour_auto_operations = get_option('invoice_auto_operations_hour');
        $new_recurring_invoice_action = get_option('new_recurring_invoice_action');

        $invoices_create_invoice_from_recurring_only_on_paid_invoices = get_option('invoices_create_invoice_from_recurring_only_on_paid_invoices');
        $this->load->model('invoices_model');
        $this->db->select('id,recurring,date,last_recurring_date,number,duedate,recurring_type,custom_recurring,addedfrom,sale_agent,clientid');
        $this->db->from(db_prefix() . 'invoices');
        $this->db->where('recurring !=', 0);
        $this->db->where('(cycles != total_cycles OR cycles=0)');

        if ($invoices_create_invoice_from_recurring_only_on_paid_invoices == 1) {
            // Includes all recurring invoices with paid status if this option set to Yes
            $this->db->where('status', 2);
        }
        $this->db->where('status !=', 6);
        $invoices = $this->db->get()->result_array();

        $_renewals_ids_data = [];
        $recurring_invoices_id = [];
        $last_id = '';
        $last_total = '';
        $total_renewed      = 0;
        $sizeofinvoice = 0;
        foreach ($invoices as $invoice) 
        {
            $recurring_invoices =  $this->invoices_model->get_invoice_recurring_invoices($invoice['id']);
            foreach ($recurring_invoices as $recurring) 
            {
                $recurring_invoices_id[] = $recurring->id;
                $recurring_total[] = $recurring->total;
                $sizeofinvoice++;
            }
            if(!empty($recurring_invoices_id))
            {
                $last_id = $recurring_invoices_id[$sizeofinvoice - 1];
            }
            // Current date
            $date = new DateTime(date('Y-m-d'));
            // Check if is first recurring
            if (!$invoice['last_recurring_date']) {
                $last_recurring_date = date('Y-m-d', strtotime($invoice['date']));
            } else {
                $last_recurring_date = date('Y-m-d', strtotime($invoice['last_recurring_date']));
            }
            if ($invoice['custom_recurring'] == 0) {
                $invoice['recurring_type'] = 'MONTH';
            }

            $re_create_at = date('Y-m-d', strtotime('+' . $invoice['recurring'] . ' ' . strtoupper($invoice['recurring_type']), strtotime($last_recurring_date)));

            if (date('Y-m-d') >= $re_create_at) {

                // Recurring invoice date is okey lets convert it to new invoice
                $_invoice                     = $this->invoices_model->get($invoice['id']);
                $new_invoice_data             = [];
                $new_invoice_data['clientid'] = $_invoice->clientid;
                $new_invoice_data['number']   = get_option('next_invoice_number');
                $new_invoice_data['date']     = _d($re_create_at);
                $new_invoice_data['duedate']  = null;

                if ($_invoice->duedate) {
                    // Now we need to get duedate from the old invoice and calculate the time difference and set new duedate
                    // Ex. if the first invoice had duedate 20 days from now we will add the same duedate date but starting from now
                    $dStart                      = new DateTime($invoice['date']);
                    $dEnd                        = new DateTime($invoice['duedate']);
                    $dDiff                       = $dStart->diff($dEnd);
                    $new_invoice_data['duedate'] = _d(date('Y-m-d', strtotime('+' . $dDiff->days . ' DAY', strtotime($re_create_at))));
                } else {
                    if (get_option('invoice_due_after') != 0) {
                        $new_invoice_data['duedate'] = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime($re_create_at))));
                    }
                }

                $new_invoice_data['project_id']       = $_invoice->project_id;
                $new_invoice_data['show_quantity_as'] = $_invoice->show_quantity_as;
                $new_invoice_data['currency']         = $_invoice->currency;
                $new_invoice_data['subtotal']         = $_invoice->subtotal;
                if($last_id != '')
                {
                    $invoice_det = $this->db->query('select * from '.db_prefix().'invoicepaymentrecords where invoiceid ="'.$last_id.'"')->row();
                    $invoice_total =  $invoice_det->amount - $_invoice->total;
                    if($invoice_total != 0 && $invoice_total < 0 &&  strpos($invoice_total, '-') !== false)
                    {
                        $b = explode('-',$invoice_total);
                        $final_total = $_invoice->total + $b[1];
                    }
                    else if($invoice_total > 0)
                    {
                        $final_total = $invoice_total - $_invoice->total;
                    }
                    else
                    {
                        $final_total = $_invoice->total;
                    }
                }
                else
                {
                    $invoice_main = $this->db->query('select * from '.db_prefix().'invoicepaymentrecords where invoiceid ="'.$_invoice->id.'"')->row();
                    $invoice_total = $invoice_main->amount - $_invoice->total;
                    if($invoice_total != 0 && $invoice_total < 0 &&  strpos($invoice_total, '-') !== false)
                    {
                        $b = explode('-',$invoice_total);
                        $final_total = $_invoice->total + $b[1];
                    }
                    else if($invoice_total > 0)
                    {
                        $final_total = $invoice_total - $_invoice->total;
                    }
                    else
                    {
                        $final_total = $_invoice->total;
                    }
                }
                $new_invoice_data['total']            = $_invoice->total;
                $new_invoice_data['adjustment']       = $_invoice->adjustment;
                $new_invoice_data['discount_percent'] = $_invoice->discount_percent;
                $new_invoice_data['discount_total']   = $_invoice->discount_total;
                $new_invoice_data['discount_type']    = $_invoice->discount_type;
                $new_invoice_data['terms']            = clear_textarea_breaks($_invoice->terms);
                $new_invoice_data['sale_agent']       = $_invoice->sale_agent;
                // Since version 1.0.6
                $new_invoice_data['billing_street']   = clear_textarea_breaks($_invoice->billing_street);
                $new_invoice_data['billing_city']     = $_invoice->billing_city;
                $new_invoice_data['billing_state']    = $_invoice->billing_state;
                $new_invoice_data['billing_zip']      = $_invoice->billing_zip;
                $new_invoice_data['billing_country']  = $_invoice->billing_country;
                $new_invoice_data['shipping_street']  = clear_textarea_breaks($_invoice->shipping_street);
                $new_invoice_data['shipping_city']    = $_invoice->shipping_city;
                $new_invoice_data['shipping_state']   = $_invoice->shipping_state;
                $new_invoice_data['shipping_zip']     = $_invoice->shipping_zip;
                $new_invoice_data['shipping_country'] = $_invoice->shipping_country;
                if ($_invoice->include_shipping == 1) {
                    $new_invoice_data['include_shipping'] = $_invoice->include_shipping;
                }
                $new_invoice_data['include_shipping']         = $_invoice->include_shipping;
                $new_invoice_data['show_shipping_on_invoice'] = $_invoice->show_shipping_on_invoice;
                // Determine status based on settings
                if ($new_recurring_invoice_action == 'generate_and_send' || $new_recurring_invoice_action == 'generate_unpaid') {
                    $new_invoice_data['status'] = 1;
                } elseif ($new_recurring_invoice_action == 'generate_draft') {
                    $new_invoice_data['save_as_draft'] = true;
                }
                $new_invoice_data['clientnote']            = clear_textarea_breaks($_invoice->clientnote);
                $new_invoice_data['adminnote']             = '';
                $new_invoice_data['allowed_payment_modes'] = unserialize($_invoice->allowed_payment_modes);
                $new_invoice_data['is_recurring_from']     = $_invoice->id;
                $new_invoice_data['newitems']              = [];
                $key                                       = 1;
                $custom_fields_items                       = get_custom_fields('items');
                foreach ($_invoice->items as $item) {
                    $new_invoice_data['newitems'][$key]['description']      = $item['description'];
                    $new_invoice_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
                    $new_invoice_data['newitems'][$key]['qty']              = $item['qty'];
                    $new_invoice_data['newitems'][$key]['unit']             = $item['unit'];
                    $new_invoice_data['newitems'][$key]['taxname']          = [];
                    $taxes                                                  = get_invoice_item_taxes($item['id']);
                    foreach ($taxes as $tax) {
                        // tax name is in format TAX1|10.00
                        array_push($new_invoice_data['newitems'][$key]['taxname'], $tax['taxname']);
                    }
                    $new_invoice_data['newitems'][$key]['rate']  = $item['rate'];
                    $new_invoice_data['newitems'][$key]['order'] = $item['item_order'];

                    foreach ($custom_fields_items as $cf) {
                        $new_invoice_data['newitems'][$key]['custom_fields']['items'][$cf['id']] = get_custom_field_value($item['id'], $cf['id'], 'items', false);

                        if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                            define('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST', true);
                        }
                    }
                    $key++;
                }
                $id = $this->invoices_model->add($new_invoice_data);
                if ($id) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'invoices', [
                        'addedfrom'                => $_invoice->addedfrom,
                        'sale_agent'               => $_invoice->sale_agent,
                        'cancel_overdue_reminders' => $_invoice->cancel_overdue_reminders,
                    ]);


                    $tags = get_tags_in($_invoice->id, 'invoice');
                    handle_tags_save($tags, $id, 'invoice');

                    // Get the old expense custom field and add to the new
                    $custom_fields = get_custom_fields('invoice');
                    foreach ($custom_fields as $field) {
                        $value = get_custom_field_value($invoice['id'], $field['id'], 'invoice', false);
                        if ($value != '') {
                            $this->db->insert(db_prefix() . 'customfieldsvalues', [
                                'relid'   => $id,
                                'fieldid' => $field['id'],
                                'fieldto' => 'invoice',
                                'value'   => $value,
                            ]);
                        }
                    }
                    // Increment total renewed invoices
                    $total_renewed++;
                    // Update last recurring date to this invoice
                    $this->db->where('id', $invoice['id']);
                    $this->db->update(db_prefix() . 'invoices', [
                        'last_recurring_date' => $re_create_at,
                    ]);

                    $this->db->where('id', $invoice['id']);
                    $this->db->set('total_cycles', 'total_cycles+1', false);
                    $this->db->update(db_prefix() . 'invoices');

                    if ($new_recurring_invoice_action == 'generate_and_send') {
                        $this->invoices_model->send_invoice_to_client($id, 'invoice_send_to_customer', true);
                    }

                    $_renewals_ids_data[] = [
                        'from'       => $invoice['id'],
                        'clientid'   => $invoice['clientid'],
                        'renewed'    => $id,
                        'addedfrom'  => $invoice['addedfrom'],
                        'sale_agent' => $invoice['sale_agent'],
                    ];
                    // $this->pay_for_invoice($id,$last_id,$invoice_total);
                }
            }
        }

        $send_recurring_invoices_email = hooks()->apply_filters('send_recurring_invoices_system_email', 'true');
        if ($total_renewed > 0 && $send_recurring_invoices_email == 'true') {
            $date                               = _dt(date('Y-m-d H:i:s'));
            $email_send_to_by_staff_and_invoice = [];
            // Get all active staff members
            $staff = $this->staff_model->get('', ['active' => 1]);
            foreach ($staff as $member) {
                $sent = false;
                load_admin_language($member['staffid']);
                $recurring_invoices_email_data = _l('not_recurring_invoices_cron_activity_heading') . ' - ' . $date . '<br /><br />';
                foreach ($_renewals_ids_data as $renewed_invoice_data) 
                {
                    if ($renewed_invoice_data['addedfrom'] == $member['staffid'] || $renewed_invoice_data['sale_agent'] == $member['staffid'] || is_admin($member['staffid'])) {
                        $unique_send = '[' . $member['staffid'] . '-' . $renewed_invoice_data['from'] . ']';
                        $sent        = true;
                        // Prevent sending the email twice if the same staff is added is sale agent and is creator for this invoice.
                        if (in_array($unique_send, $email_send_to_by_staff_and_invoice)) {
                            $sent = false;
                        }
                        $recurring_invoices_email_data .= _l('not_action_taken_from_recurring_invoice') . ' <a href="' . admin_url('invoices/list_invoices/' . $renewed_invoice_data['from']) . '">' . format_invoice_number($renewed_invoice_data['from']) . '</a><br />';
                        $recurring_invoices_email_data .= _l('not_invoice_renewed') . ' <a href="' . admin_url('invoices/list_invoices/' . $renewed_invoice_data['renewed']) . '">' . format_invoice_number($renewed_invoice_data['renewed']) . '</a> - <a href="' . admin_url('clients/client/' . $renewed_invoice_data['clientid']) . '">' . get_company_name($renewed_invoice_data['clientid']) . '</a><br /><br />';
                    }
                }
                if ($sent == true) 
                {
                    array_push($email_send_to_by_staff_and_invoice, $unique_send);
                    $this->emails_model->send_simple_email($member['email'], _l('not_recurring_invoices_cron_activity_heading'), $recurring_invoices_email_data);
                }
            }
            load_admin_language();
        }
    }
    public function pay_for_invoice($id,$last_id,$final_total)
    {
        $_invoice                     = $this->invoices_model->get($id);
        if(!empty($_invoice))
        {
            $company_detail =  $this->db->query('select * from '.db_prefix().'clients where userid ="'.$_invoice->clientid.'"')->row();
            $stripe_method = '';
            if($company_detail->stripe_connect != '' && $company_detail->stripe_connect == 'gp_marketing')
            {
                $this->load->library('stripe_core_gpw');
                $stripe_method = 'stripe_core_gpw';
            }
            else if($company_detail->stripe_connect != '' && $company_detail->stripe_connect == 'gp_workspace')
            {
                $this->load->library('stripe_core');
                $stripe_method = 'stripe_core';
            }
            else
            {
                //mail to admin
            }
            try 
            {
               $cust = $this->ci->$stripe_method->get_customer($company_detail->stripe_id);
            } 
            catch (Exception $e) 
            {
               echo $e->getMessage();
            }
            $card_id = $cust->default_source;
            $description = format_invoice_number($id);
            $desc = "".$description."";
            $amt = strcasecmp($_invoice->currency_name, 'JPY') == 0 ? intval($_invoice->total) : $_invoice->total * 100;
            $charge = ["amount" => $amt,
                "currency" => strtolower($_invoice->currency_name),
                "customer" => $cust->id,
                "source" => $card_id,
                "description" => "Monthly",
                "metadata" => array("email" => $cust->email),
                "receipt_email" => $cust->email];
            $inv_items = [
                    'customer' =>  $company_detail->stripe_id,
                    'amount' => $amt,
                    'currency' => strtolower($_invoice->currency_name),
                    'description' => $desc,
                ];
            try 
            {
                $invoice_item = $this->ci->$stripe_method->create_invoice_item($inv_items);
            } 
            catch (Exception $e) 
            {
               echo $e->getMessage();
            }
            $invoice_stripe = array(
                                    "customer" => $company_detail->stripe_id,
                                    "description" => $desc,
                                    "collection_method" => 'charge_automatically',
                            );
            try 
            {
                $inv = $this->ci->$stripe_method->create_invoice($invoice_stripe);
                $inv_pay  = $this->ci->$stripe_method->create_invoice_pay($inv->id);
                            $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'invoices', [
                    'addedfrom'                => $_invoice->addedfrom,
                    'sale_agent'               => $_invoice->sale_agent,
                    'invoice_stripe_id'        => $inv->id,
                    'status'                   => '2',
                    'cancel_overdue_reminders' => $_invoice->cancel_overdue_reminders,
                ]);
            } 
            catch (Exception $e) 
            {
               echo $e->getMessage();
            }

            // $this->db->insert(db_prefix() . 'invoicepaymentrecords', [
            //     'invoiceid'                => $_invoice->id,
            //     'amount'               => $_invoice->total,
            //     'paymentmethod'        => 'stripe',
            //     'date'                   => date('Y-m-d H:i:s'),
            //     'daterecorded' => date('Y-m-d H:i:s')
            // ]);
        }
    }
}
