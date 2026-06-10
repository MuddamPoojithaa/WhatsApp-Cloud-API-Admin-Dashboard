<?php

$token = "EAFZCry80dyl0BRtvk4Pdyum2LwdPZBlBAWKKimQ5ARUAuIEQhXMK7wFd0Tz8Gs8cjCeyDtZCuN1IFIvJPZBhzX1ZBRIlS718ZAMWuIV5sJSqRSjct3kb8a7SvgZBmLeV5fITeyPbjEHZCZCZCL53I2oC6p6EZA6BhE9WryeYMq7S9pRGt0Kt3vsz4kMwxrWc3ZAgODkDD06kVu2TpnfQcQWl5ZCTZAnWCvN5jnUJKf5E1U4HwOA9kr88I9WkRe7FHx4pqmFZBl0x6rzSwozA6MXEZA9I3dakUkk2";
$url = "https://graph.facebook.com/v25.0/me";

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

echo $response;