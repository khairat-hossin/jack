<?php
defined('BASEPATH') or exit('No direct script access allowed');
$CI          = & get_instance();

//////////////////////////////////////////////////////////////////////////////////////////////START
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/ACaa582ff178e9b9c732827995a8c9dade/Calls.json?PageSize=100',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic QUNhYTU4MmZmMTc4ZTliOWM3MzI4Mjc5OTVhOGM5ZGFkZTpjNmFiZWFmZjFhMDZhMzAxMTJhMDAxNjYyNTZjMzc2Ng=='
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$Array = json_decode($response, true);
//////////////////////////////////////////////////////////////////////////////////////////////END
//$CI->db->query("SET sql_mode = ''");
$aColumns = [
    db_prefix() . 'call_logs.call_purpose',
    db_prefix() . 'call_logs.staffid',
    db_prefix() . 'call_logs_voice_records.file_path as record_path',
    db_prefix() . 'call_logs_voice_records.id as record_id',
    db_prefix() . 'call_logs.clientid as clientid',
    db_prefix() . 'call_logs_voice_records.create_date as create_date'
    
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'call_logs_voice_records';
$where        = [];
// Add blank where all filter can be stored
$filter = [];
$join = [
    'JOIN ' . db_prefix() . 'call_logs ON ' . db_prefix() . 'call_logs.id = ' . db_prefix() . 'call_logs_voice_records.call_log_id',
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [ db_prefix() . 'call_logs.id as call_log_id', db_prefix() . 'call_logs.staffid as staff_id', db_prefix() . 'call_logs.call_purpose as call_purpose', db_prefix() . 'call_logs.is_completed as is_completed',db_prefix() . 'call_logs.userphone as phone',db_prefix() . 'call_logs.customer_type as customer_type' ]);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach($Array['calls'] as $item){
    if($item['from_formatted']<>"support_agent" and $item['to_formatted']<>"support_agent"  ){
foreach ($rResult as $aRow) {
    $call_array ="";
 $call_array = get_call_info($item['parent_call_sid']);
  if($item['parent_call_sid']<>null and $call_array<>"" ){
    $row = [];
  $_data = ' ';
    // Call Log Purpose
  /*  $hrefAttr = 'href="' . admin_url('call_logs/call_log/index/' . $aRow['call_log_id']) . '" onclick="init_call_log_modal(' . $aRow['call_log_id'] . ');return false;"';

    $_data = '<a href="' . admin_url('call_logs/preview/' . $aRow['call_log_id']) . '" >' . $aRow['call_purpose'] . '</a>';
   $_data .= '<div class="row-options">';
    $_data .= '<a ' . $hrefAttr . '>' . _l('view') . '</a>';*/

    if($aRow['is_completed'] == 0){
    //    $_data .= ' | <a href="' . admin_url('call_logs/call_log/' . $aRow['call_log_id']) . '">' . _l('edit') . '</a>';
    }

    if (has_permission('call_logs', '', 'delete')) {
   //     $_data .= ' | <a href="' . admin_url('call_logs/delete/' . $aRow['call_log_id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }
   // $_data .= '</div>';
       
    $row[] = $_data;


if($item['from']==''){
    $item['from'] = 'NO VAL FROM TWILIO';
}
if($item['to']==''){
    $item['to'] = 'NO VAL FROM TWILIO';
}
$query = $CI->db->query("SELECT * FROM gps7777_crm.tblleads where Substring(replace(replace(replace(replace(replace(replace(replace(phonenumber, '-', ''), ' ', ''),' ',''),',',''),'(',''),')',''),'.',''),-10) ='".substr($item['from'],-10)."'");
$data_info = $query->row();

if($data_info->id<>''){
    
    
}else{
$query = $CI->db->query("SELECT * FROM gps7777_crm.tblleads where Substring(replace(replace(replace(replace(replace(replace(replace(phonenumber, '-', ''), ' ', ''),' ',''),',',''),'(',''),')',''),'.',''),-10) ='".substr($item['to'],-10)."'");
$data_info = $query->row();
    
}

$query2 = $CI->db->query("SELECT * FROM gps7777_crm.tblcustomfieldsvalues where Substring(replace(replace(replace(replace(replace(replace(replace(value, '-', ''), ' ', ''),' ',''),',',''),'(',''),')',''),'.',''),-10) ='".substr($item['from'],-10)."'");
$data_info2 = $query2->row();

if($data_info2->id<>''){
    
}else{
$query2 = $CI->db->query("SELECT * FROM gps7777_crm.tblcustomfieldsvalues where Substring(replace(replace(replace(replace(replace(replace(replace(value, '-', ''), ' ', ''),' ',''),',',''),'(',''),')',''),'.',''),-10) ='".substr($item['to'],-10)."'");
$data_info2 = $query2->row();
}
    // Caller 
    $oStaff = $this->ci->staff_model->get($data_info2->relid);
    $_data =  staff_profile_image($oStaff->staffid, array('img', 'img-responsive', 'staff-profile-image-small', 'pull-left')). '<a href="'.admin_url('profile/'.$oStaff->staffid).'">'.$oStaff->firstname.' '. $oStaff->lastname. '</a><br>';

    $row[] = $_data;

    $_data = "";
   // if($aRow['customer_type'] == 'lead'){
        $CI = & get_instance();
        $CI->load->model('leads_model');
        $oClient =  $CI->leads_model->get($data_info->id);
        $contactName = '';

        if(isset($oClient)){
            $pic ='';// '<img src="'.contact_profile_image_url($oCustomer[0]['id']).'" class="img img-responsive staff-profile-image-small pull-left">';

            $contactName = $pic.'<a href="'.admin_url('leads/index/'.$oClient->id).'">'.$oClient->name.'</a><br>';
            $_data = $contactName.$oClient->company;
        }
        

  /*  }else{
        $oClient = $this->ci->clients_model->get($data_info->id);
        if(!empty($oClient)){
           $oCustomer = $this->ci->clients_model->get_contacts($oClient->userid, ['is_primary' => true]);
            $contactName = '';
            if(isset($oCustomer[0])){
                $pic = '<img src="'.contact_profile_image_url($oCustomer[0]['id']).'" class="img img-responsive staff-profile-image-small pull-left">';
                $contactName = $pic.'<br><a href="'.admin_url('clients/client/'.$data_info->id.'?group=contacts&contactid='.$oCustomer[0]['id']).'" data-id="'.$oCustomer[0]['id'].'">'.$oCustomer[0]['firstname']. ' '.  $oCustomer[0]['lastname'].'</a><br>';
            }
            $_data = $contactName.$oClient->company; 
        }
        
  //  }*/

    $row[] = $_data;
    
   

    $row[] =$item['from'].'/'.$item['to'];

    $row[] =$item['date_created'];

   // $row[] = '<div style = "display:inline-flex;"><audio src="'.base_url('uploads/call_logs/'.$aRow['call_log_id'].'/'.$aRow['record_path']).'" controls="controls"></audio>  <a href="'.admin_url('call_logs/download_record/'.$aRow['record_id']).'" style = "padding-top:20px;padding-left:20px;" class="text-info ">Download</a> <a href="'.admin_url('call_logs/delete_record/'.$aRow['record_id']).'" style = "padding-top:20px;padding-left:20px;" class="text-danger _delete task-delete">Delete</a></div>';
  //  $row[] = '<div style = "display:inline-flex;"><audio src="'.$aRow['record_path'].'" controls="controls"></audio>  <a href="'.admin_url('call_logs/download_record/'.$aRow['record_id']).'" style = "padding-top:20px;padding-left:20px;" class="text-info ">Download</a> <a href="'.admin_url('call_logs/delete_record/'.$aRow['record_id']).'" style = "padding-top:20px;padding-left:20px;" class="text-danger _delete task-delete">Delete</a></div>';

 $row[] = '<div style = "display:inline-flex;"><audio src="'.$call_array.'" controls="controls"></audio> </div>';
 

    

    //ob_start();
    ?>

    <?php
    // $progress = ob_get_contents();
    // ob_end_clean();
    // $row[]              = $progress;
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
}
}
}

function get_call_info($call){

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/ACaa582ff178e9b9c732827995a8c9dade/Recordings.json?CallSid='.$call,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic QUNhYTU4MmZmMTc4ZTliOWM3MzI4Mjc5OTVhOGM5ZGFkZTpjNmFiZWFmZjFhMDZhMzAxMTJhMDAxNjYyNTZjMzc2Ng=='
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$Array = json_decode($response, true);


if($Array['recordings'][0]['duration']>0){
    return $Array['recordings'][0]['media_url'];
}else{
    return "";
}
    
}