<?php

function sendTemplate(
    $phone,
    $templateName,
    $language,
    $token,
    $phoneNumberId
){

    $phone = preg_replace('/[^0-9]/', '', $phone);

    $url = "https://graph.facebook.com/v25.0/".$phoneNumberId."/messages";

    $data = [
        "messaging_product" => "whatsapp",
        "to" => $phone,
        "type" => "template",
        "template" => [
            "name" => $templateName,
            "language" => [
                "code" => $language
            ],
            "components" => [
                [
                    "type" => "body",
                    "parameters" => [
                        [
                            "type" => "text",
                            "text" => "Customer"
                        ]
                    ]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer ".$token,
        "Content-Type: application/json"
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $error = curl_error($ch);

    curl_close($ch);

    file_put_contents(
        "template_debug.txt",
        date('Y-m-d H:i:s')
        . "\nREQUEST: "
        . json_encode($data)
        . "\nRESPONSE: "
        . $response
        . "\nERROR: "
        . $error
        . "\n\n",
        FILE_APPEND
    );

    return $response;
}