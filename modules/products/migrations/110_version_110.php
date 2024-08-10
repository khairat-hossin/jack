<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_110 extends App_module_migration
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * { For digital products : is_digital column added in product_master table }
 	 * @return bool
     */
    public function up()
    {
        add_option('product_flat_rate_shipping', 0);
        add_option('product_tax_for_shipping_cost', 0);
    	$CI =& get_instance();
    	$table_name = db_prefix().'product_master';

    	$get_table = $CI->db->get($table_name);

    	if ($get_table) {
            if (!$CI->db->field_exists('is_digital', $table_name)) {
                $alter_qry = $CI->db->query("ALTER TABLE {$table_name} 
                    ADD `is_digital` TINYINT('1') NOT NULL DEFAULT '0' 
                    AFTER `quantity_number`;");
                 $query = $CI->db->query($alter_qry);
                if ($results) {
                    return true;
                }else{
                    return false;
                }
            }
    	}
    }
}

