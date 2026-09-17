<?PHP

function url(){
    $baseFilePath="/translator_coordinator/web";
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'] .  $baseFilePath;
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


$regionDataURL = $mainURL."/api/regions/{$regionid}";
$countryDataURL = $mainURL."/api/countries?regionid={$regionid}";
$languageDataURL = $mainURL."/api/languages/{$search}";

$countries = $countryResponse["data"];
$regionName = $regionResponse["data"][0]["REGION_NAME"];
$languages = $languageResponse["data"];

// Stub function that returns placeholder data for demo.
function searchTranslators($language = null, $country = null, $region = null, $group = null) {
    return [
        [
            'name'     => $languages,
            'language' => $languages,
            'country'  => $languages
        ]
    ];
}

?>