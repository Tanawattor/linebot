<?php


$access_token = 'YgJq8YNtqNolVGTd06CAElZkHL7rMB8qbnl89TSmzQhTUlEp05fJ2LW7lhuFNKnjlIIrUbHMqPNsJIUKWGtA4SVx08X8AiZPPygA44Z4M8v2iQIuWiuI+wVQn4vbJIZixg8hiJt28F8ZPrQxb1OurAdB04t89/1O/w1cDnyilFU=';

$userId = 'U20c9983890ba0878069436c0e3175838';

$url = 'https://api.line.me/v2/bot/profile/'.$userId;

$headers = array('Authorization: Bearer ' . $access_token);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
curl_close($ch);

echo $result;

