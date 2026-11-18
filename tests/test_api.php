<?php
/***** 
** This file executes raw PHP without dependancies to create tests.
** Future Work: We could use a PHP Framework like PHPUnit to handle unit testing.
*/
$baseURL = "http://localhost/translator_coordinator/api";

$passed = 0;
$failed = 0;

function testAPI($name, $endpoint, $expectedStatus, $method = "GET", $data = null)
{
    global $baseURL, $passed, $failed;

    $options = [
        "http" => [
            "method" => $method,
            "header" => "Content-Type: application/json\r\n",
            "ignore_errors" => true
        ]
    ];

    if ($data !== null) {
        $options["http"]["content"] = json_encode($data);
    }

    $context = stream_context_create($options);

    $response = @file_get_contents(
        $baseURL . $endpoint,
        false,
        $context
    );

    $statusLine = $http_response_header[0] ?? "";
    preg_match('/HTTP\/\S+\s+(\d{3})/', $statusLine, $matches);
    $status = (int)($matches[1] ?? 0);

    $json = json_decode($response === false ? "" : $response, true);

    $success = $status === $expectedStatus;

    if ($success && $expectedStatus === 200) {
        $success = isset($json["success"]) &&
                   $json["success"] === true &&
                   isset($json["data"]);
    }

    if ($success && $expectedStatus >= 400) {
        $success = isset($json["success"]) &&
                   $json["success"] === false;
    }

    if ($success) {
        echo "<font color=green>[PASS]</font> $name<BR>";
        $passed++;
    } else {
        echo "<font color=red>[FAIL]</font> $name (Expected $expectedStatus, got $status)<BR>";
        $failed++;
    }
}

// TEST 1: Get countries
testAPI(
    "Retrieve Countries",
    "/countries",
    200
);

// TEST 2: Country not found
testAPI(
    "Country Not Found",
    "/countries/999999999",
    404
);

// TEST 3: Invalid registration
testAPI(
    "Invalid Email Registration",
    "/users/register",
    400,
    "POST",
    [
        "first_name" => "Test",
        "last_name" => "Student",
        "email" => "invalid-email",
        "password" => "TestPassword123!",
        "original_country_id" => 1
    ]
);

echo "\nTests Passed: $passed\n";
echo "Tests Failed: $failed\n";

exit($failed > 0 ? 1 : 0);
?>
