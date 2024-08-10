<?php

defined('BASEPATH') or exit('No direct script access allowed');
$arr_table = [];
foreach ($field as $key => $value) {
    if($key == 'project'){
        foreach($value as $pj){
            $arr_table[] = 'tblprojects.'.$pj;

        }
    }
    if($key == 'milestone'){
        foreach($value as $mi){
            $arr_table[] = 'tblmilestones.'.$mi;
        }
    }
    if($key == 'task'){
        foreach($value as $tsk){
            if($tsk == 'assigned'){
                $arr_table[] = 'tbltasks.id';
            }elseif($tsk == 'spent_hour'){
                $arr_table[] = 'tbltasks.description';
            }
            else{
                $arr_table[] = 'tbltasks.'.$tsk;
            }
            
        }
    }
}
$aColumns = $arr_table;

$sIndexColumn = 'id';
$sTable       = 'tbltasks';
$join = [
         'Left join tblprojects ON tblprojects.id = tbltasks.rel_id and tbltasks.rel_type = "project"',
         'Left join tblmilestones ON tblmilestones.id = tbltasks.milestone'
];
$where = [];
if(isset($time)){
    if($time[0] == 'today'){
        array_push($where, 'AND (tbltasks.startdate <= "' . date('Y-m-d') . '" and (tbltasks.duedate is null or tbltasks.duedate >= "' . date('Y-m-d') . '"))');
    }
    elseif($time[0] == 'this_month'){

        array_push($where, 'AND (month(tbltasks.startdate) <= "' . date('m') . '" and (tbltasks.duedate is null or month(tbltasks.duedate) >= "' . date('m') . '" or (month(tbltasks.duedate) < "' . date('m') . '" and year(tbltasks.duedate) > "' . date('Y') . '")))');

    }elseif ($time[0] == 'last_month') {

        $last_month = date('m') - 1;
        array_push($where, 'AND (month(tbltasks.startdate) <= "' . $last_month . '" and (tbltasks.duedate is null or month(tbltasks.duedate) >= "' . $last_month . '" or (month(tbltasks.duedate) < "' . $last_month . '" and year(tbltasks.duedate) > "' . $last_month . '")))');

    }elseif ($time[0] == 'next_month') {

        $next_month = date('m') + 1;
        array_push($where, 'AND (month(tbltasks.startdate) <= "' . $next_month . '" and (tbltasks.duedate is null or month(tbltasks.duedate) >= "' . $next_month . '" or (month(tbltasks.duedate) < "' . $next_month . '" and year(tbltasks.duedate) > "' . $next_month . '")))');

    }elseif ($time[0] == 'xday'){

        $now = date('Y-m-d');
        $newdate = strtotime(date("Y-m-d", strtotime($now)) ." +$xday[0] day");
        $day = strftime("%Y-%m-%d", $newdate);
        array_push($where, 'AND (tbltasks.startdate <= "' . $day . '" and (tbltasks.duedate is null or tbltasks.duedate >= "' . $day . '"))');

    }elseif ($time[0] == 'day_to_day'){

        array_push($where, 'AND ((tbltasks.startdate >= "' . $from_day[0] . '" and tbltasks.startdate <= "' . $to_day[0] . '") or (tbltasks.startdate <= "' . $from_day[0] . '" and tbltasks.duedate >= "' . $to_day[0] . '") or (tbltasks.startdate <= "' . $from_day[0] . '" and tbltasks.duedate is null ))');

    }elseif ($time[0] == 'this_week'){

        $monday = date("Y-m-d", strtotime('monday this week'));
        $sunday = date("Y-m-d", strtotime('sunday this week'));
        array_push($where, 'AND ((tbltasks.startdate >= "' . $monday . '" and tbltasks.startdate <= "' . $sunday . '") or (tbltasks.startdate <= "' . $monday . '" and tbltasks.duedate >= "' . $sunday . '") or (tbltasks.startdate <= "' . $monday . '" and tbltasks.duedate is null))');

    }elseif ($time[0] == 'last_week'){

        $monday = date("Y-m-d", strtotime('monday last week'));
        $sunday = date("Y-m-d", strtotime('sunday last week'));
        array_push($where, 'AND ((tbltasks.startdate >= "' . $monday . '" and tbltasks.startdate <= "' . $sunday . '") or (tbltasks.startdate <= "' . $monday . '" and tbltasks.duedate >= "' . $sunday . '") or (tbltasks.startdate <= "' . $monday . '" and tbltasks.duedate is null))');

    }elseif ($time[0] == 'next_week'){

        $monday = date("Y-m-d", strtotime('monday next week'));
        $sunday = date("Y-m-d", strtotime('sunday next week'));
        array_push($where, 'AND ((tbltasks.startdate >= "' . $monday . '" and tbltasks.startdate <= "' . $sunday . '") or (tbltasks.startdate <= "' . $monday . '" and tbltasks.duedate >= "' . $sunday . '") or (tbltasks.startdate <= "' . $monday . '" and tbltasks.duedate is null))');

    }
}
if(isset($priority)){
    $where_pri = '';
    foreach ($priority as $pri) {
        if($pri != '')
        {
            if($where_pri == ''){
                $where_pri .= 'AND (tbltasks.priority = '.$pri ;
            }else{
                $where_pri .= ' or tbltasks.priority = '.$pri;
            }
        }
    }
    if($where_pri != '')
    {
        $where_pri .= ')';
        array_push($where, $where_pri);
    }
}
if(isset($assigned)){
    $where_assign = '';
    foreach ($assigned as $asi) {
        if($asi != '')
        {
            if($where_assign == ''){
                $where_assign .= ' AND (tbltasks.id in (select taskid from tbltask_assigned where staffid = '.$asi.')';
            }else{
                $where_assign .= ' or tbltasks.id in (select taskid from tbltask_assigned where staffid = '.$asi.')';
            }
        }
    }
    if($where_assign != '')
    {
        $where_assign .= ')';

        array_push($where, $where_assign);
    }
}
if(isset($status)){
    $where_stt = '';
    foreach ($status as $stt) {
        if($where_stt == '')
        {
            $where_stt .= ' AND (';
        }else{
            $where_stt .= ' or ';
        }
        if($stt == 'not_started(true)'){
           $where_stt .= '(tbltasks.status = 1 and (tbltasks.duedate is null or tbltasks.duedate > "'.date('Y-m-d').'"))' ;
        }elseif ($stt == 'not_started(late)') {
            $where_stt .= '(tbltasks.status = 1 and (tbltasks.duedate is NOT NULL and tbltasks.duedate <= "'.date('Y-m-d').'"))' ;
        }elseif ($stt == 'in_process(true)'){
            $where_stt .= '((tbltasks.status = 2 or tbltasks.status = 3 or tbltasks.status = 4) and (tbltasks.duedate is null or tbltasks.duedate > "'.date('Y-m-d').'"))';
        }elseif ($stt == 'in_process(late)'){
            $where_stt .= '((tbltasks.status = 2 or tbltasks.status = 3 or tbltasks.status = 4) and (tbltasks.duedate is NOT NULL and tbltasks.duedate <= "'.date('Y-m-d').'"))';
        }elseif ($stt == 'complete(true)') {
            $where_stt .= '(tbltasks.status = 5 and (tbltasks.duedate is null or tbltasks.duedate >= tbltasks.datefinished))';
        }elseif ($stt == 'complete(late)') {
            $where_stt .= '(tbltasks.status = 5 and (tbltasks.duedate is NOT NULL and tbltasks.duedate < tbltasks.datefinished))';
        }
    }
    if($where_stt != '')
    {
        $where_stt .= ')';
        array_push($where, $where_stt);
    }

}
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbltasks.id as task_id','tblprojects.id as project_id','tblmilestones.id as milestone_id','tbltasks.datefinished']);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        $str = 0;
        $sum = 0;
        if($aColumns[$i] == 'tbltasks.status'){
            if($aRow['tbltasks.status'] == 1 && ($aRow['tbltasks.duedate'] == '' || $aRow['tbltasks.duedate'] > date('Y-m-d'))){
                $outputStatus    = '';
                $outputStatus .= '<span class="inline-block label" style="color:#989898; border:1px solid #989898" task-status-table="' . $aRow['tbltasks.status'] . '">';

                $outputStatus .= _l('not_started');
                $outputStatus .= '</span>';
            }elseif($aRow['tbltasks.status'] == 1 && ($aRow['tbltasks.duedate'] != '' && $aRow['tbltasks.duedate'] <= date('Y-m-d'))){
                $outputStatus    = '';
                $outputStatus .= '<span class="inline-block label" style="color:#989898; border:1px solid #989898" task-status-table="' . $aRow['tbltasks.status'] . '">';

                $outputStatus .= _l('not_started').' ';
                
                $outputStatus .= '<span style="color:#ff2d42;" task-status-table="' . $aRow['tbltasks.status'] . '">';

                $outputStatus .= '('._l('late').')';
                $outputStatus .= '</span>';
                $outputStatus .= '</span>';
            }elseif(($aRow['tbltasks.status'] == 2 || $aRow['tbltasks.status'] == 3 || $aRow['tbltasks.status'] == 4)&& ($aRow['tbltasks.duedate'] != '' && $aRow['tbltasks.duedate'] <= date('Y-m-d'))){
                $outputStatus    = '';
                $outputStatus .= '<span class="inline-block label" style="color:#03A9F4; border:1px solid #03A9F4" task-status-table="' . $aRow['tbltasks.status'] . '">';

                $outputStatus .= _l('in_process').' ';
                $outputStatus .= '<span style="color:#ff2d42;" task-status-table="' . $aRow['tbltasks.status'] . '">';

                $outputStatus .= '('._l('late').')';
                $outputStatus .= '</span>';
                $outputStatus .= '</span>';
            }elseif(($aRow['tbltasks.status'] == 2 || $aRow['tbltasks.status'] == 3 || $aRow['tbltasks.status'] == 4) && ($aRow['tbltasks.duedate'] == '' || $aRow['tbltasks.duedate'] > date('Y-m-d'))){
                $outputStatus    = '';
                $outputStatus .= '<span class="inline-block label" style="color:#03A9F4; border:1px solid #03A9F4" task-status-table="' . $aRow['tbltasks.status'] . '">';

                $outputStatus .= _l('in_process');
                $outputStatus .= '</span>';
            }elseif($aRow['tbltasks.status'] == 5 && ($aRow['tbltasks.duedate'] != '' && $aRow['tbltasks.duedate'] < $aRow['datefinished'])){
                $outputStatus    = '';
                $outputStatus .= '<span class="inline-block label" style="color:#84c529; border:1px solid #84c529" task-status-table="' . $aRow['tbltasks.status'] . '">';

                $outputStatus .= _l('complete').' ';
                 $outputStatus .= '<span style="color:#ff2d42;" task-status-table="' . $aRow['tbltasks.status'] . '">';

                $outputStatus .= '('._l('late').')';
                $outputStatus .= '</span>';
                $outputStatus .= '</span>';
            }elseif($aRow['tbltasks.status'] == 5 && ($aRow['tbltasks.duedate'] == '' || $aRow['tbltasks.duedate'] >= $aRow['datefinished'])){
                $outputStatus    = '';
                $outputStatus .= '<span class="inline-block label" style="color:#84c529; border:1px solid #84c529" task-status-table="' . $aRow['tbltasks.status'] . '">';

                $outputStatus .= _l('complete');
                $outputStatus .= '</span>';
            }
           

            $_data = $outputStatus;
        }elseif ($aColumns[$i] == 'tbltasks.priority') {
            $outputPriority = '<span style="color:' . task_priority_color($aRow['tbltasks.priority']) . ';" class="inline-block">' . task_priority($aRow['tbltasks.priority']);
            $outputPriority .= '</span>';
            $_data = $outputPriority;
        }elseif ($aColumns[$i] == 'tblprojects.name'){
            $link = admin_url('projects/view/' . $aRow['project_id']);
            $_data = '<a href="' . $link . '">' . $aRow['tblprojects.name'] . '</a>';
        }elseif ($aColumns[$i] == 'tbltasks.addedfrom') {
            $staff_name = get_staff_full_name($aRow['tbltasks.addedfrom']);
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['tbltasks.addedfrom']) . '">' . staff_profile_image($aRow['tbltasks.addedfrom'], [
                'staff-profile-image-small'], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => $staff_name,
                ]) . '</a>';
        }elseif($aColumns[$i] == 'tbltasks.id'){
            $assigned = $this->ci->tasks_model->get_task_assignees($aRow['task_id']);
            $str = '';
            foreach($assigned as $as){
                $str .= '<a href="' . admin_url('staff/profile/' . $as['assigneeid']) . '">' . staff_profile_image($as['assigneeid'], [
                'staff-profile-image-small'], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => $as['full_name'],
                ]) . '</a>&nbsp';
            }
            $_data = $str;
        }elseif($aColumns[$i] == 'tbltasks.description'){
            $time = $this->ci->tasks_model->get_timesheeets($aRow['task_id']);
            
            foreach($time as $tm){
                if($tm['time_spent'] == NULL){

                   $str = sec2qty(time() - $tm['start_time']);
                  } else {
                   
                   $str =  sec2qty($tm['time_spent']);
                  }
                $sum += $str;
            }
            $_data = $sum;            
            $row['DT_RowClass'] = 'has-row-options';
        }
        elseif($aColumns[$i] == 'tbltasks.is_added_from_contact'){
            $watcher = $this->ci->tasks_model->get_task_watch($aRow['task_id']);
            $str = '';
            foreach($watcher as $wt){
                $str .= '<a href="' . admin_url('staff/profile/' . $wt['staffid']) . '">' . staff_profile_image($wt['staffid'], [
                'staff-profile-image-small'], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => get_staff_full_name($wt['staffid']),
                ]) . '</a>&nbsp';
            }
            $_data = $str;
        }elseif($aColumns[$i] == 'tblprojects.start_date'){
            $_data = _d($aRow['tblprojects.start_date']);
        }elseif($aColumns[$i] == 'tblprojects.deadline'){
            $_data = _d($aRow['tblprojects.deadline']);
        }elseif($aColumns[$i] == 'tblmilestones.due_date'){
            $_data = _d($aRow['tblmilestones.due_date']);
        }elseif($aColumns[$i] == 'tbltasks.startdate'){
            $_data = _d($aRow['tbltasks.startdate']);
        }elseif($aColumns[$i] == 'tbltasks.duedate'){
            if($aRow['tbltasks.duedate'] != ''){
                $_data = _d($aRow['tbltasks.duedate']);
            }else{
                $_data = '';
            }
        }elseif ($aColumns[$i] == 'tblprojects.status') {
            $status = get_project_status_by_id($aRow['tblprojects.status']);
            $_data  = '<span class="label label inline-block project-status-' . $aRow['tblprojects.status'] . '" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '">' . $status['name'] . '</span>';
        }elseif($aColumns[$i] == 'tblprojects.clientid'){
            $this->ci->load->model('leads_model');
            $this->ci->load->model('departments_model');
            if($aRow['project_rel_type'] == 'customer'){
                $_data = '<a href="' . admin_url('clients/client/' . $aRow['tblprojects.clientid']) . '">' . get_company_name($aRow['tblprojects.clientid']) . '</a>';    
            } else if($aRow['project_rel_type'] == 'lead'){
                $lead = $this->ci->leads_model->get($aRow['tblprojects.clientid']);
                $_data = '<a href="' . admin_url('leads/index/' . $aRow['tblprojects.clientid']) . '">' .$lead->name . '</a>';    
            } else if($aRow['project_rel_type'] == 'department'){
                $department = $this->ci->departments_model->get($aRow['tblprojects.clientid']);
                $_data = $department->name;    
            }
        }
        $row[] = $_data;
        
         
    }

    $output['aaData'][] = $row;
}