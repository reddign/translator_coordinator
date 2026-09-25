<?PHP
require_once __DIR__ . '/../../includes/config.php';
// include "includes/header.php";
// include "includes/navbar.php";
// include "includes/footer.php";

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

function searchTranslators($userName = null, $languageName = null, $countryName = null, $regionName = null, $group = null) {
    $sql = "
        SELECT
            userid,
            CONCAT(first_name, '',last_name) AS name
        FROM users
        WHERE 1=1
    ";
    if (!empty($userName)) {
        $safeUserName = addslashes($userName);

        $sql .= "
        AND CONCAT(first_name, ' ', last_name)
        LIKE '%$safeUserName%'
        ";
    }
    return WFDatabase::getDataFromSQL($sql);

}


?>


?>