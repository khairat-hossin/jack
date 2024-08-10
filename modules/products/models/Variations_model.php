<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Variations_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_values($id)
    {
        if ($id) {
            $this->db->where('variation_id', $id);
            $this->db->order_by('value_order');
            $variation_values = $this->db->get(db_prefix() . 'variation_values')->result_array();
            return $variation_values;
        }
        return [];
    }

    public function add_variation($data)
    {
        $this->db->insert(db_prefix() . 'variations', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Variation Added [ ID:' . $insert_id . ', ' . $data['name'] . ' ]');

            return $insert_id;
        }

        return false;
    }

    public function add_variation_values($id, $values)
    {
        foreach ($values as $value) {
            $data = [
                'variation_id' => $id,
                'value' => $value['value'],
                'value_order' => $value['order'],
                'description' => $value['description'],
            ];
            $this->db->insert(db_prefix() . 'variation_values', $data);
        }
    }

    public function edit_variation_values($id, $values)
    {
        foreach ($values as $value) {
            $data = [
                'variation_id' => $id,
                'value' => $value['value'],
                'description' => $value['description'],
                'value_order' => $value['order'],
            ];
            $this->db->where('value', $value['value']);
            $variation_value = $this->db->get(db_prefix() . 'variation_values')->row();
            if ($variation_value) {
                $this->db->where('id', $variation_value->id);
                $this->db->update(db_prefix() . 'variation_values', $data);
            } else {
                $this->db->insert(db_prefix() . 'variation_values', $data);
            }
        }

        $this->db->where('variation_id', $id);
        $variation_values = $this->db->get(db_prefix() . 'variation_values')->result_array();
        foreach ($variation_values as $variation_value) {
            $variation_value_exist = false;
            foreach ($values as $value) {
                if ($value['value'] == $variation_value['value']) {
                    $variation_value_exist = true;
                }
            }
            if (!$variation_value_exist) {
                $this->db->where('id', $variation_value['id']);
                $this->db->delete(db_prefix() . 'variation_values');
            }
        }
    }

    public function get($id = false, $values = false)
    {
        if ($id) {
            $this->db->where('id', $id);
            $variation = $this->db->get(db_prefix() . 'variations')->row();

            if ($values) {
                $this->db->where('variation_id', $id);
                $this->db->order_by('value_order');
                $variation->values = $this->db->get(db_prefix() . 'variation_values')->result_array();
            }
            return $variation;
        }
        $variations = $this->db->get(db_prefix() . 'variations')->result_array();
        if ($values) {
            foreach ($variations as $variation) {
                $this->db->where('variation_id', $variation->id);
                $this->db->order_by('value_order');
                $variation->values = $this->db->get(db_prefix() . 'variation_values')->result_array();
            }
        }

        return $variations;
    }

    public function edit($data, $id)
    {
        $variation = $this->get($id);
        $this->db->where('id', $id);
        $res = $this->db->update(db_prefix() . 'variations', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Variation Details updated[ ID: ' . $id . ', ' . $variation->name . ' ]');
        }
        if ($res) {
            return true;
        }

        return false;
    }

    public function delete($id)
    {
        $variation  = $this->get($id);
        if (!empty($id)) {
            $this->db->where('id', $id);
        }
        $result = $this->db->delete(db_prefix() . 'variations');
        log_activity('Variation Deleted[ ID: ' . $id . ', '. $variation->name . ' ]');

        if (!empty($id)) {
            $this->db->where('variation_id', $id);
        }
        $result = $this->db->delete(db_prefix() . 'variation_values');

        return $result;
    }
}
