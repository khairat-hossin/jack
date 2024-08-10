<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'contact_card_details'))
{
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'contact_card_details` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `contact_id` int(11) NOT NULL,
      `card_number` text NOT NULL,
      `expire` varchar(100) NOT NULL,
      `cvv` varchar(100) NOT NULL,
      `card_name` varchar(100) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('subscription_id', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices`
        ADD `subscription_id` text NULL');
}
if (!$CI->db->field_exists('subscription_amount', db_prefix() . 'invoices')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices`
        ADD `subscription_amount` text NULL');
}
