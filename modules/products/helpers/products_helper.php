<?php

function handle_product_upload($product_id)
{
    $CI = &get_instance();
    if (isset($_FILES['product']['name']) && '' != $_FILES['product']['name']) {
        $path        = get_upload_path_by_type('products');
        $tmpFilePath = $_FILES['product']['tmp_name'];
        if (!empty($tmpFilePath) && '' != $tmpFilePath) {
            $path_parts  = pathinfo($_FILES['product']['name']);
            $extension   = $path_parts['extension'];
            $extension   = strtolower($extension);
            $filename    = 'product_'.$product_id.'.'.$extension;
            $newFilePath = $path.$filename;
            _maybe_create_upload_path($path);
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI->products_model->edit_product(['product_image' => $filename], $product_id);

                return true;
            }
        }
    }

    return false;
}

function get_coupon_used_times($coupon_id)
{
    $CI = &get_instance();

    $coupon_used_times = $CI->coupons_model->get_used_times($coupon_id);
    return $coupon_used_times;
}

function get_product_variations($product_id)
{
    $CI = &get_instance();

    $product_variations = $CI->products_model->get_by_id_variations($product_id);

    $variations = '';
    foreach ($product_variations as $product_variation)
    {
        $variations .= '<span class="label label-danger">' . $product_variation->variation_name . '</span> ';
    }

    return $variations;
}

function get_product_variation_values($product_id)
{
    $CI = &get_instance();

    $product_variation_values = $CI->products_model->get_by_id_variation_values($product_id);

    $variation = '';
    $variation_values = '';
    foreach ($product_variation_values as $product_variation_value)
    {
        if ($variation != $product_variation_value->variation_name) {
            $variation = $product_variation_value->variation_name;
            if ($variation_values) {
                $variation_values .= '</div>';
            }
            $variation_values .= '<div>' . $product_variation_value->variation_name . ' - ';
        }
        $variation_values .= '<span class="label label-danger">' . $product_variation_value->variation_value . '</span> ';
    }
    $variation_values .= '</div>';

    return $variation_values;
}

function get_variation_values($variation_id)
{
    $CI = &get_instance();

    $variation_values = $CI->variations_model->get_values($variation_id);

    $values = '';
    foreach ($variation_values as $variation_value)
    {
        $values .= '<span class="label label-danger">' . $variation_value['value'] . '</span> ';
    }

    return $values;
}

function toPlainArray($arr)
{
    $output = "['";
    foreach ($arr as $val) {
        $output .= $val."', '";
    }
    $plain_array = substr($output, 0, -3).']';

    return $plain_array;
}
