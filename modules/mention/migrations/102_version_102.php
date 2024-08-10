<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();          
        if (!$CI->db->field_exists('is_contact', 'mention_posts')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'mention_posts` 
              ADD COLUMN `is_contact` VARCHAR(45) NOT NULL DEFAULT 0
              ;');            
        } 

     }
}
