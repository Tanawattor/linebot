<?php



require "vendor/autoload.php";

$access_token = 'JH8N92HApXGWLDSm0HDl88s+COUEGtN42fW4uqluvqJ/r4pp1Dpg7eODVelS/1h7lIIrUbHMqPNsJIUKWGtA4SVx08X8AiZPPygA44Z4M8uFLEJohH8dXrCaPpv+/31WvyE5tfNcWY9VqHFXJN3gQwdB04t89/1O/w1cDnyilFU=';

$channelSecret = 'd475125d68c2aab8159a9d526c49f0ea';

$pushID = 'U20c9983890ba0878069436c0e3175838';

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);

$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello world');
$response = $bot->pushMessage($pushID, $textMessageBuilder);

echo $response->getHTTPStatus() . ' ' . $response->getRawBody();







