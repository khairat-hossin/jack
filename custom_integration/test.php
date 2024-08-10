<?php
/*ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);


$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/ACaa582ff178e9b9c732827995a8c9dade/Calls.json?PageSize=100&StartTime='.date_create(date("Y-m-d"))->modify('-1 days')->format('Y-m-d').'&EndTime='.date_create(date("Y-m-d"))->modify('-1 days')->format('Y-m-d'),
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

echo "<pre>";
print_r($Array);
echo "</pre>";*/



?>