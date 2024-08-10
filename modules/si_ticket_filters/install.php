<?php
defined('BASEPATH') or exit('No direct script access allowed');
if(!$CI->db->table_exists(db_prefix() . 'si_ticket_filter')) {
	$CI->db->query('CREATE TABLE `' . db_prefix() . "si_ticket_filter` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`filter_name` varchar(200) NOT NULL,
	`filter_parameters` text NOT NULL,
	`staff_id` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `staff_id` (`staff_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
