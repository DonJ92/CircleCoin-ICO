<?php 
# Token send data
$data = array(
    'amount' => 0.01,												//send amount
    'adr' => '0x7C399Ef1B6ce9c8D5618386f790A5E27FE5C25Ec'		//dest amount
);

# Create a connection
$url = 'http://13.250.47.220/';
$ch = curl_init($url);

# Form data string
$postString = http_build_query($data, '', '&');

# Setting our options
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

# Get the response
$response = curl_exec($ch);

echo $response;

curl_close($ch);