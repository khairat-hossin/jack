<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_101 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();          
        $CI->db->query('ALTER TABLE `' . db_prefix() . "mention_posts`
              MODIFY `content` TEXT CHARACTER SET utf8 NOT NULL");
        $CI->db->query('ALTER TABLE `' . db_prefix() . "mention_post_comments`
              MODIFY `content` TEXT CHARACTER SET utf8 NULL DEFAULT NULL");
        if (!$CI->db->field_exists('is_contact', 'mention_post_comments')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'mention_post_comments` 
              ADD COLUMN `is_contact` VARCHAR(45) NOT NULL DEFAULT 0
              ;');            
        } 

     }
}
