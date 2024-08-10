<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Coupons_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = false)
    {
        if ($id) {
            $this->db->where('id', $id);
            $coupon = $this->db->get(db_prefix() . 'coupons')->row();
            return $coupon;
        }
        $coupons = $this->db->get(db_prefix() . 'coupons')->result_array();

        return $coupons;
    }

    public function get_by_code($code = false)
    {
        if ($code) {
            $this->db->where('code', $code);
            $coupon = $this->db->get(db_prefix() . 'coupons')->row();
            return $coupon;
        }
        $coupons = $this->db->get(db_prefix() . 'coupons')->result_array();

        return $coupons;
    }

    public function is_available($id, $client_id = false)
    {
        if ($id) {
            $now = date('Y-m-d H:i:s');
            $this->db->where('max_uses > ', 0);
            $this->db->where('max_uses_per_client >', 0);
            $this->db->where('(`start_date` is NULL OR `start_date` <= \'' . $now . '\') AND ' . '(`end_date` is NULL OR `end_date` >= \'' . $now . '\')');
            $this->db->where('id', $id);
            $coupon = $this->db->get(db_prefix() . 'coupons')->row();

            if ($coupon) {
                $this->db->where('coupon_id', $coupon->id);
                $this->db->where('clientid', $client_id);
                $client_invoices = $this->db->get(db_prefix() . 'invoices')->result_array();
    
                $this->db->where('coupon_id', $coupon->id);
                $invoices = $this->db->get(db_prefix() . 'invoices')->result_array();
    
                if (count($client_invoices) < $coupon->max_uses_per_client && count($invoices) < $coupon->max_uses) {
                    return true;
                }
            }
        }

        return false;
    }

    public function get_availables($client_id = false)
    {
        $now = date('Y-m-d H:i:s');
        $this->db->where('max_uses > ', 0);
        $this->db->where('max_uses_per_client >', 0);
        $this->db->where('(`start_date` is NULL OR `start_date` <= \'' . $now . '\') AND ' . '(`end_date` is NULL OR `end_date` >= \'' . $now . '\')');
        $coupons = $this->db->get(db_prefix() . 'coupons')->result_array();
        if ($client_id) {
            $available_coupons = [];
            foreach ($coupons as $coupon) {
                $this->db->where('coupon_id', $coupon['id']);
                $this->db->where('clientid', $client_id);
                $client_invoices = $this->db->get(db_prefix() . 'invoices')->result_array();

                $this->db->where('coupon_id', $coupon['id']);
                $invoices = $this->db->get(db_prefix() . 'invoices')->result_array();

                if (count($client_invoices) < $coupon['max_uses_per_client'] && count($invoices) < $coupon['max_uses']) {
                    $available_coupons[] = $coupon;
                }
            }

            return $available_coupons;
        }

        return $coupons;
    }

    public function get_used_times($id = false)
    {
        if ($id) {
            $this->db->where('coupon_id', $id);
        }
        $invoices = $this->db->get(db_prefix() . 'invoices')->result_array();

        return count($invoices);
    }
    
    public function add($data)
    {
        $data['start_date'] = to_sql_date($data['start_date'], true);
        $data['end_date'] = to_sql_date($data['end_date'], true);

        $this->db->insert(db_prefix() . 'coupons', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Coupon Added [ ID:' . $insert_id . ', ' . $data['code'] . ' ]');

            return $insert_id;
        }

        return false;
    }
    
    public function edit($data, $id)
    {
        $data['start_date'] = to_sql_date($data['start_date'], true);
        $data['end_date'] = to_sql_date($data['end_date'], true);

        $coupon = $this->get($id);
        $this->db->where('id', $id);
        $res = $this->db->update(db_prefix() . 'coupons', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Coupon Details updated[ ID: ' . $id . ', ' . $coupon->code . ' ]');
        }
        if ($res) {
            return true;
        }

        return false;
    }

    public function delete($id)
    {
        $coupon  = $this->get($id);
        if (!empty($id)) {
            $this->db->where('id', $id);
        }
        $result = $this->db->delete(db_prefix() . 'coupons');
        log_activity('Coupon Deleted[ ID: ' . $id . ', '. $coupon->code . ' ]');

        return $result;
    }
}
