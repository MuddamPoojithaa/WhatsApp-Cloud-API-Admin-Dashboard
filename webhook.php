<?php

include 'db.php';
include 'config.php';
include 'auto_reply.php';
include 'send_message.php';
include 'send_template.php';

/* ---------------- VERIFY ---------------- */
if (
    isset($_GET['hub_mode']) &&
    $_GET['hub_mode'] === 'subscribe' &&
    isset($_GET['hub_verify_token']) &&
    $_GET['hub_verify_token'] === $VERIFY_TOKEN &&
    isset($_GET['hub_challenge'])
) {
    echo $_GET['hub_challenge'];
    exit;
}

/* ---------------- READ PAYLOAD ---------------- */
$payload = file_get_contents("php://input");
$data = json_decode($payload, true);

/* ---------------- LOG ---------------- */
file_put_contents(
    "webhook_log.txt",
    date("Y-m-d H:i:s") . "\n" . $payload . "\n\n",
    FILE_APPEND
);

/* ---------------- VALIDATION ---------------- */
if (!isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
    http_response_code(200);
    echo "EVENT_RECEIVED";
    exit;
}

$msg = $data['entry'][0]['changes'][0]['value']['messages'][0];

$phone = $msg['from'] ?? '';
$message_id = $msg['id'] ?? '';

$message = '';

/* TEXT MESSAGE */
if (isset($msg['text']['body'])) {
    $message = trim($msg['text']['body']);
}

/* BUTTON CLICK */
if (isset($msg['button']['text'])) {
    $message = trim($msg['button']['text']);
}

if ($phone == '' || $message == '') {
    http_response_code(200);
    echo "EVENT_RECEIVED";
    exit;
}

/* DEBUG */
file_put_contents(
    "button_debug.txt",
    date("Y-m-d H:i:s") .
    " MESSAGE: " .
    $message .
    "\n",
    FILE_APPEND
);

/* ---------------- DUPLICATE CHECK ---------------- */
$stmt = $conn->prepare("
    SELECT id
    FROM messages
    WHERE message_id = ?
");

$stmt->bind_param("s", $message_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    exit;
}

/* ---------------- SAVE CONTACT ---------------- */
$stmt = $conn->prepare("
INSERT INTO contacts (wa_id,last_message_at)
VALUES (?,NOW())
ON DUPLICATE KEY UPDATE
last_message_at = NOW()
");

$stmt->bind_param("s", $phone);
$stmt->execute();

/* ---------------- SAVE INCOMING ---------------- */
$direction = "incoming";

$stmt = $conn->prepare("
INSERT INTO messages
(wa_id,message_id,message,direction,created_at)
VALUES (?,?,?,?,NOW())
");

$stmt->bind_param(
    "ssss",
    $phone,
    $message_id,
    $message,
    $direction
);

$stmt->execute();

/* ---------------- TEMPLATE BUTTONS ---------------- */

$messageLower = strtolower(trim($message));

if (
    $messageLower == "1" ||
    $messageLower == "send services"
) {

    sendTemplate(
        $phone,
        'sumahi_services',
        'en',
        $ACCESS_TOKEN,
        $PHONE_NUMBER_ID
    );

} elseif (
    $messageLower == "2" ||
    $messageLower == "send pricing"
) {

    sendTemplate(
        $phone,
        'sumahi_pricing',
        'en',
        $ACCESS_TOKEN,
        $PHONE_NUMBER_ID
    );

} elseif (
    $messageLower == "3" ||
    $messageLower == "send support"
) {

    sendTemplate(
        $phone,
        'sumahi_support',
        'en',
        $ACCESS_TOKEN,
        $PHONE_NUMBER_ID
    );

} elseif (
    $messageLower == "send follow up"
) {

    sendTemplate(
        $phone,
        'sumahi_followup',
        'en',
        $ACCESS_TOKEN,
        $PHONE_NUMBER_ID
    );

} else {

    /* NORMAL AUTO REPLY */

    $reply = getAutoReply(
        $conn,
        $messageLower
    );

    if (!empty($reply)) {

        sendWhatsApp(
            $phone,
            $reply,
            $ACCESS_TOKEN,
            $PHONE_NUMBER_ID
        );

        $direction = "outgoing";

        $stmt = $conn->prepare("
        INSERT INTO messages
        (wa_id,message,direction,created_at)
        VALUES (?, ?, ?, NOW())
        ");

        $stmt->bind_param(
            "sss",
            $phone,
            $reply,
            $direction
        );

        $stmt->execute();
    }
}

/* ---------------- RESPONSE ---------------- */
http_response_code(200);
echo "EVENT_RECEIVED";
exit;