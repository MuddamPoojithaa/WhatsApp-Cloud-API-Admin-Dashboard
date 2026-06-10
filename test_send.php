<?php

include 'config.php';

$url = "https://graph.facebook.com/v20.0/$PHONE_NUMBER_ID/messages";

$data = [
    "messaging_product" => "whatsapp",
    "to" => "919014331089",
    "type" => "text",
    "text" => [
        "body" => "Hello! Test message from system"
    ]
];

$headers = [
    "Authorization: Bearer $ACCESS_TOKEN",
    "Content-Type: application/json"
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo $response;