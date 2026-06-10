<?php

include 'config.php';
include 'send_message.php';

echo sendWhatsApp(
    "918074251396",
    "Test Message From Sumahi AI",
    $ACCESS_TOKEN,
    $PHONE_NUMBER_ID
);