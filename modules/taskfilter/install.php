<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'taskfilter')) {
	$CI->db->query('CREATE TABLE `' . db_prefix() . "taskfilter` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `filter_name` VARCHAR(100) NOT NULL,
      `creator` INT(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'filter_detail')) {
	$CI->db->query('CREATE TABLE `' . db_prefix() . "filter_detail` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `taskfilter` INT(11) NOT NULL,
      `value` VARCHAR(100) NULL,
      `xday` INT(11) NULL,
      `from_date` DATE NULL,
  	  `to_date` DATE NULL,
  	  `type` VARCHAR(45) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'list_widget')) {
	$CI->db->query('CREATE TABLE `' . db_prefix() . "list_widget` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `add_from` INT(11) NOT NULL,
      `rel_id` INT(11) NULL,
      `rel_type` VARCHAR(45) NULL,
      `layout` VARCHAR(45) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
