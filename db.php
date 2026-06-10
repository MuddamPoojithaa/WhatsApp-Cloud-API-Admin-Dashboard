<?php

$conn = new mysqli(
    "localhost",
    "sumahiai_whatsapp",
    "sumahiai_whatsapp",
    "sumahiai_whatsapp"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");