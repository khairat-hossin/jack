<?php
//documentation: https://help.perfexcrm.com/add-new-task-status/
hooks()->add_filter('before_get_task_statuses','my_add_custom_task_status');

function my_add_custom_task_status($current_statuses){
    // Push new status to the current statuses
    $current_statuses[] = array(
           'id'=>50, // new status with id 50
           'color'=>'#989898',
           'name'=>'Awaiting Feedback',
           'order'=>10,
           'filter_default'=>true, // true or false

        );
        
    // Push another status (delete this code if you need to add only 1 status)
    $current_statuses[] = array(
          'id'=>51, //new status with new id 51
          'color'=>'#FF8E30',
          'name'=>'Executive Hold',
          'order'=>11,
          'filter_default'=>true // true or false
        );
    
    $current_statuses[] = array(
          'id'=>52, //new status with new id 51
          'color'=>'#26D300',
          'name'=>'Approved By Supervisor',
          'order'=>12,
          'filter_default'=>true // true or false
        );
    
    // Return the statuses
    return $current_statuses;
}

//project status

hooks()->add_filter('before_get_project_statuses','my_add_custom_project_status');

function my_add_custom_project_status($current_statuses){
    // Push new status to the current statuses
    $current_statuses[] = array(
           'id'=>50, // new status with id 50
           'color'=>'#26FFA0',
           'name'=>'Template',
           'order'=>10,
           'filter_default'=>true, // true or false
        );
        
  
    // Return the statuses
    return $current_statuses;
}
