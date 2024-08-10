<?php
defined('BASEPATH') or exit('No direct script access allowed');
if (!$CI->db->table_exists(db_prefix() . 'mention_posts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'mention_posts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `creator` INT(11) NOT NULL,
  `datecreated` DATETIME NOT NULL,
  `content` TEXT NOT NULL,
  `pinned` INT(11) NOT NULL,
  `datepinned` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'mention_post_comments')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'mention_post_comments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `content` TEXT NULL DEFAULT NULL,
  `userid` INT(11) NOT NULL,
  `postid` INT(11) NOT NULL,
  `dateadded` DATETIME NOT NULL,
  PRIMARY KEY (`id`));');
}

// Version 1.0.1
$CI->db->query('ALTER TABLE `' . db_prefix() . "mention_posts`
      MODIFY `content` TEXT CHARACTER SET utf8 NOT NULL");
$CI->db->query('ALTER TABLE `' . db_prefix() . "mention_post_comments`
      MODIFY `content` TEXT CHARACTER SET utf8 NULL DEFAULT NULL");
if (!$CI->db->field_exists('is_contact', 'mention_post_comments')) {
      $CI->db->query('ALTER TABLE `'.db_prefix() . 'mention_post_comments` 
      ADD COLUMN `is_contact` VARCHAR(45) NOT NULL DEFAULT 0
      ;');            
} 