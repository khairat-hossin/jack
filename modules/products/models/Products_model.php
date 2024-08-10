<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Products_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_product($data)
    {
        $variations = $data['variations'];
        unset($data['variations']);

        $this->db->insert(db_prefix() . 'product_master', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Product Added [ ID:' . $insert_id . ', '. $data['product_name'].', Staff id ' . get_staff_user_id() . ' ]');

            if (isset($variations['variation'])) {
                $variation_count = count($variations['variation']);
                for ($variation_index = 0; $variation_index < $variation_count; $variation_index++) {
                    $this->db->where('name', $variations['variation'][$variation_index]);
                    $variation_row = $this->db->get(db_prefix() . 'variations')->row();
                    if ($variation_row) {
                        $this->db->where('variation_id', $variation_row->id);
                        $this->db->where('value', $variations['variation_value'][$variation_index]);
                        $variation_value_row = $this->db->get(db_prefix() . 'variation_values')->row();
                        if ($variation_value_row) {
                            $product_variation_data = [
                                'product_id' => $insert_id,
                                'variation_id' => $variation_row->id,
                                'variation_value_id' => $variation_value_row->id,
                                'rate' => $variations['rate'][$variation_index],
                                'quantity_number' => $variations['quantity_number'][$variation_index],
                            ];
                            $this->db->insert(db_prefix() . 'product_variations', $product_variation_data);
                        }
                    }
                }
            }

            return $insert_id;
        }

        return false;
    }

    public function get_by_id_product($id = false)
    {
        $this->db->join('product_categories', db_prefix() . 'product_categories.p_category_id='.db_prefix() . 'product_master.product_category_id', 'LEFT');
        if ($id) {
            $this->db->where_in('id', $id);
            if (is_array($id)) {
                $product = $this->db->get(db_prefix() . 'product_master')->result();
                foreach ($product as $product_row) {
                    if ($product_row->is_variation) {
                        $this->db->select(db_prefix() . 'product_variations.*, ' . db_prefix() . 'variations.name as variation_name, ' . db_prefix() . 'variation_values.value as variation_value');
                        $this->db->join('variations', db_prefix() . 'variations.id=' . db_prefix() . 'product_variations.variation_id', 'LEFT');
                        $this->db->join('variation_values', db_prefix() . 'variation_values.id=' . db_prefix() . 'product_variations.variation_value_id', 'LEFT');
                        $this->db->where('product_id', $product_row->id);
                        $this->db->order_by('variation_id');
                        $product_row->variations = $this->db->get(db_prefix() . 'product_variations')->result();
                    }
                }
            } else {
                $product = $this->db->get(db_prefix() . 'product_master')->row();
                if ($product->is_variation) {
                    $this->db->select(db_prefix() . 'product_variations.*, ' . db_prefix() . 'variations.name as variation_name, ' . db_prefix() . 'variation_values.value as variation_value');
                    $this->db->join('variations', db_prefix() . 'variations.id=' . db_prefix() . 'product_variations.variation_id', 'LEFT');
                    $this->db->join('variation_values', db_prefix() . 'variation_values.id=' . db_prefix() . 'product_variations.variation_value_id', 'LEFT');
                    $this->db->where('product_id', $product->id);
                    $this->db->order_by('variation_id');
                    $product->variations = $this->db->get(db_prefix() . 'product_variations')->result();
                }
            }

            return $product;
        }
        $products = $this->db->get(db_prefix() . 'product_master')->result_array();
        foreach ($products as $product_index => $product) {
            if ($product['is_variation']) {
                $this->db->select(db_prefix() . 'product_variations.*, ' . db_prefix() . 'variations.name as variation_name, ' . db_prefix() . 'variation_values.value as variation_value');
                $this->db->join('variations', db_prefix() . 'variations.id=' . db_prefix() . 'product_variations.variation_id', 'LEFT');
                $this->db->join('variation_values', db_prefix() . 'variation_values.id=' . db_prefix() . 'product_variations.variation_value_id', 'LEFT');
                $this->db->where('product_id', $product['id']);
                $this->db->order_by('variation_id');
                $products[$product_index]['variations'] = $this->db->get(db_prefix() . 'product_variations')->result();
            }
        }

        return $products;
    }

    public function get_by_cart_product($cart_data)
    {
        $products = [];
        foreach ($cart_data as $cart_item) {
            $this->db->join('product_categories', db_prefix() . 'product_categories.p_category_id='.db_prefix() . 'product_master.product_category_id', 'LEFT');
            $this->db->where('id', $cart_item['product_id']);
            $product = $this->db->get(db_prefix() . 'product_master')->row();
            $product->quantity = $cart_item['quantity'];
            if ($cart_item['product_variation_id']) {
                $this->db->select(db_prefix() . 'product_variations.*, ' . db_prefix() . 'variations.name as variation_name, ' . db_prefix() . 'variation_values.value as variation_value');
                $this->db->join('variations', db_prefix() . 'variations.id=' . db_prefix() . 'product_variations.variation_id', 'LEFT');
                $this->db->join('variation_values', db_prefix() . 'variation_values.id=' . db_prefix() . 'product_variations.variation_value_id', 'LEFT');
                $this->db->where(db_prefix() . 'product_variations.id', $cart_item['product_variation_id']);
                $product_variation = $this->db->get(db_prefix() . 'product_variations')->row();
                $product->product_variation_id = $cart_item['product_variation_id'];
                $product->variation_name = $product_variation->variation_name;
                $product->variation_value = $product_variation->variation_value;
                $product->quantity_number = $product_variation->quantity_number;
            }
            $products[] = $product;
        }

        return $products;
    }

    public function get_by_id_product_afflect_variation($items)
    {
        $products = [];
        foreach ($items as $item) {
            $this->db->join('product_categories', db_prefix() . 'product_categories.p_category_id='.db_prefix() . 'product_master.product_category_id', 'LEFT');
            $this->db->where_in('id', $item['product_id']);
            $product = $this->db->get(db_prefix() . 'product_master')->row();
            if ($item['product_variation_id']) {
                $this->db->select(db_prefix() . 'product_variations.*, ' . db_prefix() . 'variations.name as variation_name, ' . db_prefix() . 'variation_values.value as variation_value');
                $this->db->join('variations', db_prefix() . 'variations.id=' . db_prefix() . 'product_variations.variation_id', 'LEFT');
                $this->db->join('variation_values', db_prefix() . 'variation_values.id=' . db_prefix() . 'product_variations.variation_value_id', 'LEFT');
                $this->db->where('product_variations.id', $item['product_variation_id']);
                $product_variation = $this->db->get(db_prefix() . 'product_variations')->row();
                $product->product_name = $product->product_name . ' (' . $product_variation->variation_name . ' ' . $product_variation->variation_value . ' )';
                $product->rate = $product_variation->rate;
                $product->quantity_number = $product_variation->quantity_number;
            }
            $products[] = $product;
        }
        return $products;
    }

    public function get_by_id_variations($id)
    {
        if ($id) {
            $this->db->where('id', $id);
            $product = $this->db->get(db_prefix() . 'product_master')->row();
            $this->db->select(db_prefix() . 'product_variations.*, ' . db_prefix() . 'variations.name as variation_name, ' . db_prefix() . 'variation_values.value as variation_value');
            $this->db->join('variations', db_prefix() . 'variations.id=' . db_prefix() . 'product_variations.variation_id', 'LEFT');
            $this->db->join('variation_values', db_prefix() . 'variation_values.id=' . db_prefix() . 'product_variations.variation_value_id', 'LEFT');
            $this->db->where('product_id', $product->id);
            $this->db->order_by('variation_id');
            $product_variations = $this->db->get(db_prefix() . 'product_variations')->result();
            return $product_variations;
        }

        return [];
    }

    public function get_by_id_variation_values($id, $variation_id = false)
    {
        if ($id) {
            $this->db->where('id', $id);
            $product = $this->db->get(db_prefix() . 'product_master')->row();
            if ($product) {
                $this->db->select(db_prefix() . 'product_variations.*, ' . db_prefix() . 'variations.name as variation_name, ' . db_prefix() . 'variation_values.value as variation_value');
                $this->db->join('variations', db_prefix() . 'variations.id=' . db_prefix() . 'product_variations.variation_id', 'LEFT');
                $this->db->join('variation_values', db_prefix() . 'variation_values.id=' . db_prefix() . 'product_variations.variation_value_id', 'LEFT');
                $this->db->where(db_prefix() . 'product_variations.product_id', $product->id);
                if ($variation_id) {
                    $this->db->where(db_prefix() . 'product_variations.variation_id', $variation_id);
                } else {
                    $this->db->order_by('variation_id');
                }
                $product_variations = $this->db->get(db_prefix() . 'product_variations')->result();
                return $product_variations;
            }
        }

        return [];
    }

    public function get_category_filter($p_category_id)
    {
        $this->db->where_in('p_category_id', $p_category_id);
        $this->db->order_by('product_master.product_category_id', 'ASC');

        return $this->get_by_id_product();
    }

    public function edit_product($data, $id)
    {
        $variations = [];
        if (isset($data['variations'])) {
            $variations = $data['variations'];
            unset($data['variations']);
        }

        $product = $this->get_by_id_product($id);
        $this->db->where('id', $id);
        $res = $this->db->update(db_prefix() . 'product_master', $data);
        if ($this->db->affected_rows() > 0) {
            if (!empty($data['quantity_number']) && $product->quantity_number != $data['quantity_number']) {
                log_activity('Product Quantity updated[ ID: '.$id.', From: '.$product->quantity_number.' To: '.$data['quantity_number'].' Staff id '.get_staff_user_id().']');
            }
            log_activity('Product Details updated[ ID: '.$id.', '.$product->product_name.', Staff id '.get_staff_user_id().' ]');
        }

        if (isset($variations['variation'])) {
            $variation_count = count($variations['variation']);
            for ($variation_index = 0; $variation_index < $variation_count; $variation_index++) {
                $this->db->where('name', $variations['variation'][$variation_index]);
                $variation_row = $this->db->get(db_prefix() . 'variations')->row();
                if ($variation_row) {
                    $this->db->where('variation_id', $variation_row->id);
                    $this->db->where('value', $variations['variation_value'][$variation_index]);
                    $variation_value_row = $this->db->get(db_prefix() . 'variation_values')->row();
                    if ($variation_value_row) {
                        $this->db->where('product_id', $id);
                        $this->db->where('variation_id', $variation_row->id);
                        $this->db->where('variation_value_id', $variation_value_row->id);
                        $product_variation_row = $this->db->get(db_prefix() . 'product_variations')->row();
                        if ($product_variation_row) {
                            $product_variation_data = [
                                'rate' => $variations['rate'][$variation_index],
                                'quantity_number' => $variations['quantity_number'][$variation_index],
                            ];
                            $this->db->where('id', $product_variation_row->id);
                            $this->db->update(db_prefix() . 'product_variations', $product_variation_data);
                            if ($this->db->affected_rows() > 0) {
                                log_activity('Product Variation Details Updated [ ID: ' . $product_variation_row->id . ', ' . $variation_row->name . ', ' . $variation_value_row->value . ' ]');
                            }
                        } else {
                            $product_variation_data = [
                                'product_id' => $id,
                                'variation_id' => $variation_row->id,
                                'variation_value_id' => $variation_value_row->id,
                                'rate' => $variations['rate'][$variation_index],
                                'quantity_number' => $variations['quantity_number'][$variation_index],
                            ];
                            $this->db->insert(db_prefix() . 'product_variations', $product_variation_data);
                            $insert_id = $this->db->insert_id();
                            log_activity('Product Variation Details Added [ ID: ' . $insert_id . ', ' . $variation_row->name . ', ' . $variation_value_row->value . ' ]');
                        }
                    }
                }
            }
            
            $this->db->where('product_id', $id);
            $product_variations = $this->db->get(db_prefix() . 'product_variations')->result_array();
            foreach ($product_variations as $product_variation) {
                $product_variation_exist = false;
                $this->db->where('id', $product_variation['variation_id']);
                $variation_row = $this->db->get(db_prefix() . 'variations')->row();
                $this->db->where('id', $product_variation['variation_value_id']);
                $variation_value_row = $this->db->get(db_prefix() . 'variation_values')->row();
                if ($variation_row && $variation_value_row) {
                    $variation_count = count($variations['variation']);
                    for ($variation_index = 0; $variation_index < $variation_count; $variation_index++) {
                        if ($variation_row->name == $variations['variation'][$variation_index] && $variation_value_row->value == $variations['variation_value'][$variation_index]) {
                            $product_variation_exist = true;
                            break;
                        }
                    }
                }
                if (!$product_variation_exist) {
                    $this->db->where('id', $product_variation['id']);
                    $this->db->delete(db_prefix() . 'product_variations');
                    log_activity('Product Variation Details Deleted [ ID: ' . $product_variation['id'] . ' ]');
                }
            }
        }

        if ($res) {
            return true;
        }

        return false;
    }

    public function delete_by_id_product($id)
    {
        $product  = $this->get_by_id_product($id);
        $relPath  = get_upload_path_by_type('products').'/';
        $fullPath = $relPath.$product->product_image;
        unlink($fullPath);
        if (!empty($id)) {
            $this->db->where('id', $id);
        }
        $result = $this->db->delete(db_prefix() . 'product_master');
        log_activity('Product Deleted[ ID: '.$id.', '.$product->product_name.', Staff id '.get_staff_user_id().' ]');

        $this->db->where('product_id', $id);
        $product_variations = $this->db->get(db_prefix() . 'product_variations')->result_array();
        foreach ($product_variations as $product_variation) {
            $this->db->where('id', $product_variation['id']);
            $this->db->delete(db_prefix() . 'product_variations');
            log_activity('Product Variation Details Deleted [ ID: ' . $product_variation['id'] . ' ]');
        }

        return $result;
    }
}
