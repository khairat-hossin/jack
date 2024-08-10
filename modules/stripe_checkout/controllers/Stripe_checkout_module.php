<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stripe_checkout_module extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Stripe_model');
        $this->load->model('emails_model');
        $this->load->model('staff_model');
        $this->load->library('stripe_core');
    }
    public function card_detail_html()
    {
      $contact_id = get_contact_user_id();
      $data['card_details'] = $this->Stripe_model->get_card_details($contact_id);
      return $this->load->view('card_details_html',$data);
    }
    public function save_card_details()
    {
      if($this->input->post())
      {
        $contact_id = get_contact_user_id();
        $card_details = $this->Stripe_model->get_card_details($contact_id);
        if(empty($card_details))
        {
          $save = $this->db->insert(db_prefix().'contact_card_details',[
              'contact_id' => $contact_id,
              'card_number' => convert_uuencode($_POST['card_number']),
              'expire_month' => convert_uuencode($_POST['expire_month']),
              'expire_year' => convert_uuencode($_POST['expire_year']),
              'cvv' => convert_uuencode($_POST['cvv']),
              'card_name' => convert_uuencode($_POST['card_name'])]);
          $id = $this->db->insert_id();
          if($id !== '')
          {
            set_alert('success',_l('card_details_saved_successfully'));
          }
        }
        else {

          $this->db->where('contact_id',$contact_id);
          $save = $this->db->update(db_prefix().'contact_card_details',[
              'contact_id' => $contact_id,
              'card_number' => convert_uuencode($_POST['card_number']),
              'expire_month' => convert_uuencode($_POST['expire_month']),
              'expire_year' => convert_uuencode($_POST['expire_year']),
              'cvv' => convert_uuencode($_POST['cvv']),
              'card_name' => convert_uuencode($_POST['card_name'])]);
          if($this->db->affected_rows() > 0)
          {
            set_alert('success',_l('card_details_updated_successfully'));
          }
        }
        redirect(site_url('clients/profile'));
      }
    }

    public function save_credit_card_details()
    {
       if($this->input->post())
      {
        $contact_id = get_contact_user_id();
        $data = $this->input->post();
        $card_details = $this->Stripe_model->get_card_details($contact_id);
        if(empty($card_details))
        {
          $save = $this->db->insert(db_prefix().'contact_card_details',[
              'contact_id' => $contact_id,
              'card_number' => convert_uuencode($_POST['card_number']),
              'expire_month' => convert_uuencode($_POST['expire_month']),
              'expire_year' => convert_uuencode($_POST['expire_year']),
              'cvv' => convert_uuencode($_POST['cvv']),
              'card_name' => convert_uuencode($_POST['card_name'])]);
          $id = $this->db->insert_id();
          if($id !== '')
          {
            set_alert('success',_l('card_details_saved_successfully'));
          }
        }
        else {
          $this->db->where('contact_id',$contact_id);
          $save = $this->db->update(db_prefix().'contact_card_details',[
              'card_number' => convert_uuencode($_POST['card_number']),
              'expire_month' => convert_uuencode($_POST['expire_month']),
              'expire_year' => convert_uuencode($_POST['expire_year']),
              'cvv' => convert_uuencode($_POST['cvv']),
              'card_name' => convert_uuencode($_POST['card_name'])]);
          if($this->db->affected_rows() > 0)
          {
            set_alert('success',_l('card_details_updated_successfully'));
          }
        }
        redirect(site_url('clients/credit_card'));
      }
    }

    public function subscription_html()
    {
      $invoice_id = $_GET['invoice_id'];
      $invoice = $this->db->query('select * from '.db_prefix().'invoices where id ="'.$invoice_id.'"')->row();
      if(is_client_logged_in())
      {
        $client_id = get_client_user_id();
      }
      else 
      {
        $client_id = $invoice->clientid;
      }
      
      if($invoice->recurring_type !== '' && $invoice->recurring !== '')
      {
        $sub = $this->Stripe_model->get_subscriptions($client_id);
        $html = '';
        $html .= '<div class="form-group"><label class="control-label" for="subscription">Subscription Plan</label><select id="subscription" name="subscription" class="selectpicker form-control" onchange="subscription_change(this);">';
        $html .= '<option value="">Select Subscription</option>';
        foreach ($sub as $value)
        {
          $html .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
        }
        $html .= '</select></div>';
      }
      else {
        $html = 'no';
      }
      echo json_encode(array('html'=>$html));
    }
    public function subscription_data()
    {
      $id = $_GET['sub_id'];
      $subscription = $this->db->query('select * from '.db_prefix().'subscriptions where id="'.$id.'"')->row();
      echo json_encode(array('sub_type'=>$subscription->type));
    }

    public function card_details_exist()
    {
      $contact_id = $_GET['contact_id'];
      $card_details = $this->Stripe_model->get_card_details($contact_id);
      if(!empty($card_details))
      {
        $success = 'true';
        $html = '';
      }
      else {
        $html = 'Please fill the Credit Card Details <a href="'.base_url().'clients/profile">here</a>';
        $success = 'false';
      }
      echo json_encode(array('success'=>$success,'html'=>$html));
    }
    public function recurring_invoice_gpw()
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
        $total_renewed      = 0;
        foreach ($invoices as $invoice)
        {
            $_invoice                     = $this->invoices_model->get($invoice['id']);
            if(isset($_invoice) && !empty($_invoice))
            {
              $subscription =  $this->db->query('select * from '.db_prefix().'subscriptions where stripe_subscription_id="'.$_invoice->subscription_id.'"')->row();

              if(!empty($subscription))
              {
                if(!empty($subscription) && $subscription->type == 'gpw' && $subscription->type !== 'gpm' && $_invoice->type !== 'product')
                {
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
                    if (date('Y-m-d') >= $re_create_at)
                    {
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
                        $new_invoice_subscription = $_invoice->subscription_id;
                        $new_invoice_stripe_id = $_invoice->invoice_stripe_id;
                        $new_invoice_customer_id = $_invoice->customer_id;
                        $new_invoice_data['project_id']       = $_invoice->project_id;
                        $new_invoice_data['show_quantity_as'] = $_invoice->show_quantity_as;
                        $new_invoice_data['currency']         = $_invoice->currency;
                        $new_invoice_data['subtotal']         = $_invoice->subtotal;
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
                                    if($_invoice->type !== 'product')
                                    {
                                        $cust = $this->ci->stripe_core->get_customer($new_invoice_customer_id);
                                        $card_id = $cust->default_source;
                                        $description = format_invoice_number($id);
                                        $desc = "".$description."";
                                        $amt = strcasecmp($_invoice->currency_name, 'JPY') == 0 ? intval($new_invoice_data['total']) : $new_invoice_data['total'] * 100;
                                        $charge = ["amount" => $amt,
                                            "currency" => strtolower($_invoice->currency_name),
                                            "customer" => $cust->id,
                                            "source" => $card_id,
                                            "description" => "Monthly",
                                            "metadata" => array("email" => $cust->email),
                                            "receipt_email" => $cust->email];

                                            $charge1 = $this->ci->stripe_core->create_charge($charge);
                                            $inv_items = [
                                                'customer' =>  $new_invoice_customer_id,
                                                'amount' => $amt,
                                                'currency' => strtolower($_invoice->currency_name),
                                                'description' => $desc,
                                            ];
                                            $invoice_item = $this->ci->stripe_core->create_invoice_item($inv_items);
                                            $invoice_stripe = array(
                                                                    "customer" => $new_invoice_customer_id,
                                                                    "description" => $desc,
                                                                    "collection_method" => 'charge_automatically',
                                                            );
                                            $inv = $this->ci->stripe_core->create_invoice($invoice_stripe);
                                            $inv_pay  = $this->ci->stripe_core->create_invoice_pay($inv->id);
                                                    $this->db->where('id', $id);
                                                $this->db->update(db_prefix() . 'invoices', [
                                                    'addedfrom'                => $_invoice->addedfrom,
                                                    'sale_agent'               => $_invoice->sale_agent,
                                                    'invoice_stripe_id'        => $inv->id,
                                                    'status'                   => '2',
                                                    'cancel_overdue_reminders' => $_invoice->cancel_overdue_reminders,
                                                ]);
                                                $successUrl = site_url('gateways/stripe/success/' . $_invoice->id . '/' . $_invoice->hash);
                                                $cancelUrl  = site_url('invoice/' . $_invoice->id . '/' . $_invoice->hash);

                                                $items = [
                                                    'name'     => $desc,
                                                    'amount'   => $amt,
                                                    'currency' => strtolower($_invoice->currency_name),
                                                    'quantity' => 1,
                                                ];
                                                $sessionData = [
                                                      'payment_method_types' => ['card'],
                                                      'line_items'           => [$items],
                                                      'success_url'          => $successUrl,
                                                      'cancel_url'           => $cancelUrl,
                                                      'payment_intent_data'  => [
                                                          'description' => $description,
                                                          'metadata'    => [
                                                                'ClientId'    => $_invoice->clientid,
                                                                'InvoiceId'   => $_invoice->id,
                                                                'InvoiceHash' => $_invoice->hash,
                                                        ],
                                                      ],
                                              ];
                                              $session = $this->ci->stripe_core->create_session($sessionData);
                                    }

                                    $tags = get_tags_in($_invoice->id, 'invoice');
                                    handle_tags_save($tags, $id, 'invoice');

                                    // Get the old expense custom field and add to the new

                                    $custom_fields = get_custom_fields('invoice');
                                    if(!empty($custom_fields))
                                    {
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
                    }
                  }
                 }
              }
              else
              {
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
                if (date('Y-m-d') >= $re_create_at)
                {
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
                    $new_invoice_subscription = $_invoice->subscription_id;
                    $new_invoice_stripe_id = $_invoice->invoice_stripe_id;
                    $new_invoice_customer_id = $_invoice->customer_id;
                    $new_invoice_data['project_id']       = $_invoice->project_id;
                    $new_invoice_data['show_quantity_as'] = $_invoice->show_quantity_as;
                    $new_invoice_data['currency']         = $_invoice->currency;
                    $new_invoice_data['subtotal']         = $_invoice->subtotal;
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
                                $tags = get_tags_in($_invoice->id, 'invoice');
                                handle_tags_save($tags, $id, 'invoice');

                                // Get the old expense custom field and add to the new

                                $custom_fields = get_custom_fields('invoice');
                                if(!empty($custom_fields))
                                {
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
                }
              }
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
               foreach ($_renewals_ids_data as $renewed_invoice_data) {
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
               if ($sent == true) {
                   array_push($email_send_to_by_staff_and_invoice, $unique_send);
                   $this->emails_model->send_simple_email($member['email'], _l('not_recurring_invoices_cron_activity_heading'), $recurring_invoices_email_data);
               }
           }
           load_admin_language();
       }
    }

   public function place_order($product_id = false)
    {
        if (0 != get_option('product_menu_disabled')) {
            $this->session->unset_userdata('cart_data');
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        $this->load->model('products/order_model');
        $this->load->model('products/products_model');
        if (!is_client_logged_in()) {
            set_alert('warning', _l('clients_login_heading_no_register'));
            redirect(site_url(''));
        }
        $message          = '';
        $post = $this->input->post();
        unset($post['csrf_token_name']);
        unset($post['taxes']);
        unset($post['shipping_cost']);
        if (!empty($post)) {
            $post['product_items'] = $this->sort_cart($post['product_items']);
            $return_data = $this->order_model->add_invoice_order($post);
            $this->db->where('id',$return_data['invoice_id']);
            $this->db->update(db_prefix().'invoices',['type' => 'product']);
            if ($return_data['status']) {
                $this->session->unset_userdata('cart_data');
                set_alert('success', _l('order_success'));
                if ($return_data['single_invoice']) {
                    redirect(site_url('invoice/' . $return_data['invoice_id'] . '/' . $return_data['invoice_hash']), 'refresh');
                }
                redirect(site_url('clients/invoices'), 'refresh');
            }
            if (!$return_data['status']) {
                set_alert('error', _l('order_fail'));
                $message .= $return_data['message'];
            }
        }
        $cart_data = $this->sort_cart($this->session->cart_data);
        if (empty($cart_data)) {
            set_alert('danger', _l('Cart is empty'));
            redirect(site_url('products/client/'));
        }
        $data['products'] = $product = $this->products_model->get_by_cart_product($cart_data);
        if (empty($product)) {
            set_alert('danger', _l('Products in Cart not found'));
            redirect(site_url('products/client/'));
        }
        $all_taxes        = [];
        $init_tax         = [];
        $apply_shipping   = false;
        foreach ($product as $value) {
            if (!$value->is_digital) {
                if ((int) $value->quantity_number < 1) {
                    $this->remove_cart($value->id, $value->product_variation_id ?? '', true);
                    $message .= $value->product_name . ' is out of stock so removed from cart <br>';
                    continue;
                }
                if ((int) $value->quantity > (int) $value->quantity_number) {
                    $value->quantity = $value->quantity_number;
                    $message         .= $value->product_name . ' is only ' . $value->quantity_number . ' in stock so quantity reduced to that quantity <br>';
                }
            }
            $value->apply_shipping = false;
            if (!$value->recurring && !$value->is_digital) {
                $value->apply_shipping = true;
                $apply_shipping = true;
            }
            $taxes_arr       = [];
            $value->taxname  = $taxes  = unserialize($value->taxes);
            if ($taxes) {
                foreach ($taxes as $tax) {
                    if (!is_array($tax)) {
                        $tmp_taxname = $tax;
                        $tax_array   = explode('|', $tax);
                    } else {
                        $tax_array   = explode('|', $tax['taxname']);
                        $tmp_taxname = $tax['taxname'];
                        if ('' == $tmp_taxname) {
                            continue;
                        }
                    }
                    $init_tax[$tmp_taxname][]  = ($value->rate * $value->quantity) / 100 * $tax_array[1];
                    $all_taxes[$tmp_taxname]   = $taxes_arr[]   = ['name' => $tmp_taxname, 'taxrate' => $tax_array[1], 'taxname' => $tax_array[0]];
                }
            }
            $value->taxes = $taxes_arr;
        }
        $shipping_cost = 0;
        $base_shipping_cost = 0;
        $shipping_tax = 0;
        if ($apply_shipping) {
            $taxname = (!empty((get_option('product_tax_for_shipping_cost')))) ? unserialize(get_option('product_tax_for_shipping_cost')) : '';
            $shipping_cost = $base_shipping_cost = get_option('product_flat_rate_shipping');
            $shipping_tax = 0;
            if ($taxname) {
                foreach ($taxname as $tax) {
                    if (!is_array($tax)) {
                        $tmp_taxname = $tax;
                        $tax_array   = explode('|', $tax);
                    } else {
                        $tax_array   = explode('|', $tax['taxname']);
                        $tmp_taxname = $tax['taxname'];
                        if ('' == $tmp_taxname) {
                            continue;
                        }
                    }
                    $shipping_tax  += $tax_array[1];
                    $shipping_cost += ($base_shipping_cost) / 100 * $tax_array[1];
                }
            }
        }
        $data['shipping_cost']    = $shipping_cost;
        $data['shipping_base']    = $base_shipping_cost;
        $data['shipping_tax']     = $shipping_tax;
        $data['all_taxes']        = $all_taxes;
        $data['init_tax']         = $init_tax;
        $data['message']          = $message;
        $data['title']            = _l('confirm') . ' ' . _l('place_order');
        $data['base_currency']    = $this->currencies_model->get_base_currency();
        $this->data($data);
        $this->view('clients/place_order');
        $this->layout();
    }
    public function remove_cart($product_id = null, $product_variation_id = null, $return = false)
    {
        if (empty($product_id)) {
            $product_id = $this->input->post('product_id');
        }
        if (empty($product_variation_id)) {
            $product_variation_id = $this->input->post('product_variation_id');
        }
        $newdata['cart_data'] = $this->session->cart_data;
        foreach ($newdata['cart_data'] as $key => $value) {
            if ($product_id == $value['product_id'] && $product_variation_id == $value['product_variation_id']) {
                unset($newdata['cart_data'][$key]);
            }
        }
        $cart_data = [];
        foreach ($newdata['cart_data'] as $value) {
            $cart_data[] = $value;
        }
        $newdata['cart_data'] = $cart_data;
        $this->session->set_userdata($newdata);
        if (empty($newdata['cart_data'])) {
            set_alert('danger', _l('Cart is empty'));
            $res['status'] = false;
            if ($return) {
                return json_encode($res);
            }
            echo json_encode($res);

            return;
        }
        $res['status'] = true;
        $res['cart_data'] = $newdata['cart_data'];
        if ($return) {
            return json_encode($res);
        }
        echo json_encode($res);
    }
    private function sort_cart($cart_data)
    {
        $cart_data_keys = array_keys($cart_data);
        $first_index = 0;
        while ($first_index < count($cart_data_keys) - 1) {
            $sorted_count = 0;
            for ($second_index = $first_index + 2; $second_index < count($cart_data_keys); $second_index++) {
                if ($cart_data[$cart_data_keys[$first_index]]['product_id'] == $cart_data[$cart_data_keys[$second_index]]['product_id']) {
                    $replace_cart_item = $cart_data[$cart_data_keys[$second_index]];
                    for ($third_index = $second_index; $third_index > $first_index + $sorted_count + 1; $third_index--) {
                        $cart_data[$cart_data_keys[$third_index]] = $cart_data[$cart_data_keys[$third_index - 1]];
                    }
                    $cart_data[$cart_data_keys[$first_index + $sorted_count + 1]] = $replace_cart_item;
                    $sorted_count = $sorted_count + 1;
                }
            }
            $first_index = $first_index + $sorted_count + 1;
        }
        return $cart_data;
    }
    public function autocharge_due_date_invoice_gpw()
    {
        $this->ci             = &get_instance();
        $invoice_hour_auto_operations = get_option('invoice_auto_operations_hour');

        $new_recurring_invoice_action = get_option('new_recurring_invoice_action');

        // $invoices_create_invoice_from_recurring_only_on_paid_invoices = get_option('invoices_create_invoice_from_recurring_only_on_paid_invoices');
        $this->load->model('invoices_model');
        $this->db->select('*');
        $this->db->from(db_prefix() . 'invoices');

        // if ($invoices_create_invoice_from_recurring_only_on_paid_invoices == 1) {
        //     // Includes all recurring invoices with paid status if this option set to Yes
        //     $this->db->where('status', 2);
        // }
        $this->db->where('status !=', 2);
        $this->db->where('status !=', 6);
        $invoices = $this->db->get()->result_array();
        $_renewals_ids_data = [];
        $total_renewed      = 0;
        foreach ($invoices as $invoice)
        {
            $_invoice                     = $this->invoices_model->get($invoice['id']);
            if(isset($_invoice) && !empty($_invoice))
            {
                $this->db->select('*');
                $this->db->from(db_prefix() . 'clients');
                $this->db->where('userid =', $_invoice->clientid);
                $clientdata = $this->db->get()->row();
                $stripe_id = !empty($clientdata->stripe_id) ? $clientdata->stripe_id : '';
                // Current date
                $date = new DateTime(date('Y-m-d'));
                // Check if is first recurring
                $due_date = date('Y-m-d', strtotime($_invoice->duedate));
                if (date('Y-m-d') >= $due_date && $stripe_id != '')
                {
                    $stripe_customer = $this->stripe_core->get_customer($stripe_id);   
                    $id = $_invoice->id;
                    if($stripe_customer != '' && $stripe_customer->default_source != '')
                    {
                        $cust = $stripe_customer;
                        $card_id = $cust->default_source;
                        $description = format_invoice_number($id);
                        $desc = "".$description."";
                        $amt = strcasecmp($_invoice->currency_name, 'JPY') == 0 ? intval($invoice['total']) : $invoice['total'] * 100;
                            $inv_items = [
                                'customer' =>  $cust->id,
                                'amount' => $amt,
                                'currency' => strtolower($_invoice->currency_name),
                                'description' => $desc,
                            ];
                                        $invoice_item = $this->stripe_core->create_invoice_item($inv_items);
                                        $invoice_stripe = array(
                                                                "customer" => $cust->id,
                                                                "description" => $desc,
                                                                "collection_method" => 'charge_automatically',
                                                        );
                                        $inv = $this->stripe_core->create_invoice($invoice_stripe);
                                        $inv_pay  = $this->stripe_core->create_invoice_pay($inv->id);
                                                $this->db->where('id', $id);
                                            $this->db->update(db_prefix() . 'invoices', [
                                                'addedfrom'                => $_invoice->addedfrom,
                                                'sale_agent'               => $_invoice->sale_agent,
                                                'invoice_stripe_id'        => $inv->id,
                                                'status'                   => '2',
                                                'cancel_overdue_reminders' => $_invoice->cancel_overdue_reminders,
                                            ]);
                                            $successUrl = site_url('gateways/stripe/success/' . $_invoice->id . '/' . $_invoice->hash);
                                            $cancelUrl  = site_url('invoice/' . $_invoice->id . '/' . $_invoice->hash);

                                            $items = [
                                                'name'     => $desc,
                                                'amount'   => $amt,
                                                'currency' => strtolower($_invoice->currency_name),
                                                'quantity' => 1,
                                            ];
                                            $sessionData = [
                                                  'payment_method_types' => ['card'],
                                                  'line_items'           => [$items],
                                                  'success_url'          => $successUrl,
                                                  'cancel_url'           => $cancelUrl,
                                                  'payment_intent_data'  => [
                                                      'description' => $description,
                                                      'metadata'    => [
                                                            'ClientId'    => $_invoice->clientid,
                                                            'InvoiceId'   => $_invoice->id,
                                                            'InvoiceHash' => $_invoice->hash,
                                                    ],
                                                  ],
                                          ];
                                          $session = $this->stripe_core->create_session($sessionData);

                    }
                    else
                    {
                      $this->db->select('*');
                        $this->db->from(db_prefix() . 'contacts');
                        $this->db->where('userid =', $_invoice->clientid);
                        $this->db->where('is_primary =','1');
                        $primary = $this->db->get()->row();
                        $card_details = $this->db->query('select * from '.db_prefix().'contact_card_details where contact_id="'.$primary->id.'"')->row_array();
                        if(!empty($card_details))
                        {
                            $paymentmethod = array(
                                  'type' => 'card',
                                  'card' => [
                                    'number' => $card_details['card_number'],
                                    'exp_month' => '01',
                                    'exp_year' => '24',
                                    'cvc' => $card_details['cvv'],
                                  ],
                                  );
                            $payment = $this->stripe_core->create_payment($paymentmethod);
                            $payid= $payment->id;
                            $stripe_customer = $this->stripe_core->get_customer($stripe_id);
                            if(!empty($stripe_customer))
                            {
                                $pay_customer = array('customer'=> $stripe_id);
                                $pay = $this->stripe_core->attach_payment($payid,$pay_customer);
                                $amt = strcasecmp($_invoice->currency_name, 'JPY') == 0 ? intval($invoice['total']) : $invoice['total'] * 100;
                                $token = array(
                                                    "card" => array(
                                                        "name" => $card_details['card_name'],
                                                        "number" => $card_details['card_number'],
                                                        "exp_month" =>$card_details['expire_month'],
                                                        "exp_year" => $card_details['expire_year'],
                                                        "cvc" => $card_details['cvv']
                                                    )
                                            );
                                $token_create = $this->stripe_core->create_token($token);
                                $card = $token_create->card;
                                $card_id = $card->id;
                                $cust_tok = $this->stripe_core->update_customer($stripe_id,['source'=>$token_create->id]);
                                $description = format_invoice_number($id);
                                $desc = "".$description."";
                                $inv_items = [
                                    'customer' =>  $stripe_id,
                                    'amount' => $amt,
                                    'currency' => strtolower($_invoice->currency_name),
                                    'description' => $desc,
                                ];
                                $invoice_item = $this->stripe_core->create_invoice_item($inv_items);
                                $invoice = array(
                                                        "customer" => $stripe_id,
                                                        "description" => $desc,
                                                        "collection_method" => 'charge_automatically',
                                                 );
                                $inv = $this->stripe_core->create_invoice($invoice);
                                 $invoice_stripe = array(
                                                                        "customer" => $cust->id,
                                                                        "description" => $desc,
                                                                        "collection_method" => 'charge_automatically',
                                                                );
                                                $inv = $this->stripe_core->create_invoice($invoice_stripe);
                                                $inv_pay  = $this->stripe_core->create_invoice_pay($inv->id);
                                                        $this->db->where('id', $id);
                                                    $this->db->update(db_prefix() . 'invoices', [
                                                        'addedfrom'                => $_invoice->addedfrom,
                                                        'sale_agent'               => $_invoice->sale_agent,
                                                        'invoice_stripe_id'        => $inv->id,
                                                        'status'                   => '2',
                                                        'cancel_overdue_reminders' => $_invoice->cancel_overdue_reminders,
                                                    ]);
                                                    $successUrl = site_url('gateways/stripe/success/' . $_invoice->id . '/' . $_invoice->hash);
                                                    $cancelUrl  = site_url('invoice/' . $_invoice->id . '/' . $_invoice->hash);
        
                                                    $items = [
                                                        'name'     => $desc,
                                                        'amount'   => $amt,
                                                        'currency' => strtolower($_invoice->currency_name),
                                                        'quantity' => 1,
                                                    ];
                                                    $sessionData = [
                                                          'payment_method_types' => ['card'],
                                                          'line_items'           => [$items],
                                                          'success_url'          => $successUrl,
                                                          'cancel_url'           => $cancelUrl,
                                                          'payment_intent_data'  => [
                                                              'description' => $description,
                                                              'metadata'    => [
                                                                    'ClientId'    => $_invoice->clientid,
                                                                    'InvoiceId'   => $_invoice->id,
                                                                    'InvoiceHash' => $_invoice->hash,
                                                            ],
                                                          ],
                                                  ];
                                                  $session = $this->stripe_core->create_session($sessionData);
                            }
                     }
                    }

                  }
                 }
              }
    }

}
