<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once __DIR__ . "/../../../includes/config.php";

/*
------------------------------------------------------------
Call GET /api/languages
------------------------------------------------------------
Same pattern as getCountries.php — plain GET, no auth needed
for public reference data.
*/
$url = $mainURL . "/api/languages";

$options = [
    "http" => [
        "method" => "GET",
        "header" => "Accept: application/json\r\n",
        "ignore_errors" => true
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

header('Content-Type: application/json');

if ($response === false) {
    http_response_code(502);
    echo json_encode(["success" => false, "message" => "Unable to reach languages service."]);
    exit;
}

echo $response;
exit;