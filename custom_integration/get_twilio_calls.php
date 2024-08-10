<?php
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);

$servername = "localhost";
$username = "gps7777_crmuser";
$password = "6;H;K*QIogC&";
$dbname = "gps7777_crm";
define("APIKEY", "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");

require "sendgrid-php.php";

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/ACaa582ff178e9b9c732827995a8c9dade/Calls.json?PageSize=100&StartTime='.date_create(date("Y-m-d"))->modify('0 days')->format('Y-m-d').'&EndTime='.date_create(date("Y-m-d"))->modify('0 days')->format('Y-m-d'),
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
/*
echo "<pre>";
print_r($Array);
echo "</pre>";
*/

foreach($Array['calls'] as $item){  
$audio_file ="";
$audio_file= get_mp3($item['sid']);

    // $audio_file['duration']
    // $audio_file['media_url']
 
 $time_input = strtotime( $item['date_created']); 
$date_input = getDate($time_input);
$date_added= $date_input['year']."-".str_pad($date_input['mon'], 2, "0", STR_PAD_LEFT)."-".str_pad($date_input['mday'], 2, "0", STR_PAD_LEFT)." ".$date_input['hours'].":".$date_input['minutes'].":".$date_input['seconds'];

   // $sql_page2 = "SELECT * FROM `tblleads` where website='".$item['sid']."'";
   // $result_page2 = $conn->query($sql_page2);
   
       $sql_page2 = "SELECT * FROM `twilio_control` where twilio_id='".$item['sid']."'";
    $result_page2 = $conn->query($sql_page2);
    
    
  if(mysqli_num_rows($result_page2)==0){
      
    $sql1 = "INSERT INTO `twilio_control`(twilio_id ) VALUES ('".$item['sid']."')";
    
    $conn->query($sql1);
  
  $array_sip = explode("sip", $item['from']);
  
 
  
if(@$audio_file['media_url']<>"" and $item['sid']<>"" and count($array_sip)<=1 and ($item['direction']=="inbound")){
    //echo $audio_file."----".$item['sid']."<br>";
 
     // $sql1 = "INSERT INTO `tblleads`(name,dateadded,status,source,addedfrom,email,phonenumber,assigned,website ) VALUES ('".$item['sid']."','".$date_added."',2,55,0,'noreply@globalpresence.support','".$item['from']."',1,'".$item['sid']."')";
    
    $conn->query($sql1);
    
    $audio_file_url=$audio_file['media_url'];
  //  $sql_page = "SELECT * FROM `tblleads` where name='".$item['sid']."'";
 //   $result_page = $conn->query($sql_page);
    
  //   if(mysqli_num_rows($result_page)>0){
      //   while($row_page = $result_page->fetch_assoc()) {
              $lead_id=$item['sid'];
        	  //   $sql = "INSERT INTO `tblcustomfieldsvalues`(`relid`, `fieldid`, `fieldto`, `value`) VALUES ('".$row_page['id']."','100','leads','<a href=\"$audio_file_url\" target=\"_blank\">$audio_file_url</a>')";
    
            //     $conn->query($sql);
                 
           //      $sql = "INSERT INTO `tblcustomfieldsvalues`(`relid`, `fieldid`, `fieldto`, `value`) VALUES ('".$row_page['id']."','103','leads','".$audio_file['duration']."')";
    
            //     $conn->query($sql);
    //	} 
    	
    	    	  $subject = "New Twilio Lead - ".$item['from'];
    
    $message = "
    <html>
    <head>
    </head>
    <body>
    <p>
    <p>A new lead is assigned to you.</p>
    <p><b>Phone From:</b> ".$item['from']."</p>
    <p><b>Phone To:</b> ".$item['to']."</p>
    <p><b>Duration Seconds:</b> ".$audio_file['duration']."</p>
    <p><b>Souce:</b> Twilio</p>
    <p><b>Status:</b> New</p>
    <p><b>Assigned:</b> Jack Hakimian</p>
    <p><b>Twilio Mp3:</b> <a href='".$audio_file['media_url']."'>".$audio_file['media_url']."</a></p>
    <p>You can view & update the lead status and notes here: <a href='https://crm.globalpresence.support/admin/leads/index/".$lead_id."'>".$item['from']."</a></p>
    ";
 $email = new \SendGrid\Mail\Mail(); 
$email->setFrom("admin@globalpresence.org", "GP Lead");
$email->setSubject($subject);
$email->addTo("gptwiliocalls@gp.marketing", "Recipient");
$email->addContent("text/html", $message);
$sendgrid = new \SendGrid(APIKEY);
$response = $sendgrid->send($email);
    	
  $data = array(
    'Lead_id' => $lead_id, // Replace with the phone number value
    'Phone From' => $item['from'], // Replace with the phone number value
    'Phone To' => $item['to'], // Replace with the phone number value
    'Duration_Seconds' => $audio_file['duration'], // Replace with the duration seconds value
    'Mp3' => $audio_file['media_url'], // Replace with the MP3 URL value
);

sed_to_zapier($data);  	    	
    	
     }
    

//}
    }

}
function get_mp3($CallSid){
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/ACaa582ff178e9b9c732827995a8c9dade/Recordings.json?CallSid='.$CallSid,
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

$Array_mp3['duration']=@$Array['recordings'][0]['duration'];

if(@$Array['recordings'][0]['duration']>0){
    $Array_mp3['duration']=$Array['recordings'][0]['duration'];
     $Array_mp3['media_url']=$Array['recordings'][0]['media_url'];
    return $Array_mp3;
}else{
    return "";
}
    
    
}

function sed_to_zapier($data){
    // Define the data to be submitted


// Encode the data in JSON format
$json_data = json_encode($data);

// Set the webhook URL
$webhook_url = 'https://hooks.zapier.com/hooks/catch/723538/31xubny/';

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $webhook_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL session
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    // Handle cURL errors here
    echo 'cURL error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);

    
}

?>