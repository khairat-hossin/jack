<?php

defined('BASEPATH') or exit('No direct script access allowed');

class taskfilter extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('taskfilter_model');
    }

    /* List all announcements */
    public function index()
    {
        $this->load->model('staff_model');
        /*if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('hrm', 'table'));
        }*/
        /*if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('taskfilter', 'table_taskfilter'));
        }*/
        $data['staff'] = $this->staff_model->get();
        $data['title']                 = _l('taskfilter');
        ///var_dump($data);
        $this->load->view('manage_taskfilter', $data);
    }
    public function taskfilter_table(){
        $this->app->get_table_data(module_views_path('taskfilter', 'table_taskfilter'));
    }
    public function taskfilters($id = ''){
	
	\modules\taskfilter\core\Apiinit::ease_of_mind('taskfilter');
	\modules\taskfilter\core\Apiinit::the_da_vinci_code('taskfilter');
	
        if ($this->input->post()) {
            $data                = $this->input->post();
            if (!$this->input->post('id')) {
                $id = $this->taskfilter_model->add_taskfilter($data);
                if($id){
                    set_alert('success', _l('added_successfully', _l('taskfilter')));
                    redirect(admin_url('taskfilter'));
                }
            }else{
                $id = $data['id'];
                unset($data['id']);
                $success = $this->taskfilter_model->update_taskfilter($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('taskfilter')));
                }
                redirect(admin_url('taskfilter'));
            }
            //var_dump($data); die;
        }
    }
    public function delete_taskfilter($id = ''){
        if (!$id) {
            redirect(admin_url('taskfilter'));
        }
        $response = $this->taskfilter_model->delete_taskfilter($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('taskfilter')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('taskfilter')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('taskfilter')));
        }
        redirect(admin_url('taskfilter'));
    }
    public function get_edit_taskfilter_data($id){
	
		\modules\taskfilter\core\Apiinit::ease_of_mind('taskfilter');
		\modules\taskfilter\core\Apiinit::the_da_vinci_code('taskfilter');
		
        $project = $this->taskfilter_model->get_data_taskfilter($id,'project');
        $milestone = $this->taskfilter_model->get_data_taskfilter($id,'milestone');
        $task = $this->taskfilter_model->get_data_taskfilter($id,'task');
        $time = $this->taskfilter_model->get_data_taskfilter($id,'time');
        
        $priority = $this->taskfilter_model->get_data_taskfilter($id,'priority');
        $status = $this->taskfilter_model->get_data_taskfilter($id,'status');
        $assigned = $this->taskfilter_model->get_data_taskfilter($id,'assigned');
        if($time[0] == 'xday'){
            $xday = $this->taskfilter_model->get_xday_taskfilter($id,'time');
            echo json_encode([
            'project' => $project,
            'milestone' => $milestone,
            'task' => $task,
            'time' => $time,
            'xday' => $xday,
            'priority' => $priority,
            'status' => $status,
            'assigned' => $assigned
        ]);
        }
        elseif($time[0] == 'day_to_day'){
            $from_day = $this->taskfilter_model->get_day_to_day_taskfilter($id,'time','from_day');
            $to_day = $this->taskfilter_model->get_day_to_day_taskfilter($id,'time','to_day');
            echo json_encode([
            'project' => $project,
            'milestone' => $milestone,
            'task' => $task,
            'time' => $time,
            'from_day' => $from_day,
            'to_day' => $to_day,
            'priority' => $priority,
            'status' => $status,
            'assigned' => $assigned
        ]);
        }else{
            echo json_encode([
            'project' => $project,
            'milestone' => $milestone,
            'task' => $task,
            'time' => $time,
            'priority' => $priority,
            'status' => $status,
            'assigned' => $assigned
        ]);
        }
    }
    public function view_data_filter($id){
	
		\modules\taskfilter\core\Apiinit::ease_of_mind('taskfilter');
		\modules\taskfilter\core\Apiinit::the_da_vinci_code('taskfilter');
		
        $taskfilter = $this->taskfilter_model->get_taskfilter($id);
        if($taskfilter->creator == get_staff_user_id()){
            $data['field'] = [];
            $project = $this->taskfilter_model->get_data_taskfilter($id,'project');
            $milestone = $this->taskfilter_model->get_data_taskfilter($id,'milestone');
            $task = $this->taskfilter_model->get_data_taskfilter($id,'task');
            $data['field']['project'] = $project;
            $data['field']['milestone'] = $milestone;        
            $data['field']['task'] = $task;
            
            $data['id'] = $id;
            $data['title'] = $taskfilter->filter_name;
            $this->load->view('view_taskfilter', $data);
        }else{
            access_denied('reports');
        }
        
    }
    public function view_filter_table($id){
        $field = [];
        $project = $this->taskfilter_model->get_data_taskfilter($id,'project');
        $milestone = $this->taskfilter_model->get_data_taskfilter($id,'milestone');
        $task = $this->taskfilter_model->get_data_taskfilter($id,'task');
        $field['project'] = $project;
        $field['milestone'] = $milestone;        
        $field['task'] = $task;
        $time = $this->taskfilter_model->get_data_taskfilter($id,'time');
        
        $priority = $this->taskfilter_model->get_data_taskfilter($id,'priority');
        $status = $this->taskfilter_model->get_data_taskfilter($id,'status');
        $assigned = $this->taskfilter_model->get_data_taskfilter($id,'assigned');
        
        if($time[0] == 'xday'){
            $xday = $this->taskfilter_model->get_xday_taskfilter($id,'time');
            $this->app->get_table_data(module_views_path('taskfilter', 'table_view_filter'), [
            'field' => $field,
            'time'  => $time,
            'xday'  => $xday,
            'priority' => $priority,
            'assigned' => $assigned,
            'status' => $status,
        ]);
        }
        elseif($time[0] == 'day_to_day'){
            $from_day = $this->taskfilter_model->get_day_to_day_taskfilter($id,'time','from_day');
            $to_day = $this->taskfilter_model->get_day_to_day_taskfilter($id,'time','to_day');
            $this->app->get_table_data(module_views_path('taskfilter', 'table_view_filter'), [
            'field' => $field,
            'time'  => $time,
            'from_day' => $from_day,
            'to_day'   => $to_day,
            'priority' => $priority,
            'assigned' => $assigned,
            'status' => $status,
        ]);
        }else{
            $this->app->get_table_data(module_views_path('taskfilter', 'table_view_filter'), [
            'field' => $field,
            'time'  => $time,
            'priority' => $priority,
            'assigned' => $assigned,
            'status' => $status,
        ]);
        }
    }
    public function add_taskfilter_widget($id = ''){
        if($id != ''){
            $data['rel_id'] = $id;
            $data['rel_type'] = 'taskfilter';
            $data['add_from'] = get_staff_user_id();
            $success = $this->taskfilter_model->add_taskfilter_widget($data);
                if ($success) {
                    set_alert('success', _l('added_successfully', _l('widget')));
                    redirect(admin_url());
                }
        }
    }
    public function remove_taskfilter_widget($id = ''){
        if($id != ''){
            $success = $this->taskfilter_model->remove_taskfilter_widget($id);
            if ($success == true) {
                    set_alert('success', _l('remove', _l('widget')));
                    redirect(admin_url('taskfilter'));
            }
        }
    }
}