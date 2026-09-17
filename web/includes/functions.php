<?php

function url(){
    $baseFilePath = "/translator_coordinator/web";

    if (isset($_SERVER['HTTPS'])) {
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off")
            ? "https"
            : "http";
    } else {
        $protocol = "http";
    }

    return $protocol . "://" . $_SERVER['HTTP_HOST'] . $baseFilePath;
}

function getJSONFromURL($url){
    // SSL context for local XAMPP development.
    // This allows PHP to connect to the local HTTPS API
    // even when the development certificate is self-signed.
    $context = stream_context_create([
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);

    // Fetch the JSON string from the URL
    $json_data = file_get_contents($url, false, $context);

    // Check if the request was successful
    if ($json_data === false) {
        die("Error: Unable to fetch country data from the API.");
    }

    // Decode the JSON string into an associative PHP array
    $response = json_decode($json_data, true);

    // Verify JSON decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Error decoding JSON: " . json_last_error_msg());
    }

    return $response;
}

?>