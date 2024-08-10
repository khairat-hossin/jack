<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_ticket_filters extends AdminController
{
	public function __construct()
	{
		parent::__construct(); 
		$this->load->model('tickets_model');
		$this->load->model('departments_model');
		if (!is_admin() && !has_permission('si_ticket_filters', '', 'view') && !has_permission('si_ticket_filters', '', 'view_own')) {
			access_denied(_l('si_ticket_filters'));
		}
	}
	
	private function get_where_report_period($field = 'date')
	{
		$months_report      = $this->input->post('report_months');
		$custom_date_select = '';
		if ($months_report != '') {
			if (is_numeric($months_report)) {
				// Last month
				if ($months_report == '1') {
					$beginMonth = date('Y-m-01', strtotime('first day of last month'));
					$endMonth   = date('Y-m-t', strtotime('last day of last month'));
				} else {
					$months_report = (int) $months_report;
					$months_report--;
					$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
					$endMonth   = date('Y-m-t');
				}

				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
			} elseif ($months_report == 'today') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-d') . '" AND "' . date('Y-m-d') . '")';
			} elseif ($months_report == 'this_week') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-d', strtotime('monday this week')) . '" AND "' . date('Y-m-d', strtotime('sunday this week')) . '")';
			} elseif ($months_report == 'last_week') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-d', strtotime('monday last week')) . '" AND "' . date('Y-m-d', strtotime('sunday last week')) . '")';	
			} elseif ($months_report == 'this_month') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
			} elseif ($months_report == 'this_year') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' .
				date('Y-m-d', strtotime(date('Y-01-01'))) .
				'" AND "' .
				date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
			} elseif ($months_report == 'last_year') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' .
				date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
				'" AND "' .
				date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
			} elseif ($months_report == 'custom') {
				$from_date = to_sql_date($this->input->post('report_from'));
				$to_date   = to_sql_date($this->input->post('report_to'));
				if ($from_date == $to_date) {
					$custom_date_select = 'AND ' . $field . ' = "' . $from_date . '"';
				} else {
					$custom_date_select = 'AND (' . $field . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
				}
			}
		}
		
		 return $custom_date_select;
	}
	
	public function tickets_report()
	{
		$overview = [];
		
		$saved_filter_name='';
		$filter_id = $this->input->get('filter_id');
		if($filter_id!='' && is_numeric($filter_id) && empty($this->input->post()))
		{
			$filter_obj = $this->si_ticket_filter_model->get($filter_id);
			if(!empty($filter_obj))
			{
				$_POST = unserialize($filter_obj->filter_parameters);
				$saved_filter_name = $filter_obj->filter_name;
			}	
		}	

		$has_permission_view   = has_permission('si_ticket_filters', '', 'view');

		if (!$has_permission_view) {
			$staff_id = get_staff_user_id();
		} elseif ($this->input->post('member')) {
			$staff_id = $this->input->post('member');
		} else {
			$staff_id = '';
		}
		
		if ($this->input->post('rel_id')) {
			$rel_id = $this->input->post('rel_id');
		} else {
			$rel_id = '';
		}
		
		if ($this->input->post('rel_type')) {
			$rel_type = $this->input->post('rel_type');
		} else {
			$rel_type = '';
		}
		if ($this->input->post('group_id')) {
			$group_id = $this->input->post('group_id');
		} else {
			$group_id = '';
		}
		if ($this->input->post('group_by')) {
			$group_by = $this->input->post('group_by');
		} else {
			$group_by = '';
		}
		if ($this->input->post('date_by')) {
			$date_by = $this->input->post('date_by');
		} else {
			$date_by = 'date';
		}
		if ($this->input->post('department')!='') {
			$department = $this->input->post('department');
		} else {
			$department = '';
		}
		if ($this->input->post('priority')!='') {
			$priority = $this->input->post('priority');
		} else {
			$priority = '';
		}
		if ($this->input->post('service')!='') {
			$service = $this->input->post('service');
		} else {
			$service = '';
		}
		$tag = $this->input->post('tags');//fetch array of tags
		if(empty($tag))
			$tag=array('');//blank for All Tags

		$status = $this->input->post('status');//fetch array of statuses
		if(empty($status))
			$status = hooks()->apply_filters('default_tickets_list_statuses', [1, 2, 4]);	
			
		$hide_columns = $this->input->post('hide_columns');//fetch array of statuses
		if(empty($hide_columns))
			$hide_columns=array();	
		

		$fetch_month_from = $date_by;
		
		$save_filter = $this->input->post('save_filter');
		$filter_name='';
		$current_user_id = get_staff_user_id();
		if($save_filter==1)
		{
			$filter_name=$this->input->post('filter_name');
			$all_filter = $this->input->post();
			unset($all_filter['save_filter']);
			unset($all_filter['filter_name']);
			$saved_filter_name = $filter_name;
			$filter_parameters = serialize($all_filter);
			$filter_data = array('filter_name'=>$filter_name,
								 'filter_parameters'=>$filter_parameters,
								 'staff_id'=>$current_user_id);
			if($filter_id!='' && is_numeric($filter_id))
				$this->si_ticket_filter_model->update($filter_data,$filter_id);
			else					 
				$new_filter_id = $this->si_ticket_filter_model->add($filter_data);
		}	


		//allowed departments to staff
		if (!is_admin()) {
            if (get_option('staff_access_only_assigned_departments') == 1) {
                $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                if (count($staff_deparments_ids) > 0)
					$this->db->where_in('department', $staff_deparments_ids);
				else
					$this->db->where('department', '');	
            }
        }
		// Ticket rel_name
		$sqlTicketSelect = db_prefix().'tickets.*,' . db_prefix().'clients.company as rel_name,'.db_prefix() . 'departments.name as department_name,'.db_prefix() . 'services.name as service_name,CONCAT(' . db_prefix() . 'contacts.firstname, \' \', ' . db_prefix() . 'contacts.lastname) as contact_full_name,statuscolor';

		$this->db->select($sqlTicketSelect);
		
		if($this->input->post('report_months')!='')
		{
			$custom_date_select = $this->get_where_report_period('DATE('.$fetch_month_from.')');
			$this->db->where("1=1 ".$custom_date_select);
		}
		
		if($rel_type!='')
		{
			if($rel_type=='customer')
				$this->db->where(db_prefix().'tickets.userid<>', 0);
			if($rel_type=='project')
				$this->db->where(db_prefix().'tickets.project_id<>', 0);
		}	
		if($department!='')
			$this->db->where('department', $department);
		if($priority!='')
			$this->db->where('priority', $priority);
		if($service!='')
			$this->db->where('service', $service);			
		if ($rel_id && $rel_id != '') {
			if($rel_type=='customer')
				$this->db->where(db_prefix().'tickets.userid', $rel_id);
			if($rel_type=='project')
				$this->db->where(db_prefix().'tickets.project_id', $rel_id);
		}
		if ($group_id !='' && $rel_type == 'customer') {
			$this->db->join(db_prefix() .'customer_groups',db_prefix() .'customer_groups.customer_id='.db_prefix() . 'tickets.userid','left');
			$this->db->where('groupid', $group_id);
		}

		if (!$has_permission_view || is_numeric($staff_id)) {
			$this->db->where('assigned',$staff_id);
		}
		
		if ($tag && !in_array('',$tag)) {
			$this->db->where_in(db_prefix() . 'tickets.ticketid','select distinct(rel_id) from '.db_prefix() . 'taggables where '.db_prefix() . 'taggables.rel_type=\'ticket\' and tag_id in('.implode(',',$tag).')',false);
		}

		if ($status && !in_array('',$status)) {
			$this->db->where_in('status', $status);
		}
		
		//joins
		$this->db->join(db_prefix() .'contacts',db_prefix() .'contacts.id='.db_prefix() . 'tickets.contactid','left');
		$this->db->join(db_prefix() .'services',db_prefix() .'services.serviceid='.db_prefix() . 'tickets.service','left');
		$this->db->join(db_prefix() .'departments',db_prefix() .'departments.departmentid='.db_prefix() . 'tickets.department','left');
		$this->db->join(db_prefix() .'tickets_status',db_prefix() .'tickets_status.ticketstatusid='.db_prefix() . 'tickets.status','left');
		$this->db->join(db_prefix() .'clients',db_prefix() .'clients.userid='.db_prefix() . 'tickets.userid','left');
		$this->db->join(db_prefix() .'tickets_priorities',db_prefix() .'tickets_priorities.priorityid='.db_prefix() . 'tickets.priority','left');

		$this->db->order_by($fetch_month_from, 'DESC');
		$overview_ = $this->db->get(db_prefix() . 'tickets')->result_array();

		unset($overview[0]);
		foreach($overview_ as $row)
		{
			$by='';
			if($group_by=='contact' && $row['contact_full_name']!='')
				$by = $row['contact_full_name'];
			elseif($group_by=='service' && $row['service_name']!='')
				$by = $row['service_name'];	
			elseif($group_by=='department_name' && $row['department']!=0)
				$by = ucfirst($row['department_name']);	
			elseif($group_by=='subject_name')
				$by = $row['subject'];		
			elseif($group_by=='status')
				$by = ticket_status_translate($row['status']);
				
			$overview[$by][]=$row;
			ksort($overview);
		}	

		$overview = [
			'staff_id' => $staff_id,
			'detailed' => $overview,
			'rel_id'   => $rel_id,
			'rel_type' => $rel_type,
			'group_id' => $group_id,
		];

		$data['members']  = $this->staff_model->get();
		$data['overview'] = $overview['detailed'];
		$data['staff_id'] = $overview['staff_id'];
		$data['title']    = _l('si_tf_filters_menu');
		$data['rel_id']   = $overview['rel_id'];
		$data['rel_type'] = $overview['rel_type'];
		$data['department'] = $department;
		$data['priority'] = $priority;
		$data['service'] = $service;
		$data['groups']   = $this->clients_model->get_groups();//customer_groups
		$data['group_id'] = $group_id;
		$data['report_months'] = $this->input->post('report_months');
		$data['report_from'] = $this->input->post('report_from');
		$data['report_to'] = $this->input->post('report_to');
		$data['group_by'] = $group_by;
		$data['date_by'] = $date_by;
		$data['statuses']  =$status;
		$data['tags']  =$tag;
		$data['departments']        = $this->departments_model->get();
		$data['ticket_statuses'] 	= $this->tickets_model->get_ticket_status();
		$data['priorities']         = $this->tickets_model->get_priority();
        $data['services']           = $this->tickets_model->get_service();
		$data['filter_templates'] = $this->si_ticket_filter_model->get_templates($current_user_id);
		$data['saved_filter_name'] = $saved_filter_name;
		$data['hide_columns'] = $hide_columns;
		$this->load->view('ticket_report', $data);
	}
	
	function list_filters()
	{
		$data=array();
		$data['title']    = _l('si_tf_templates_menu');
		$current_user_id = get_staff_user_id();
		$data['filter_templates'] = $this->si_ticket_filter_model->get_templates($current_user_id);
		$this->load->view('ticket_list_filters', $data);
	}
	function del_ticket_filter($id)
	{
		$current_user_id = get_staff_user_id();
		$this->si_ticket_filter_model->delete($id,$current_user_id);
		redirect('si_ticket_filters/list_filters');
	}
}
