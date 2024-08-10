<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_123 extends App_module_migration
{
    public function up()
    {
        $this->ci->db->query('SET foreign_key_checks = 0');

        add_option('coupons_disabled', 1);
        $this->ci->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . 'coupons` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `code` VARCHAR(30) NOT NULL,
            `type` VARCHAR(10) NOT NULL,
            `amount` DECIMAL(10,2) NOT NULL,
            `max_uses` INT NOT NULL DEFAULT "0",
            `max_uses_per_client` INT NOT NULL DEFAULT "0",
            `start_date` DATE NULL DEFAULT NULL,
            `end_date` DATE NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE = InnoDB DEFAULT CHARSET=' . $this->ci->db->char_set . ';');

        $this->ci->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . 'variations` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(50) NOT NULL,
            `description` TEXT NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE = InnoDB DEFAULT CHARSET=' . $this->ci->db->char_set . ';');

        $this->ci->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . 'variation_values` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `variation_id` INT NOT NULL,
            `value` VARCHAR(50) NOT NULL,
            `value_order` INT NOT NULL,
            `description` VARCHAR(50) NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            FOREIGN KEY (variation_id) REFERENCES `' . db_prefix() . 'variations`(`id`) ON DELETE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARSET=' . $this->ci->db->char_set . ';');

        $this->ci->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . 'product_variations` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `product_id` INT NOT NULL,
            `variation_id` INT NOT NULL,
            `variation_value_id` INT NOT NULL,
            `rate` DECIMAL(15,2) NOT NULL,
            `quantity_number` INT NOT NULL,
            PRIMARY KEY (`id`),
            FOREIGN KEY (product_id) REFERENCES `' . db_prefix() . 'product_master`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (variation_id) REFERENCES `' . db_prefix() . 'variations`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (variation_value_id) REFERENCES `' . db_prefix() . 'variation_values`(`id`) ON DELETE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARSET=' . $this->ci->db->char_set . ';');
        
        if (!$this->ci->db->field_exists('product_variation_id', db_prefix() . 'order_items')) {
            $this->ci->db->query('ALTER TABLE `' . db_prefix() . 'order_items`
                ADD `product_variation_id` INT AFTER `product_id`');

            $this->ci->db->query('ALTER TABLE `' . db_prefix() . 'order_items` 
                ADD FOREIGN KEY (`product_variation_id`) 
                REFERENCES `' . db_prefix() . 'product_variations`(`id`) 
                ON DELETE CASCADE 
                ON UPDATE CASCADE'
            );
        } else {
            $this->ci->db->query('ALTER TABLE `' . db_prefix() . 'order_items`
                MODIFY COLUMN `product_variation_id` INT');
        }

        if (!$this->ci->db->field_exists('is_variation', db_prefix() . 'product_master')) {
            $this->ci->db->query('ALTER TABLE `' . db_prefix() . 'product_master`
                ADD `is_variation` TINYINT(1) NOT NULL DEFAULT "0"');
        } else {
            $this->ci->db->query('ALTER TABLE `' . db_prefix() . 'product_master`
                MODIFY COLUMN `is_variation` TINYINT(1) NOT NULL DEFAULT "0"');
        }

        if (!$this->ci->db->field_exists('coupon_id', db_prefix() . 'invoices')) {
            $this->ci->db->query('ALTER TABLE `' . db_prefix() . 'invoices`
                ADD `coupon_id` INT AFTER `currency`');
            $this->ci->db->query('ALTER TABLE `' . db_prefix() . 'invoices`
                ADD FOREIGN KEY (`coupon_id`) 
                REFERENCES `' . db_prefix() . 'coupons`(`id`) 
                ON DELETE CASCADE 
                ON UPDATE CASCADE
            ');
        } else {
            $this->ci->db->query('ALTER TABLE `' . db_prefix() . 'invoices`
                MODIFY COLUMN `coupon_id` INT');
        }
        if (!$this->ci->db->field_exists('coupon_discount', db_prefix() . 'invoices')) {
            $this->ci->db->query('ALTER TABLE `' . db_prefix() . 'invoices`
                ADD `coupon_discount` DECIMAL(15,2) DEFAULT "0" AFTER `total_tax`');
        } else {
            $this->ci->db->query('ALTER TABLE `' . db_prefix() . 'invoices`
                MODIFY COLUMN `coupon_discount` DECIMAL(15,2) DEFAULT "0"');
        }
        
        $this->ci->db->query('SET foreign_key_checks = 1');
    }
}