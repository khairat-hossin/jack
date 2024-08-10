<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_ticket_filter_model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	* @param  integer (optional)
	* @return object
	* Get single ticket filter
	*/
	public function get($id = '')
	{
		$this->db->where('staff_id',get_staff_user_id());
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'si_ticket_filter')->row();
		}
		return $this->db->get(db_prefix() . 'si_ticket_filter')->result_array();
	}
	/**
	* get all filter templates of that staff
	*/
	function get_templates($staff_id)
	{
		if (is_numeric($staff_id)) {
			$this->db->where('staff_id', $staff_id);
			return $this->db->get(db_prefix() . 'si_ticket_filter')->result_array();
		}
		return array();
	}
	/**
	* Add new ticket filter
	* @param mixed $data All $_POST data
	* @return mixed
	*/
	public function add($data)
	{
		$this->db->insert(db_prefix() . 'si_ticket_filter', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			log_activity('New Advanced Ticket Filter Added [Name:' . $data['filter_name'] . ']');
			return $insert_id;
		}
		return false;
	}
	/**
	* Update ticket filter
	* @param mixed $data All $_POST data
	* @return mixed
	*/
	public function update($data,$filter_id)
	{
		$this->db->where('id',$filter_id);
		$update = $this->db->update(db_prefix() . 'si_ticket_filter', $data);
		if ($update) {
			log_activity('Advanced Ticket Filter Updated [Name:' . $data['filter_name'] . ']');
			return true;
		}
		return false;
	}
	/**
	* Delete ticket filter
	* @param  mixed $id filter id
	* @return boolean
	*/
	public function delete($id,$staff_id)
	{
		$this->db->where('id', $id);
		$this->db->where('staff_id', $staff_id);
		$this->db->delete(db_prefix() . 'si_ticket_filter');
		if ($this->db->affected_rows() > 0) {
			log_activity('Advanced Ticket Filter Deleted [ID:' . $id . ']');
			return true;
		}
		return false;
	}
}
