<?php
$access_token = 'JH8N92HApXGWLDSm0HDl88s+COUEGtN42fW4uqluvqJ/r4pp1Dpg7eODVelS/1h7lIIrUbHMqPNsJIUKWGtA4SVx08X8AiZPPygA44Z4M8uFLEJohH8dXrCaPpv+/31WvyE5tfNcWY9VqHFXJN3gQwdB04t89/1O/w1cDnyilFU=';


$url = 'https://api.line.me/v1/oauth/verify';

$headers = array('Authorization: Bearer ' . $access_token);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
curl_close($ch);

echo $result;