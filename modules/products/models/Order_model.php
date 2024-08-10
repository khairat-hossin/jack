<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Order_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_order($data)
    {
        $data['datecreated'] = date('Y-m-d H:i:s');
        $product_items       = $data['product_items'];
        unset($data['product_items']);
        unset($data['coupon_id']);
        $this->db->insert(db_prefix() . 'order_master', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $product_items = array_map(function ($arr) use ($insert_id) {
                if (empty($arr['product_variation_id'])) {
                    $arr['product_variation_id'] = NULL;
                }
                return $arr + ['order_id' => $insert_id];
            }, $product_items);
            $this->db->insert_batch(db_prefix() . 'order_items', $product_items);
            $this->db->select('staffid, email');
            $this->db->where('admin', 1);
            $this->db->where('active', 1);
            $system_admin = $this->db->get(db_prefix() . 'staff')->result_array();
            foreach ($system_admin as $staff) {
                add_notification([
                    'description' => _l('Order added'),
                    'touserid'    => $staff['staffid'],
                    'link'        => 'products/order_history/',
                ]);
            }

            return $insert_id;
        }

        return false;
    }

    public function update_status($invoice_id, $status)
    {
        $this->db->update(db_prefix() . 'order_master', ['status' => $status], ['invoice_id'=>$invoice_id]);
    }

    public function update_quantity_on_invoice($invoice_id)
    {
        $res         = null;
        $order_items = $this->get_order_items_from_invoice($invoice_id);
        if (empty($order_items)) {
            $this->load->model('invoices_model');
            $recurring_invoice = $this->invoices_model->get($invoice_id);
            if (!empty($recurring_invoice)) {
                $recurring_invoice_id = $recurring_invoice->is_recurring_from;
                $order_items          = $this->get_order_items_from_invoice($recurring_invoice_id);
            }
        }
        if (!empty($order_items)) {
            $order_id    = reset($order_items)['order_id'];
            $product_variations = $order_items;
            $order_items = array_map(function ($arr) {
                $quantity_arr['quantity_number'] = 'quantity_number - '.$arr['qty'];
                $quantity_arr['id']              = $arr['product_id'];

                return $quantity_arr;
            }, $order_items);
            $this->db->set_update_batch($order_items, 'id', false);
            $this->db->where('is_digital', 0);
            $res = $this->db->update_batch(db_prefix() . 'product_master', null, 'id');
            if ($res) {
                $product_variations = array_filter($product_variations, function ($arr) {
                    if ($arr['product_variation_id']) {
                        return $arr;
                    }
                });
                $product_variations = array_map(function ($arr) {
                    $quantity_arr['quantity_number'] = 'quantity_number - ' . $arr['qty'];
                    $quantity_arr['id']              = $arr['product_variation_id'];
    
                    return $quantity_arr;
                }, $product_variations);
                if (count($product_variations)) {
                    $this->db->set_update_batch($product_variations, 'id', false);
                    $variation_res = $this->db->update_batch(db_prefix() . 'product_variations', null, 'id');
                } else {
                    $variation_res = true;
                }
                if ($variation_res) {
                    $data = $this->order_model->get_by_id_order($order_id);
                    $this->db->select('staffid, email');
                    $this->db->where('admin', 1);
                    $this->db->where('active', 1);
                    $system_admin = $this->db->get(db_prefix() . 'staff')->result_array();
                    foreach ($system_admin as $staff) {
                        send_mail_template('order_paid_admin', 'products', $data, $staff);
                    }
                    send_mail_template('Order_paid_client', 'products', $data);
                }
            }
        }

        return $res;
    }

    public function get_order_items_from_invoice($invoice_id)
    {
        $this->db->where(db_prefix() . 'order_master.invoice_id', $invoice_id);
        $this->db->join('order_master', db_prefix() . 'order_master.id=' . db_prefix() . 'order_items.order_id', 'LEFT');
        $result      = $this->db->get(db_prefix() . 'order_items');

        return $order_items = $result->result_array();
    }

    public function get_by_id_order($id = false)
    {
        if ($id) {
            $this->db->where_in(db_prefix() . 'order_master.id', $id);
            if (is_array($id)) {
                $product = $this->db->get(db_prefix() . 'order_master')->result();
            } else {
                $product = $this->db->get(db_prefix() . 'order_master')->row();
            }

            return $product;
        }
        $products = $this->db->get(db_prefix() . 'order_master')->result_array();

        return $products;
    }

    public function get_order_with_items($id = false)
    {
        if (!empty($id)) {
            $this->db->where(db_prefix() . 'order_master.id', $id);
        }
        $this->db->join('order_master', db_prefix() . 'order_master.id=' . db_prefix() . 'order_items.order_id', 'LEFT');
        $this->db->join('product_master', db_prefix() . 'product_master.id=' . db_prefix() . 'order_items.product_id', 'LEFT');
        $result      = $this->db->get(db_prefix() . 'order_items');

        return $order_items = $result->result();
    }

    public function add_invoice_order($post)
    {
        if (empty($post)) {
            return ['status' => false, 'message' => "Post data cannot be empty`"];
        }

        $coupon_description = '';
        if (!$post['coupon_id']) {
            $post['coupon_id'] = NULL;
        }
        if ($post['coupon_id']) {
            $this->db->where('id', $post['coupon_id']);
            $coupon = $this->db->get(db_prefix() . 'coupons')->row();
            if ($coupon) {
                $coupon_description = '(Coupon' . ' ' . $coupon->code . ' applied)';
            }
        }

        $post['newitems'] = $post['product_items'];
        $product_items = [];
        foreach ($post['product_items'] as $product_item) {
            $product_items[] = [
                'product_id' => $product_item['product_id'],
                'product_variation_id' => isset($product_item['product_variation_id']) ? $product_item['product_variation_id'] : '',
            ];
        }
        $data['products'] = $product = $this->products_model->get_by_id_product_afflect_variation($product_items);
        $message          = '';
        foreach ($product as $key => $value) {
            unset($post['newitems'][$key]['product_id']);
            unset($post['newitems'][$key]['product_variation_id']);
            $post['newitems'][$key]['unit']             = '';
            $post['newitems'][$key]['order']            = $key + 1;
            $post['newitems'][$key]['description']      = $value->product_name;
            $post['newitems'][$key]['long_description'] = $value->product_description . $coupon_description;
            $post['newitems'][$key]['taxname']          = unserialize($value->taxes);
            $post['newitems'][$key]['rate']             = $value->rate;

            $post['product_items'][$key]['rate']        = $value->rate;

            $post['newitems'][$key]['recurring']        = $value->recurring;
            $post['newitems'][$key]['recurring_type']   = $value->recurring_type;
            $post['newitems'][$key]['custom_recurring'] = $value->custom_recurring;
            $post['newitems'][$key]['cycles']           = $value->cycles;
            if (!$value->is_digital) {
                if ((int) $value->quantity_number < 1) {
                    $message .= '- <u>'.$value->product_name.'</u> is out of stock <br>';
                    continue;
                }
                if ((int) $post['product_items'][$key]['qty'] > (int) $value->quantity_number) {
                    $message .= '- <u>'.$value->product_name.'</u> is only <u>'.$value->quantity_number.'</u> in stock <br>';
                }
            }
        }

        $order_data = $post;

        if (!empty($message)) {
            return ['status' => false, 'message' => $message];
        }
        $billing_shipping = $this->clients_model->get_customer_billing_and_shipping_details($post['clientid']);
        $post             = array_merge($post, reset($billing_shipping));
        unset($post['billing_country']);
        unset($post['shipping_country']);
        $post['show_shipping_on_invoice'] = 'on';
        $post['number']                   = get_option('next_invoice_number');
        $order_data['order_date']         = $post['date']           = _d(date('Y-m-d'));
        $post['duedate']                  = _d(date('Y-m-d', strtotime('+'.get_option('invoice_due_after').' DAY', strtotime(date('Y-m-d')))));
        $post['show_quantity_as']         = 1;
        $this->load->model('payment_modes_model');
        $payment_modes = $this->payment_modes_model->get();
        foreach ($payment_modes as $modes) {
            if ($modes['selected_by_default']) {
                $post['allowed_payment_modes'][] = $modes['id'];
            }
        }
        
        $post['allowed_payment_modes'] = isset($post['allowed_payment_modes']) ? ($post['allowed_payment_modes']) : ([]);
        // echo '<pre>'; print_r($post); exit;
        unset($order_data['newitems']);
        unset($post['product_items']);
        unset($post['csrf_token_name']);
        $post['currency'] = $this->currencies_model->get_base_currency()->id;

        $invoice_insert_items = [];
        $invoice_order_items  = [];
        $result               = [];
        $init_tax             = [];
        $total                = $subtotal                = 0;

        foreach ($post['newitems'] as $key => $items) {
            if (0 != $items['recurring']) {
                $invoice_insert_items[$key] = $items;
                $invoice_order_items[$key]  = $order_data['product_items'][$key];
                unset($post['newitems'][$key]);
                unset($order_data['product_items'][$key]);
                continue;
            }
            $subtotal += $items['rate'] * $items['qty'];
            $total = $subtotal;
            if (!empty($items['taxname'])) {
                foreach ($items['taxname'] as $tax) {
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
                    $total += ($items['rate'] * $items['qty']) / 100 * $tax_array[1];
                }
            }

            unset($post['newitems'][$key]['recurring']);
            unset($post['newitems'][$key]['recurring_type']);
            unset($post['newitems'][$key]['custom_recurring']);
            unset($post['newitems'][$key]['cycles']);
        }
        $order_data['subtotal'] = $post['subtotal'] = $subtotal;
        $order_data['total']    = $post['total']    = $total;

        $count = 0;
        if (!empty($post['newitems'])) {
            $newitem_key = count($post['newitems']);
            $post['newitems'][$newitem_key]['unit']             = '';
            $post['newitems'][$newitem_key]['order']            = $key + 2;
            $post['newitems'][$newitem_key]['description']      = _l('flat_shipping');
            $post['newitems'][$newitem_key]['long_description'] = '';
            $post['newitems'][$newitem_key]['taxname']          = (!empty((get_option('product_tax_for_shipping_cost')))) ? unserialize(get_option('product_tax_for_shipping_cost')) : '';
            $post['newitems'][$newitem_key]['rate']             = get_option('product_flat_rate_shipping');
            $post['newitems'][$newitem_key]['qty']              = 1;
            $post['newitems'][$newitem_key]['recurring']        = 0;
            $post['newitems'][$newitem_key]['recurring_type']   = '';
            $post['newitems'][$newitem_key]['custom_recurring'] = '';
            $post['newitems'][$newitem_key]['cycles']           = 0;

            $subtotal += get_option('product_flat_rate_shipping');
            $total += get_option('product_flat_rate_shipping');
            $coupon_discount = 0;
            if (!empty($coupon)) {
                if ($coupon->type == '%') {
                    $coupon_discount = $total * $coupon->amount / 100;
                } else {
                    $coupon_discount = $coupon->amount;
                }
                $total = $total - $coupon_discount;
            }

            if (!empty($post['newitems'][$newitem_key]['taxname'])) {
                foreach ($post['newitems'][$newitem_key]['taxname'] as $tax) {
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
                    $total += (get_option('product_flat_rate_shipping')) / 100 * $tax_array[1];
                }
            }

            $post['subtotal']           = $subtotal;
            $post['coupon_discount']    = $coupon_discount;
            $post['total']              = $total;

            $count            = 1;
            $id               = $this->invoices_model->add($post);
            if ($id) {
                $result[]                 = true;
                $res                      = $this->invoices_model->get($id);
                $order_data['status']     = $res->status;
                $order_data['invoice_id'] = $id;
                $this->order_model->add_order($order_data);
            }
            $whereStaff = 'active=1 AND is_not_staff=0';
            $this->load->model('staff_model');
            $staff = $this->staff_model->get('', $whereStaff);
            foreach ($staff as $member) {
                $notificaton = [
                    'description'     => _l('Order added'),
                    'touserid'        => $member['staffid'],
                    'fromcompany'     => true,
                    'link'            => '../invoice/' . $res->id . '/' . $res->hash,
                ];
                
                $notified = add_notification($notificaton);                      
            }
        }
        
        if (!empty($invoice_insert_items)) {
            foreach ($invoice_insert_items as $key => $new_invoice_item) {
                $total = $subtotal = 0;

                $post['newitems']            = [];
                $order_data['product_items'] = [];
                $post['recurring']           = $new_invoice_item['recurring'];
                $post['recurring_type']      = $new_invoice_item['recurring_type'];
                $post['custom_recurring']    = $new_invoice_item['custom_recurring'];
                $post['cycles']              = $new_invoice_item['cycles'];

                unset($new_invoice_item['recurring']);
                unset($new_invoice_item['recurring_type']);
                unset($new_invoice_item['custom_recurring']);
                unset($new_invoice_item['cycles']);

                $post['number'] = get_option('next_invoice_number');

                $post['newitems'][$key]            = $new_invoice_item;
                $order_data['product_items'][$key] = $invoice_order_items[$key];

                $subtotal += $new_invoice_item['rate'] * $new_invoice_item['qty'];
                $total = $subtotal;
                $coupon_discount = 0;
                if (!empty($coupon)) {
                    if ($coupon->type == '%') {
                        $coupon_discount = $total * $coupon->amount / 100;
                    } else {
                        $coupon_discount = $coupon->amount;
                    }
                    $total = $total - $coupon_discount;
                }
                if (!empty($new_invoice_item['taxname'])) {
                    foreach ($new_invoice_item['taxname'] as $tax) {
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
                        $total += ($new_invoice_item['rate'] * $new_invoice_item['qty']) / 100 * $tax_array[1];
                    }
                }

                $order_data['subtotal'] = $post['subtotal'] = $subtotal;
                $post['coupon_discount'] = $coupon_discount;
                $order_data['total']    = $post['total']    = $total;

                $id               = $this->invoices_model->add($post);
                if ($id) {
                    $result[]                 = true;
                    $res                      = $this->invoices_model->get($id);
                    $order_data['status']     = $res->status;
                    $order_data['invoice_id'] = $id;
                    $this->order_model->add_order($order_data);
                }
                $whereStaff = 'active=1 AND is_not_staff=0';

                $this->load->model('staff_model');
                $staff = $this->staff_model->get('', $whereStaff);
                foreach ($staff as $member) {
                    $notificaton = [
                        'description'     => _l('Order added'),
                        'touserid'        => $member['staffid'],
                        'fromcompany'     => true,
                        'link'            => '../invoice/' . $res->id . '/' . $res->hash,
                    ];
                    
                    $notified = add_notification($notificaton);                      
                }
                
            }
        }
        if (count($invoice_insert_items) + $count == count($result)) {
            if (1 == count($result)) {
                return ['status' => true, 'single_invoice' => true,'invoice_id' => $id, 'invoice_hash' => $res->hash];
            }
            return ['status' => true, 'single_invoice' => false];
        }
        return ['status' => false, 'message' => _l('order_fail')];
    }
}