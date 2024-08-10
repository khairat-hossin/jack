<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stripe_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_card_details($contact_id)
    {
      $sql = 'select * from '.db_prefix().'contact_card_details where contact_id = "'.$contact_id.'"';
      return $this->db->query($sql)->row();
    }
    public function get_subscriptions($client_id)
    {
      $sql = 'select * from '.db_prefix().'subscriptions where clientid = "'.$client_id.'" and status="active" or status="future"';
      return $this->db->query($sql)->result_array();
    }
    public function get_stripe_connect($clientid)
    {
      $sql = 'select * from '.db_prefix().'clients where userid = "'.$clientid.'"';
      return $this->db->query($sql)->row();
    }
}
