<?php

defined('BASEPATH') or exit('No direct script access allowed');

class taskfilter_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer (optional)
     * @return object
     * Get single goal
     */
    public function add_taskfilter($data){
        //var_dump($data); die;
        $project = [];
        $milestone = [];
        $task = [];
        $priority = [];
        $status = [];
        $assigned = [];
        if(isset($data['project_name'])){
            $project[] = 'name'; 
            unset($data['project_name']);
        }
        if(isset($data['project_clientid'])){
            $project[] = 'clientid'; 
            unset($data['project_clientid']);
        }
        if(isset($data['project_start_date'])){
            $project[] = 'start_date'; 
            unset($data['project_start_date']);
        }
        if(isset($data['project_deadline'])){
            $project[] = 'deadline'; 
            unset($data['project_deadline']);
        }
        if(isset($data['project_status'])){
            $project[] = 'status'; 
            unset($data['project_status']);
        }
        if(isset($data['milestone_name'])){
            $milestone[] = 'name'; 
            unset($data['milestone_name']);
        }
        if(isset($data['milestone_due_date'])){
            $milestone[] = 'due_date'; 
            unset($data['milestone_due_date']);
        }
        if(isset($data['task_name'])){
            $task[] = 'name'; 
            unset($data['task_name']);
        }
        if(isset($data['task_status'])){
            $task[] = 'status'; 
            unset($data['task_status']);
        }
        if(isset($data['task_priority'])){
            $task[] = 'priority'; 
            unset($data['task_priority']);
        }
        if(isset($data['task_startdate'])){
            $task[] = 'startdate'; 
            unset($data['task_startdate']);
        }
        if(isset($data['task_duedate'])){
            $task[] = 'duedate'; 
            unset($data['task_duedate']);
        }
        if(isset($data['task_assigned'])){
            $task[] = 'assigned'; 
            unset($data['task_assigned']);
        }
        if(isset($data['task_estimate_hour'])){
            $task[] = 'estimate_hour'; 
            unset($data['task_estimate_hour']);
        }
        if(isset($data['task_spent_hour'])){
            $task[] = 'spent_hour'; 
            unset($data['task_spent_hour']);
        }
        if(isset($data['task_addedfrom'])){
            $task[] = 'addedfrom'; 
            unset($data['task_addedfrom']);
        }
        if(isset($data['task_watch'])){
            $task[] = 'watch'; 
            unset($data['task_watch']);
        }
        if(isset($data['priority'])){
            $priority = $data['priority'];
            unset($data['priority']);
        }
        if(isset($data['status'])){
            $status = $data['status'];
            unset($data['status']);
        }
        if(isset($data['assigned'])){
            $assigned = $data['assigned'];
            unset($data['assigned']);
        }
        if(isset($data['time'])){
            if($data['time'] == 'xday'){
                if($data['xday'] != ''){
                $xday = $data['xday'];
                unset($data['xday']);
                }else{
                    unset($data['xday']);
                }
                unset($data['time']);
                unset($data['from_date']);
                unset($data['to_date']);
            }elseif ($data['time'] == 'day_to_day') {
                if($data['from_date'] != ''){
                    $from_date = to_sql_date($data['from_date']);
                unset($data['from_date']);
                }else{
                    unset($data['from_date']);
                }
                if($data['to_date'] != ''){
                    $to_date = to_sql_date($data['to_date']);
                    unset($data['to_date']);
                }else{
                    unset($data['to_date']);
                }
                unset($data['time']);
                unset($data['xday']);
            }else{
                $time = $data['time'];
                unset($data['time']);
                unset($data['xday']);
                unset($data['from_date']);
                unset($data['to_date']);
            }
           
        }
        $this->db->insert('tbltaskfilter', $data);
        $filter_id = $this->db->insert_id();
        if($filter_id){
            if(isset($project)){
                foreach($project as $pj){
                    $this->db->insert('tblfilter_detail' ,[
                        'taskfilter' => $filter_id,
                        'value'       => $pj,
                        'xday'      => '',
                        'from_date' => '',
                        'to_date' => '',
                        'type' => 'project',
                    ]);
                }
            }
            if(isset($milestone)){
                foreach($milestone as $mi){
                    $this->db->insert('tblfilter_detail' ,[
                        'taskfilter' => $filter_id,
                        'value'       => $mi,
                        'xday'      => '',
                        'from_date' => '',
                        'to_date' => '',
                        'type' => 'milestone',
                    ]);
                }
            }
            if(isset($task)){
                foreach($task as $tsk){
                    $this->db->insert('tblfilter_detail' ,[
                        'taskfilter' => $filter_id,
                        'value'       => $tsk,
                        'xday'      => '',
                        'from_date' => '',
                        'to_date' => '',
                        'type' => 'task',
                    ]);
                }
            }
            if(isset($priority)){
                foreach($priority as $pri){
                    $this->db->insert('tblfilter_detail' ,[
                        'taskfilter' => $filter_id,
                        'value'       => $pri,
                        'xday'      => '',
                        'from_date' => '',
                        'to_date' => '',
                        'type' => 'priority',
                    ]);
                }
            }
            if(isset($status)){
                foreach($status as $sts){
                    $this->db->insert('tblfilter_detail' ,[
                        'taskfilter' => $filter_id,
                        'value'       => $sts,
                        'xday'      => '',
                        'from_date' => '',
                        'to_date' => '',
                        'type' => 'status',
                    ]);
                }
            }
            if(isset($assigned)){
                foreach($assigned as $asig){
                    $this->db->insert('tblfilter_detail' ,[
                        'taskfilter' => $filter_id,
                        'value'       => $asig,
                        'xday'      => '',
                        'from_date' => '',
                        'to_date' => '',
                        'type' => 'assigned',
                    ]);
                }
            }
            if(isset($time)){
                $this->db->insert('tblfilter_detail' ,[
                        'taskfilter' => $filter_id,
                        'value'       => $time,
                        'xday'      => '',
                        'from_date' => '',
                        'to_date' => '',
                        'type' => 'time',
                    ]);
            }
            if(isset($xday)){
                 $this->db->insert('tblfilter_detail' ,[
                        'taskfilter' => $filter_id,
                        'value'       => 'xday',
                        'xday'      => $xday,
                        'from_date' => '',
                        'to_date' => '',
                        'type' => 'time',
                    ]);
            }
            if(isset($from_date) && isset($to_date)){
                 $this->db->insert('tblfilter_detail' ,[
                        'taskfilter' => $filter_id,
                        'value'       => 'day_to_day',
                        'xday'      => '',
                        'from_date' => $from_date,
                        'to_date' => $to_date,
                        'type' => 'time',
                    ]);
            }
        return $filter_id;
        }
        return false; 
    }
    public function update_taskfilter($data, $id){
        $project = [];
        $milestone = [];
        $task = [];
        $priority = [];
        $status = [];
        $assigned = [];
        if(isset($data['project_name'])){
            $project[] = 'name'; 
            unset($data['project_name']);
        }
        if(isset($data['project_clientid'])){
            $project[] = 'clientid'; 
            unset($data['project_clientid']);
        }
        if(isset($data['project_start_date'])){
            $project[] = 'start_date'; 
            unset($data['project_start_date']);
        }
        if(isset($data['project_deadline'])){
            $project[] = 'deadline'; 
            unset($data['project_deadline']);
        }
        if(isset($data['project_status'])){
            $project[] = 'status'; 
            unset($data['project_status']);
        }
        if(isset($data['milestone_name'])){
            $milestone[] = 'name'; 
            unset($data['milestone_name']);
        }
        if(isset($data['milestone_due_date'])){
            $milestone[] = 'due_date'; 
            unset($data['milestone_due_date']);
        }
        if(isset($data['task_name'])){
            $task[] = 'name'; 
            unset($data['task_name']);
        }
        if(isset($data['task_status'])){
            $task[] = 'status'; 
            unset($data['task_status']);
        }
        if(isset($data['task_priority'])){
            $task[] = 'priority'; 
            unset($data['task_priority']);
        }
        if(isset($data['task_startdate'])){
            $task[] = 'startdate'; 
            unset($data['task_startdate']);
        }
        if(isset($data['task_duedate'])){
            $task[] = 'duedate'; 
            unset($data['task_duedate']);
        }
        if(isset($data['task_assigned'])){
            $task[] = 'assigned'; 
            unset($data['task_assigned']);
        }
        if(isset($data['task_estimate_hour'])){
            $task[] = 'estimate_hour'; 
            unset($data['task_estimate_hour']);
        }
        if(isset($data['task_spent_hour'])){
            $task[] = 'spent_hour'; 
            unset($data['task_spent_hour']);
        }
        if(isset($data['task_addedfrom'])){
            $task[] = 'addedfrom'; 
            unset($data['task_addedfrom']);
        }
        if(isset($data['task_watch'])){
            $task[] = 'watch'; 
            unset($data['task_watch']);
        }
        if(isset($data['priority'])){
            $priority = $data['priority'];
            unset($data['priority']);
        }
        if(isset($data['status'])){
            $status = $data['status'];
            unset($data['status']);
        }
        if(isset($data['assigned'])){
            $assigned = $data['assigned'];
            unset($data['assigned']);
        }
        if(isset($data['time'])){
            if($data['time'] == 'xday'){
                if($data['xday'] != ''){
                $xday = $data['xday'];
                unset($data['xday']);
                }else{
                    unset($data['xday']);
                }
                unset($data['time']);
                unset($data['from_date']);
                unset($data['to_date']);
            }elseif ($data['time'] == 'day_to_day') {
                if($data['from_date'] != ''){
                    $from_date = to_sql_date($data['from_date']);
                unset($data['from_date']);
                }else{
                    unset($data['from_date']);
                }
                if($data['to_date'] != ''){
                    $to_date = to_sql_date($data['to_date']);
                    unset($data['to_date']);
                }else{
                    unset($data['to_date']);
                }
                unset($data['time']);
                unset($data['xday']);
            }else{
                $time = $data['time'];
                unset($data['time']);
                unset($data['xday']);
                unset($data['from_date']);
                unset($data['to_date']);
            }
           
        }
        $this->db->where('id',$id);
        $this->db->update('tbltaskfilter',$data);

        $this->db->where('taskfilter',$id);
        $this->db->delete('tblfilter_detail');
        if(isset($project)){
                foreach($project as $pj){
                    $this->db->insert('tblfilter_detail' ,[
                        'taskfilter' => $id,
                        'value'       => $pj,
                        'xday'      => '',
                        'from_date' => '',
                        'to_date' => '',
                        'type' => 'project',
                    ]);
                }
        }
        if(isset($milestone)){
            foreach($milestone as $mi){
                $this->db->insert('tblfilter_detail' ,[
                    'taskfilter' => $id,
                    'value'       => $mi,
                    'xday'      => '',
                    'from_date' => '',
                    'to_date' => '',
                    'type' => 'milestone',
                ]);
            }
        }
        if(isset($task)){
            foreach($task as $tsk){
                $this->db->insert('tblfilter_detail' ,[
                    'taskfilter' => $id,
                    'value'       => $tsk,
                    'xday'      => '',
                    'from_date' => '',
                    'to_date' => '',
                    'type' => 'task',
                ]);
            }
        }
        if(isset($priority)){
            foreach($priority as $pri){
                $this->db->insert('tblfilter_detail' ,[
                    'taskfilter' => $id,
                    'value'       => $pri,
                    'xday'      => '',
                    'from_date' => '',
                    'to_date' => '',
                    'type' => 'priority',
                ]);
            }
        }
        if(isset($status)){
            foreach($status as $sts){
                $this->db->insert('tblfilter_detail' ,[
                    'taskfilter' => $id,
                    'value'       => $sts,
                    'xday'      => '',
                    'from_date' => '',
                    'to_date' => '',
                    'type' => 'status',
                ]);
            }
        }
        if(isset($assigned)){
            foreach($assigned as $asig){
                $this->db->insert('tblfilter_detail' ,[
                    'taskfilter' => $id,
                    'value'       => $asig,
                    'xday'      => '',
                    'from_date' => '',
                    'to_date' => '',
                    'type' => 'assigned',
                ]);
            }
        }
        if(isset($time)){
            $this->db->insert('tblfilter_detail' ,[
                    'taskfilter' => $id,
                    'value'       => $time,
                    'xday'      => '',
                    'from_date' => '',
                    'to_date' => '',
                    'type' => 'time',
                ]);
        }
        if(isset($xday)){
             $this->db->insert('tblfilter_detail' ,[
                    'taskfilter' => $id,
                    'value'       => 'xday',
                    'xday'      => $xday,
                    'from_date' => '',
                    'to_date' => '',
                    'type' => 'time',
                ]);
        }
        if(isset($from_date) && isset($to_date)){
             $this->db->insert('tblfilter_detail' ,[
                    'taskfilter' => $id,
                    'value'       => 'day_to_day',
                    'xday'      => '',
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'type' => 'time',
                ]);
        }
        return true;
    }
    public function delete_taskfilter($id){
        $this->db->where('taskfilter', $id);
        $this->db->delete('tblfilter_detail');
        $this->db->where('id', $id);
        $this->db->delete('tbltaskfilter');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    public function get_data_taskfilter($filter_id, $type =''){
        $data = [];
        $old_data = $this->db->query('select value from tblfilter_detail where type = "'.$type.'" and taskfilter = '.$filter_id)->result_array();
        foreach($old_data as $old){
            $data[] = $old['value'];
        }
        return $data;
    }
    public function get_xday_taskfilter($filter_id, $type =''){
        $data = [];
        $old_data = $this->db->query('select xday from tblfilter_detail where type = "'.$type.'" and taskfilter = '.$filter_id)->result_array();
        foreach($old_data as $old){
            $data[] = $old['xday'];
        }
        return $data;
    }
    public function get_day_to_day_taskfilter($filter_id, $subtype){
        $data_from = [];
        $data_to = [];
        $old_data = $this->db->query('select from_date, to_date from tblfilter_detail where type = "" and taskfilter = '.$filter_id)->result_array();
        foreach($old_data as $old){
            $data_from[] = $old['from_date'];
            $data_to[] = $old['to_date'];
        }
        if($subtype == 'from_day'){
            return $data_from;
        }else{
            return $data_to;
        }
    }
    public function get_taskfilter($id){
        return $this->db->query('Select * from tbltaskfilter where id = '.$id)->row();
    }
    public function add_taskfilter_widget($data){
        $this->db->insert('tbllist_widget', $data);
        $filter_id = $this->db->insert_id();
        return $filter_id;
    }
    public function remove_taskfilter_widget($id){
        $this->db->where('id', $id);
        $this->db->delete('tbllist_widget');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function get_filter_widget($staff, $type = ''){
        return $this->db->query('select * from tbllist_widget where add_from = '.$staff.' and rel_type = "'.$type.'"')->result_array();
    }
    public function get_name_taskfilter($id){
        $taskfilter = $this->get_taskfilter($id);
        $data['title'] = $taskfilter->filter_name;
        return $data['title'];
    }
    public function view_data_filter_helper($id){
        $data['field'] = [];
        $project = $this->get_data_taskfilter($id,'project');
        $milestone = $this->get_data_taskfilter($id,'milestone');
        $task = $this->get_data_taskfilter($id,'task');
        $data['field']['project'] = $project;
        $data['field']['milestone'] = $milestone;        
        $data['field']['task'] = $task;
        return $data['field'] ;
    }
}