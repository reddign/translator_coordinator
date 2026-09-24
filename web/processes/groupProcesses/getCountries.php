<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once __DIR__ . "/../../../includes/config.php";

/*
------------------------------------------------------------
Call GET /api/countries
------------------------------------------------------------
This is read-only reference data, so no session/auth needed —
just a plain GET request. If GET /api/countries turns out to
require an auth token, add an Authorization header the same
way login.php's POST context does.
*/
$url = $mainURL . "/api/countries";

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
    echo json_encode(["success" => false, "message" => "Unable to reach countries service."]);
    exit;
}

// Pass the API's response straight through to the frontend.
// Confirmed shape (from api/languages/index.php, same author
// likely wrote api/countries the same way):
//   { "success": true, "count": N, "data": [ {COUNTRY_ID, COUNTRY_NAME}, ... ] }
// If api/countries/index.php uses different column names, this
// still works unchanged — only the JS in createGroupForm.php
// would need its idField/nameField arguments adjusted.
echo $response;
exit;