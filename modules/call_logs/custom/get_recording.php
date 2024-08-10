<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/ACaa582ff178e9b9c732827995a8c9dade/Recordings.json?CallSid=CA987159354f000c2cf98c327855e3ae15',
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
echo "</pre>";

/*
foreach($Array['recordings'] as $item){
    
    echo $item['media_url'].$item['duration']."<br>";
    
}*/
?>