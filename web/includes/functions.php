<?PHP

// Base URL for the web folder. Uses $mainURL from config.php,
// so it works both locally and on the live server.
function url(){
    global $mainURL;
    return ($mainURL ?? "") . "/web";
}

function getJSONFromURL($url){
    // Fetch the JSON string from the URL
    $json_data = file_get_contents($url);

    // Check if the request was successful
    if ($json_data === FALSE) {
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

// Calls the API with the logged-in user's token attached
function apiRequest(string $method, string $url, ?array $body = null): ?array {
    $headers = "Accept: application/json\r\n";
    if (!empty($_SESSION['api_token'])) {
        $headers .= "Authorization: Bearer {$_SESSION['api_token']}\r\n";
    }
    if ($body !== null) {
        $headers .= "Content-Type: application/json\r\n";
    }
    $context = stream_context_create(['http' => [
        'method'        => $method,
        'header'        => $headers,
        'content'       => $body !== null ? json_encode($body) : '',
        'ignore_errors' => true,
    ]]);
    $response = file_get_contents($url, false, $context);
    return $response === false ? null : json_decode($response, true);
}

?>