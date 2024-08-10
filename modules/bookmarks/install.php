<?php
defined('BASEPATH') or exit('No direct script access allowed');
if (!$CI->db->table_exists(db_prefix() . 'bookmarks'))
{
    $CI->db->query('CREATE TABLE `' . db_prefix() . "bookmarks` (          
			`id` int(11) NOT NULL,          
			`url` varchar(1000) NOT NULL,          
			`title` varchar(500) NOT NULL,          
			`description` text NOT NULL,          
			`created_date` datetime NOT NULL,          
			`staff_id` int(11) NOT NULL DEFAULT '0',          
			`location_id` int(11) NOT NULL,          
			`only_me` tinyint(4) NOT NULL DEFAULT '0'        
		) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'bookmarks`        
		ADD PRIMARY KEY (`id`),        
		ADD KEY `location_id` (`location_id`),        
		ADD KEY `staff_id` (`staff_id`);');
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'bookmarks` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}
if (!$CI->db->table_exists(db_prefix() . 'bookmark_locations'))
{
    $CI->db->query('CREATE TABLE `' . db_prefix() . "bookmark_locations` (              
			`id` int(11) NOT NULL,              
			`location_name` varchar(500) NOT NULL,              
			`description` varchar(1000) NOT NULL,              
			`ordering` int(11) NOT NULL,              
			`staff_id` int(11) NOT NULL,              
			`created_date` datetime NOT NULL,              
			`only_me` tinyint(4) NOT NULL DEFAULT '0'        
		) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'bookmark_locations`        
		ADD PRIMARY KEY (`id`),        
		ADD KEY `staff_id` (`staff_id`);');
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'bookmark_locations`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}