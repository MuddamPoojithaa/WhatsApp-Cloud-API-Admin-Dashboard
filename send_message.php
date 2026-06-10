<?php

/*
----------------------------------
WHATSAPP SEND MESSAGE FUNCTION
----------------------------------
*/

function sendWhatsApp(
    $phone,
    $message,
    $token,
    $phoneNumberId
) {
    $url = "https://graph.facebook.com/v25.0/" . $phoneNumberId . "/messages";

    $data = [
        "messaging_product" => "whatsapp",
        "to" => $phone,
        "type" => "text",
        "text" => [
            "body" => $message
        ]
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $token,
        "Content-Type: application/json"
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);

    /*
    ----------------------------------
    ERROR CHECK (OPTIONAL BUT USEFUL)
    ----------------------------------
    */
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);

        return json_encode([
            "error" => true,
            "message" => $error
        ]);
    }

    curl_close($ch);

    return $response;
}