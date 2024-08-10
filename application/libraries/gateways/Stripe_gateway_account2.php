<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stripe_gateway_account2 extends App_gateway
{
    public $webhookEndPoint;

    public function __construct()
    {
        $this->webhookEndPoint = site_url('gateways/stripe/webhook_endpoint');

        /**
        * Call App_gateway __construct function
        */
        parent::__construct();

        /**
        * REQUIRED
        * Gateway unique id
        * The ID must be alpha/alphanumeric
        */
        $this->setId('stripeaccount2');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Stripe GP.Marketing');

        /**
         * Add gateway settings
        */
        $this->setSettings([
            [
                'name'  => 'api_publishable_key',
                'label' => 'settings_paymentmethod_stripe_api_publishable_key',
            ],
            [
                'name'      => 'api_secret_key',
                'encrypted' => true,
                'label'     => 'settings_paymentmethod_stripe_api_secret_key',
            ],
            [
                'name'          => 'description_dashboard',
                'label'         => 'settings_paymentmethod_description',
                'type'          => 'textarea',
                'default_value' => 'Payment for Invoice {invoice_number}',
            ],
            [
                'name'          => 'currencies',
                'label'         => 'settings_paymentmethod_currencies',
                'default_value' => 'USD,CAD',
            ],
            [
                'name'          => 'allow_primary_contact_to_update_credit_card',
                'type'          => 'yes_no',
                'default_value' => 1,
                'label'         => 'allow_primary_contact_to_update_credit_card',
            ],
        ]);

        hooks()->add_action('before_render_payment_gateway_settings', 'stripe_gateway_webhook_check2');
    }

    /**
     * Get the current webhook object based on the endpoint
     *
     * @return boolean|\Stripe\WebhookEndpoint
     */
    public function get_webhook_object()
    {
        if (!class_exists('stripe_core_gpw', false)) {
            $this->ci->load->library('stripe_core_gpw');
        }

        $endpoints = $this->ci->stripe_core_gpw->list_webhook_endpoints();
        $webhook   = false;

        foreach ($endpoints->data as $endpoint) {
            if ($endpoint->url == $this->webhookEndPoint) {
                $webhook = $endpoint;

                break;
            }
        }

        return $webhook;
    }

    /**
     * Determine the Stripe environment based on the keys
     *
     * @return string
     */
    public function environment()
    {
        $environment = 'production';
        $apiKey      = $this->decryptSetting('api_secret_key');

        if (strpos($apiKey, 'sk_test') !== false) {
            $environment = 'test';
        }

        return $environment;
    }

    /**
     * Check whether the environment is test
     *
     * @return boolean
     */
    public function is_test()
    {
        return $this->environment() === 'test';
    }

    /**
     * Process the payment
     *
     * @param  array $data
     *
     * @return mixed
     */
    public function process_payment($data)
    {
        // echo "here - Stripe_gateway_account2"; exit;
        $this->ci->load->library('stripe_core_gpw');


        $description = str_replace('{invoice_number}', format_invoice_number($data['invoiceid']), $this->getSetting('description_dashboard'));

        $items = [
                'name'     => $description,
                'amount'   => strcasecmp($data['invoice']->currency_name, 'JPY') == 0 ? intval($data['amount']) : $data['amount'] * 100,
                'currency' => strtolower($data['invoice']->currency_name),
                'quantity' => 1,
        ];

        $successUrl = site_url('gateways/stripe/success/' . $data['invoice']->id . '/' . $data['invoice']->hash);
        $cancelUrl  = site_url('invoice/' . $data['invoiceid'] . '/' . $data['invoice']->hash);

        $sessionData = [
              'payment_method_types' => ['card'],
              'line_items'           => [$items],
              'success_url'          => $successUrl,
              'cancel_url'           => $cancelUrl,
              'payment_intent_data'  => [
                  'description' => $description,
                  'metadata'    => [
                        'ClientId'    => $data['invoice']->clientid,
                        'InvoiceId'   => $data['invoice']->id,
                        'InvoiceHash' => $data['invoice']->hash,
                ],
              ],
        ];
        // echo '<pre>'; print_r($sessionData); exit;

        if ($data['invoice']->client->stripe_id) {
            $sessionData['customer'] = $data['invoice']->client->stripe_id;
        }

         $cf      = get_custom_fields('invoice');
        // $data['invoice']->custom_fields->invoice_stripe_customer_id['value'] = '';
          foreach ($cf as $custom_field) {
              $custom_field['value']                 = get_custom_field_value($data['invoice']->id, $custom_field['id'], 'invoice');
            //   echo '<pre>'; print_r($custom_field['value']); exit;
              if($custom_field['slug'] == 'invoice_stripe_customer_id') {
                // $data['invoice']->custom_fields->invoice_stripe_customer_id = $custom_field;
              }
          }
        //   if($data['invoice']->custom_fields->invoice_stripe_customer_id['value'] != ""){
            // $sessionData['customer'] = $data['invoice']->custom_fields->invoice_stripe_customer_id['value'];
        //   }

        $email_address = '';
        $this->ci->load->library('stripe_core_gpw');
        if(is_client_logged_in())
        {
            $contact1 = $this->ci->clients_model->get_contact(get_contact_user_id());
            if ($contact1->email) 
            {
                $email_address = $contact1->email;
            }
        }
        else
        {
            $contact1 = $this->ci->db->query('select * from '.db_prefix().'contacts where userid = '.$data['invoice']->clientid.' and is_primary = 1')->row();
            if ($contact1->email) 
            {
                $email_address = $contact1->email;
            }
        }
        if (is_client_logged_in() && !$data['invoice']->client->stripe_id) {
            $contact = $this->ci->clients_model->get_contact(get_contact_user_id());
            if ($contact->email) {
                $email_address = $contact->email;
                $customer = $this->ci->stripe_core_gpw->get_customer_by_email($contact->email);
                if(!empty($customer->data))
                {
                  $cust = $customer->data;
                  $user_stripe_id = $cust[0]->id;
                }
                if(!empty($user_stripe_id))
                {
                  $sessionData['customer'] = $user_stripe_id;
                }
                else
                {
                  $clients = $this->ci->db->query('select * from '.db_prefix().'clients where userid = '.$data['invoice']->clientid)->row();
                  $client_data['email'] = $contact->email;
                  $client_data['description'] = $contact->firstname .' '. $contact->lastname;
                  $client_data['address'] = array("city" => $clients->city,
                                                           "country" => get_country_short_name($clients->country),
                                                            "line1" => $clients->address,
                                                            "line2" => $clients->address,
                                                            "postal_code" => $clients->zip,
                                                            "state" => $clients->state);
                  $client_data['shipping'] = array('address'=> $client_data['address'],
                                                'name' => $contact->firstname .' '. $contact->lastname,
                                                 'phone' => $clients->phonenumber);
                  $customers = $this->ci->stripe_core_gpw->create_customer($client_data);
                  $sessionData['customer'] = $customers->id;
                }
            }
        }

        $CI = &get_instance();
        if(is_client_logged_in())
        {
            $contact_id = get_contact_user_id();
        }
        else
        {
            $contact1 = $this->ci->db->query('select * from '.db_prefix().'contacts where userid = '.$data['invoice']->clientid.' and is_primary = 1')->row();
            if ($contact1->email) 
            {
                $email_address = $contact1->email;
            }
            $contact_id = $contact1->id;
        }
        if($data['invoice']->type !== 'product' && $CI->uri->uri_string() !== 'admin/invoices/record_payment')
        {
        $desc = "".$description."";
        $CI = &get_instance();
        if(isset($data['subscription']) && $data['subscription'] != '')
        {
            $subscription = $CI->db->query('select * from '.db_prefix().'subscriptions where id="'.$data['subscription'].'"')->row();
        }
        $card_details = $CI->db->query('select * from '.db_prefix().'contact_card_details where contact_id="'.$contact_id.'"')->row_array();
    //     $paymentmethod = array(
    //           'type' => 'card',
    //           'card' => [
    //             'number' => convert_uudecode($card_details['card_number']),
    //             'exp_month' => convert_uudecode($card_details['expire_month']),
    //             'exp_year' => convert_uudecode($card_details['expire_year']),
    //             'cvc' => convert_uudecode($card_details['cvv']),
    //           ],
    //           );
    //   try {
    //     $payment = $this->ci->stripe_core_gpw->create_payment($paymentmethod);
    //     } catch (Exception $e) {
    //           set_alert('warning', $e->getError()->message);
    //         redirect(site_url('invoice/' . $data['invoiceid'] . '/' . $data['hash']));
    //     }
    //     $payid= $payment->id;
    //     $pay_customer = array('customer'=>$sessionData['customer']);
    //     try {
    //     $pay = $this->ci->stripe_core_gpw->attach_payment($payid,$pay_customer);
    //     } catch (Exception $e) {
    //           set_alert('warning', $e->getError()->message);
    //         redirect(site_url('invoice/' . $data['invoiceid'] . '/' . $data['hash']));
    //     }
        $amt = strcasecmp($data['invoice']->currency_name, 'JPY') == 0 ? intval($data['amount']) : $data['amount'] * 100;
    //     $token = array(
    //                         "card" => array(
    //                          'number' => convert_uudecode($card_details['card_number']),
    //                         'exp_month' => convert_uudecode($card_details['expire_month']),
    //                         'exp_year' => convert_uudecode($card_details['expire_year']),
    //                         'cvc' => convert_uudecode($card_details['cvv']),
    //                         )
    //                 );
    //     try {
    //     $token_create = $this->ci->stripe_core_gpw->create_token($token);
    //     } catch (Exception $e) {
    //         set_alert('warning', $e->getError()->message);
    //         redirect(site_url('invoice/' . $data['invoiceid'] . '/' . $data['hash']));
    //     }
    //     $card = $token_create->card;
    //     $card_id = $card->id;

    //     try {
    //     $cust_tok = $this->ci->stripe_core_gpw->update_customer($sessionData['customer'],['source'=>$token_create->id]);
    //     } catch (Exception $e) {
    //         set_alert('warning', $e->getError()->message);
    //         redirect(site_url('invoice/' . $data['invoiceid'] . '/' . $data['hash']));
    //     }
        $inv_items = [
            'customer' =>  $sessionData['customer'],
            'amount' => $amt,
            'currency' => strtolower($data['invoice']->currency_name),
            'description' => $desc,
        ];
        try {
        $invoice_item = $this->ci->stripe_core_gpw->create_invoice_item($inv_items);
        } catch (Exception $e) {
            set_alert('warning', $e->getError()->message);
            redirect(site_url('invoice/' . $data['invoiceid'] . '/' . $data['hash']));
        }
        $invoice = array(
                                "customer" => $sessionData['customer'],
                                "description" => $desc,
                                "collection_method" => 'charge_automatically',
                         );
        // try {
        // $inv = $this->ci->stripe_core_gpw->create_invoice($invoice);
        // $inv_pay  = $this->ci->stripe_core_gpw->create_invoice_pay($inv->id);
        // } catch (Exception $e) {
        //     set_alert('warning', $e->getError()->message);
        //     redirect(site_url('invoice/' . $data['invoiceid'] . '/' . $data['hash']));
        // }

        // if(!empty($subscription))
        // {
        //   $update = $this->ci->db->update(db_prefix().'invoices',['customer_id'=>$sessionData['customer'],'subscription_id'=>$subscription->stripe_subscription_id,'invoice_stripe_id'=> $inv->id]);
        // }
        // else
        // {
        //     $update = $this->ci->db->update(db_prefix().'invoices',['customer_id'=>$sessionData['customer'],'invoice_stripe_id'=> $inv->id]);
        // }
        $this->ci->db->where('userid',$data['invoice']->clientid);
        $this->ci->db->update(db_prefix().'clients',['stripe_id'=>$sessionData['customer'],'stripe_connect'=>'gp_marketing']);
        }
        try {
            $session = $this->ci->stripe_core_gpw->create_session($sessionData);
        } catch (Exception $e) {
            set_alert('warning', $e->getError()->message);
            redirect(site_url('invoice/' . $data['invoiceid'] . '/' . $data['hash']));
        }
        redirect_to_stripe_checkout2($session->id);
    }
}

