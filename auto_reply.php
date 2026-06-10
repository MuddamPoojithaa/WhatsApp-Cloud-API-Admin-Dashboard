<?php

function getAutoReply($conn, $message)
{
    $message = strtolower(trim($message));

    $stmt = $conn->prepare("
        SELECT reply_text
        FROM auto_replies
        WHERE LOWER(keyword) = ?
        AND status = 1
        LIMIT 1
    ");

    $stmt->bind_param("s", $message);
    $stmt->execute();

    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        return $row['reply_text'];
    }

    return null;
}