<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stripe extends App_Controller
{
    protected $subscriptionMetaKey = 'pcrm-subscription-hash';

    /**
     * Create the application Stripe webhook endpoint
     *
     * @return mixed
     */
    public function create_webhook()
    {
        if (staff_can('edit', 'settings')) {
            $this->load->library('stripe_core');

            try {
                $webhooks = $this->stripe_core->list_webhook_endpoints();

                foreach ($webhooks->data as $webhook) {
                    if ((isset($webhook->metadata->identification_key) &&
                                            $webhook->metadata->identification_key == get_option('identification_key')) ||
                        $webhook->url == $this->stripe_gateway->webhookEndPoint) {
                        $this->stripe_core->delete_webhook($webhook->id);
                    }
                }

                if ($this->input->get('recreate')) {
                    update_option('stripe_webhook_id', '');
                    update_option('stripe_webhook_signing_secret', '');
                }
            } catch (Exception $e) {
            }

            try {
                $this->stripe_core->create_webhook();
                set_alert('success', _l('webhook_created'));
            } catch (Exception $e) {
                $this->session->set_flashdata('stripe-webhook-failure', $e->getMessage());
            }

            redirect(admin_url('settings/?group=payment_gateways&tab=online_payments_stripe_tab'));
        }
    }

    /**
     * Enable the application Stripe webhook endpoint
     *
     * @return mixed
     */
    public function enable_webhook()
    {
        if (staff_can('edit', 'settings')) {
            $this->load->library('stripe_core');

            $this->stripe_core->enable_webhook(get_option('stripe_webhook_id'));

            redirect(admin_url('settings/?group=payment_gateways&tab=online_payments_stripe_tab'));
        }
    }

    /**
     * The application Stripe webhook endpoint
     *
     * @return mixed
     */
    public function webhook_endpoint()
    {
        log_activity('Stripe Entered inside webhook_endpoint function');
        $this->load->library('stripe_core');
        $this->load->library('stripe_subscriptions');
        $this->load->model('subscriptions_model');

        $payload = @file_get_contents('php://input');
        $event   = null;

        if (!isset($_SERVER['HTTP_STRIPE_SIGNATURE'])) {
            return;
        }

        log_activity('Stripe Payload Data [payload:' . json_encode($payload) . ']');
        // Validate the webhook
        try {
            $event = $this->stripe_core->construct_event($payload, get_option('stripe_webhook_signing_secret'));
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
          http_response_code(400); // PHP 5.4 or greater
          log_activity('Stripe UnexpectedValueException [error:' . $e->getMessage() . ']');
          exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
              http_response_code(400); // PHP 5.4 or greater
              log_activity('Stripe SignatureVerificationException [error:' . $e->getMessage() . ']');
              exit();
        }
        log_activity('Stripe Event Data [event:' . json_encode($event) . ']');
        try {
            // Handle the checkout.session.completed event
            if ($event->type == 'checkout.session.completed') {
                $session = $event->data->object;

                if ($session->payment_intent) {
                    $payment = $this->stripe_core->retrieve_payment_intent($session->payment_intent);

                    if (isset($payment->metadata->InvoiceId)) {
                        $this->load->model('invoices_model');

                        $invoice = $this->invoices_model->get(
                            $payment->metadata->InvoiceId,
                            // 1 Stripe account, multiple CRM installations
                            isset($payment->metadata->InvoiceHash) ? [
                                'hash' => $payment->metadata->InvoiceHash,
                            ] : []
                        );

                        if ($invoice) {
                            $this->load->model('payments_model');
                            if (!$this->payments_model->transaction_exists($payment->id, $invoice->id)) {
                                $this->stripe_gateway->addPayment([
                                      'amount'        => (strcasecmp($invoice->currency_name, 'JPY') == 0 ? $payment->amount : $payment->amount / 100),
                                      'invoiceid'     => $invoice->id,
                                      'transactionid' => $payment->id,
                                ]);
                            }

                            if (!$this->stripe_gateway->is_test()) {
                                $this->db->where('userid', $payment->metadata->ClientId);
                                $this->db->update('clients', ['stripe_id' => $payment->customer]);
                            }
                        }
                    }
                }
            } elseif ($event->type == 'customer.subscription.created') {
                $this->customerSubscriptionCreatedEvent($event);
            } elseif ($event->type == 'invoice.payment_succeeded') {
                $this->invoicePaymentSucceededEvent($event);
            } elseif ($event->type == 'invoice.payment_failed') {
                $this->invoicePaymentFailedEevent($event);
            } elseif ($event->type == 'charge.succeeded') {
                log_activity('Stripe called function invoicePaymentSucceededEventChargeSucceeded');
                $this->invoicePaymentSucceededEventChargeSucceeded($event);
                log_activity('Stripe end function invoicePaymentSucceededEventChargeSucceeded');
            }elseif ($event->type == 'invoice.payment_action_required') {
                $this->invoicePaymentActionRequiredEevent($event);
            } elseif ($event->type == 'customer.subscription.deleted') {
                $this->customerSubscriptionDeletedEvent($event);
            } elseif ($event->type == 'customer.subscription.updated') {
                $this->customerSubscriptionUpdatedEvent($event);
            } elseif ($event->type == 'customer.deleted') {
                $this->customerDeletedEvent($event);
            }
        } catch (\Exception $e) {
            log_activity('Stripe webhook error: ' . $e->getMessage());
        }
    }

    /**
     * After stripe checkout succcess
     * Used only to display success message to the customer
     *
     * @param  string $invoice_id   The invoice id the payment is made to
     * @param  strgin $invoice_hash invoice hash
     *
     * @return mixed
     */
    public function success($invoice_id, $invoice_hash)
    {
        set_alert('success', _l('online_payment_recorded_success'));
        $invoice = $this->db->query('select * from '.db_prefix().'invoices where id="'.$invoice_id.'"')->row();
        $subscription =  $this->db->query('select * from '.db_prefix().'subscriptions where stripe_subscription_id="'.$invoice->subscription_id.'"')->row();
        $invoice_stripe_id = $invoice->invoice_stripe_id;
        $client = $this->db->query('select * from '.db_prefix().'contacts where userid="'.$invoice->clientid.'"')->row();
        if(!empty($subscription))
        {
            if(!empty($subscription) && $subscription->type == 'gpm')
            {
                $this->load->library('stripe_core_gpw');
                $stripe_customer = $this->stripe_core_gpw->get_customer_by_email($client->email);
            }
            if(!empty($subscription) && $subscription->type == 'gpw')
            {
                $this->load->library('stripe_core');
                $stripe_customer = $this->stripe_core->get_customer_by_email($client->email);
            }
            $cust = $stripe_customer->data;
            $user_stripe_id = $cust[0]->id;
            $this->db->where('userid', $invoice->clientid);
            $this->db->update('clients', ['stripe_id' => $user_stripe_id]);
            redirect(site_url('invoice/' . $invoice_id . '/' . $invoice_hash));
        }
        else
        {
            try {
                 $this->load->library('stripe_core');
                $client_data = $this->stripe_core->retrieve_customer_using_invoice($invoice_stripe_id);
            } catch (Exception $e) {
                 $this->load->library('stripe_core_gpw');
                $client_data = $this->stripe_core_gpw->retrieve_customer_using_invoice($invoice_stripe_id);
            }
            $this->db->where('userid', $invoice->clientid);
            $this->db->update('clients', ['stripe_id' => $client_data->customer]);
            redirect(site_url('invoice/' . $invoice_id . '/' . $invoice_hash));
        }
        
    }

    /**
     * Handle subscription created event
     *
     * @param  \stdClass $event
     *
     * @return void
     */
    protected function customerSubscriptionCreatedEvent($event)
    {
        $subscription = $event->data->object;

        if (isset($subscription->metadata[$this->subscriptionMetaKey])) {
            $subscription = $this->stripe_subscriptions->get_subscription($subscription->id);

            $this->stripe_core->update_customer($subscription->customer, [
                    'invoice_settings' => [
                      'default_payment_method' => $subscription->default_payment_method,
                    ],
            ]);

            \Stripe\Subscription::update($subscription->id, ['default_payment_method' => '']);

            $dbSubscription = $this->subscriptions_model->get_by_hash($subscription->metadata[$this->subscriptionMetaKey]);
            $update         = ['in_test_environment' => $this->stripe_gateway->is_test() ? 1 : 0];

            if (!empty($dbSubscription->date)) {
                if ($dbSubscription->date <= date('Y-m-d')) {
                    // Updates the first billing date to be today because
                    // in the create_subscription method is now
                    $update['date'] = date('Y-m-d');
                }

                if ($dbSubscription->date > date('Y-m-d')) {
                    $update['status']                 = 'future';
                    $update['next_billing_cycle']     = strtotime($dbSubscription->date);
                    $update['stripe_subscription_id'] = $subscription->id;
                    $update['date_subscribed']        = date('Y-m-d H:i:s');
                }
            }

            $this->subscriptions_model->update($dbSubscription->id, $update);

            send_email_customer_subscribed_to_subscription_to_staff($dbSubscription);

            hooks()->do_action('customer_subscribed_to_subscription', $dbSubscription);
        }
    }

    /**
     * Handle subscription invoice payment succeded event
     *
     * @param  \stdClass $event
     *
     * @return void
     */
    protected function invoicePaymentSucceededEvent($event)
    {
        $invoice             = $event->data->object;
        $crmSubscriptionItem = null;

        // Let's check if it's really subscription created from CRM
        foreach ($invoice->lines->data as $item) {
            if (isset($item->metadata[$this->subscriptionMetaKey])) {
                $crmSubscriptionItem = $item;

                break;
            }
        }

        if (!is_null($crmSubscriptionItem)) {
            $dbSubscription = $this->subscriptions_model->get_by_hash($crmSubscriptionItem->metadata[$this->subscriptionMetaKey]);

            if ($dbSubscription) {
                if (!$this->stripe_gateway->is_test()) {
                    $this->db->where('userid', $dbSubscription->clientid);
                    $this->db->update('clients', ['stripe_id' => $invoice->customer]);
                }

                $this->subscriptions_model->update($dbSubscription->id, ['next_billing_cycle' => $crmSubscriptionItem->period->end]);
                $this->load->model('payments_model');

                $new_invoice_data = create_subscription_invoice_data($dbSubscription, $invoice);
                $this->load->model('invoices_model');

                if (!defined('STRIPE_SUBSCRIPTION_INVOICE')) {
                    define('STRIPE_SUBSCRIPTION_INVOICE', true);
                }

                $id = $this->invoices_model->add($new_invoice_data);

                if ($id) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'invoices', [
                        'addedfrom' => $dbSubscription->created_from,
                    ]);

                    // Probably created via the checkout.session.completed event type
                    if (! $this->payments_model->transaction_exists($invoice->charge)) {
                        $payment_data['paymentmode']   = 'stripe';
                        $payment_data['amount']        = $new_invoice_data['total'];
                        $payment_data['invoiceid']     = $id;
                        $payment_data['transactionid'] = $invoice->charge;

                        $this->load->model('payments_model');
                        $this->payments_model->add($payment_data, $dbSubscription->id);
                    }

                    $update = [
                        'status'                 => 'active',
                        'stripe_subscription_id' => $invoice->subscription,
                    ];

                    // In case updated previously in subscription.created event and the subscription was in future
                    if (empty($dbSubscription->date_subscribed)) {
                        $update['date_subscribed'] = date('Y-m-d H:i:s');
                    }

                    if (empty($dbSubscription->date)) {
                        $update['date'] = date('Y-m-d');
                    }

                    $this->subscriptions_model->update($dbSubscription->id, $update);
                }
            }
        }
    }
    
    /**
     * Handle subscription invoice payment succeded event for charge
     *
     * @param  \stdClass $event
     *
     * @return void
     */
    protected function invoicePaymentSucceededEventChargeSucceeded($event)
    {
        $invoice             = $event->data->object;
        $crmSubscriptionItem = null;
        
        $created_time = $event->created;
        $current_time = time();

        if (isset($invoice->metadata->InvoiceId) && !empty($invoice->metadata->InvoiceId) && isset($invoice->metadata->ClientId) && !empty($invoice->metadata->ClientId)) {
            if ($invoice->amount == $invoice->amount_captured && $invoice->amount_captured > 0) {
                if ($current_time - $created_time < 120) {
                    $payment_data['paymentmode']   = 'stripe';
                    $payment_data['amount']        = $invoice->amount_captured/100;
                    $payment_data['invoiceid']     = $invoice->metadata->InvoiceId;
                    $payment_data['transactionid'] = $invoice->balance_transaction;
                    $payment_data['note'] = 'Receipt Number: '.$invoice->receipt_number.' Receipt URL: '.$invoice->receipt_url;
                    log_activity('Stripe Payment Data [invoiceid:' . $invoice->metadata->InvoiceId . ', amount: ' . $invoice->amount_captured . ', transactionid: ' . $invoice->balance_transaction . ']');
                    $this->load->model('payments_model');
                    $this->payments_model->add($payment_data, false);
                    return;
                }
            }
        }else{
            return;
        }
        
    }

    /**
     * Handle subscription invoice payment failed event
     *
     * @param  \stdClass $event
     *
     * @return void
     */
    protected function invoicePaymentFailedEevent($event)
    {
        $invoice = $event->data->object;
        if (isset($invoice->lines->data[0]->metadata[$this->subscriptionMetaKey])) {
            $dbSubscription = $this->subscriptions_model->get_by_hash($invoice->lines->data[0]->metadata[$this->subscriptionMetaKey]);

            if ($dbSubscription) {
                $payment_intent = $this->stripe_core->retrieve_payment_intent($invoice->payment_intent);
                //log_message('error', json_encode($payment_intent, JSON_PRETTY_PRINT));
                // Will handle requires action in the event invoice.payment_action_required
                if ($payment_intent->status != 'requires_action') {
                    $this->subscriptions_model->send_email_template(
                        $dbSubscription->id,
                        $this->getStaffCCForMailTemplate($dbSubscription->created_from),
                        'subscription_payment_failed_to_customer'
                    );
                }
            }
        }
    }

    /**
     * Handle subscription invoice payment require action event event
     *
     * @param  \stdClass $event
     *
     * @return void
     */
    protected function invoicePaymentActionRequiredEevent($event)
    {
        $invoice = $event->data->object;
        if (isset($invoice->lines->data[0]->metadata[$this->subscriptionMetaKey])) {

            // Customer was on session while trying to subscribe to the invoice
            // In this case, in the Subscription.php class he will be redirected to the
            // invoice hosted url to confirm the payment
            // he already know that he need to confir the payment and no email is needed
            // perhaps Stripe will send one if configured
            if (isset($invoice->lines->data[0]->metadata['customer-on-session'])) {
                return;
            }

            $dbSubscription = $this->subscriptions_model->get_by_hash($invoice->lines->data[0]->metadata[$this->subscriptionMetaKey]);

            if ($dbSubscription) {
                $contact = $this->clients_model->get_contact(get_primary_contact_user_id($dbSubscription->clientid));

                if (!$contact) {
                    return false;
                }

                send_mail_template(
                    'subscription_payment_requires_action',
                    $dbSubscription,
                    $contact,
                    $invoice->hosted_invoice_url,
                    $this->getStaffCCForMailTemplate($dbSubscription->created_from)
                );
            }
        }
    }

    /**
     * Handle subscription updated event
     *
     * @param  \stdClass $event
     *
     * @return void
     */
    protected function customerSubscriptionUpdatedEvent($event)
    {
        $subscription = $event->data->object;
        if (isset($subscription->metadata[$this->subscriptionMetaKey])) {
            $dbSubscription = $this->subscriptions_model->get_by_hash($subscription->metadata[$this->subscriptionMetaKey]);

            if ($dbSubscription) {
                $update = [
                    // in case not yet updated e.q. because of hook or event handler failure
                    'stripe_subscription_id' => $subscription->id,
                    'status'                 => $subscription->status,
                    'next_billing_cycle'     => $subscription->current_period_end,
                    'quantity'               => $subscription->items->data[0]->quantity,
                    'ends_at'                => $subscription->cancel_at_period_end ? $subscription->current_period_end : null,
                ];

                if ($dbSubscription->status == 'future') {
                    unset($update['status']);
                    unset($update['next_billing_cycle']);
                }

                $this->subscriptions_model->update($dbSubscription->id, $update);
            }
        }
    }

    /**
     * Handle subscription deleted event
     *
     * @param  \stdClass $event
     *
     * @return void
     */
    protected function customerSubscriptionDeletedEvent($event)
    {
        $subscription = $event->data->object;
        if (isset($subscription->metadata[$this->subscriptionMetaKey])) {
            $dbSubscription = $this->subscriptions_model->get_by_hash($subscription->metadata[$this->subscriptionMetaKey]);

            if ($dbSubscription) {
                $this->subscriptions_model->send_email_template(
                    $dbSubscription->id,
                    $this->getStaffCCForMailTemplate($dbSubscription->created_from),
                    'subscription_cancelled_to_customer'
                );

                $this->subscriptions_model->update(
                    $dbSubscription->id,
                    ['status' => $subscription->status, 'next_billing_cycle' => null]
                );
            }
        }
    }

    /**
      * Handle customer deleted
      *
      * @param  \stdClass $event
      *
      * @return void
      */
    protected function customerDeletedEvent($event)
    {
        $stripeClient = $event->data->object;
        $this->db->where('stripe_id', $stripeClient->id);
        $client = $this->db->get('clients')->row();

        if ($client) {
            $this->db->where('userid', $client->userid)->update('clients', ['stripe_id' => null]);
        }
    }

    /**
     * Get CC for the subscription mail template
     *
     * @param  int $staff_id
     *
     * @return string
     */
    protected function getStaffCCForMailTemplate($staff_id)
    {
        $this->db->select('email')
                ->from(db_prefix() . 'staff')
                ->where('staffid', $staff_id);
        $staff = $this->db->get()->row();

        return $staff ? $staff->email : '';
    }
}