function stripe_gateway_webhook_check2($gateway)
{
    if ($gateway['id'] === 'stripe') {
        $CI = &get_instance();

        $CI->load->library('stripe_core_gpw');

        if ($CI->stripe_core_gpw->has_api_key() && $gateway['active'] == '1') {
            try {
                $webhook = $CI->stripe_gateway->get_webhook_object();
            } catch (Exception $e) {
                echo '<div class="alert alert-warning">';
                // useful when user add wrong keys
                // e.q. This API call cannot be made with a publishable API key. Please use a secret API key. You can find a list of your API keys at https://dashboard.stripe.com/account/apikeys.
                echo $e->getError()->message;
                echo '</div>';

                return;
            }

            $environment = $CI->stripe_gateway->environment();
            $endpoint    = $CI->stripe_gateway->webhookEndPoint;

            if ($CI->session->has_userdata('stripe-webhook-failure')) {
                echo '<div class="alert alert-warning" style="margin-bottom:15px;">';
                echo '<h4>Error: ' . $CI->session->userdata('stripe-webhook-failure') . '</h4>';
                echo 'The system was unable to create the <b>required</b> webhook endpoint for Stripe.';
                echo '<br />You should consider creating webhook manually directly via Stripe dashboard for your environment (' . $environment . ')';
                echo '<br /><br /><b>Webhook URL:</b><br />' . $endpoint;
                echo '<br /><br /><b>Webhook events:</b><br />' . implode(',<br />', $CI->stripe_core_gpw->get_webhook_events());
                echo '</div>';
            }

            if (!$webhook || !startsWith($webhook->url, site_url())) {
                echo '<div class="alert alert-warning">';
                echo 'Webhook endpoint (' . $endpoint . ') not found for ' . $environment . ' environment.';
                echo '<br />Click <a href="' . site_url('gateways/stripe/create_webhook') . '">here</a> to create the webhook directly in Stripe.';
                echo '</div>';
            } elseif ($webhook && $webhook->id != get_option('stripe_webhook_id')) {
                echo '<div class="alert alert-warning">';
                echo 'The application stored Stripe webhook id does not match the configured webhook.';
                echo '<br />Click <a href="' . site_url('gateways/stripe/create_webhook?recreate=true') . '">here</a> to re-create the webhook directly in Stripe and delete the old webhook.';
                echo '</div>';
            } elseif ($webhook && $webhook->status != 'enabled') {
                echo '<div class="alert alert-warning">';
                echo 'Your Stripe configured webhook is disabled, you should consider enabling your webhook via Stripe dashboard or by clicking <a href="' . site_url('gateways/stripe/enable_webhook') . '">here</a>.';
                echo '</div>';
            }
        }
    }
}
