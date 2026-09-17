<?PHP

include "includes/header.php";
include "includes/navbar.php";

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

$id = $_GET['id'] ?? null;
$mainURL = $mainURL ?? 'http://localhost/translator_coordinator';

$countryDataURL  = "{$mainURL}/api/countries/{$id}";
$currencyDataURL = "{$mainURL}/api/currencies?countryid={$id}";
$languageDataURL = "{$mainURL}/api/languages?countryid={$id}";

if ($id) {
    $countryResponse  = getJSONFromURL("{$mainURL}/api/countries/{$id}");
    $currencyResponse = getJSONFromURL("{$mainURL}/api/currencies?countryid={$id}");
    $languageResponse = getJSONFromURL("{$mainURL}/api/languages?countryid={$id}");
} else {
    $countryResponse = $currencyResponse = $languageResponse = ['data' => []];
}

$countries  = $countryResponse['data'] ?? [];
$currencies = $currencyResponse['data'] ?? [];
$languages  = $languageResponse['data'] ?? [];

$countryName = $countries[0]['COUNTRY_NAME'] ?? '';
$countryId   = $countries[0]['COUNTRY_ID'] ?? $id;


function searchTranslators($languageName = null, $countryName = null, $regionName = null, $group = null) {
    return [
        [
            'name'     => 'Placeholder Name',
            'language' => $languageName,
            'country'  => $regionName
        ]
    ];
}

?>