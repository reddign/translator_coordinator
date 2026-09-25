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
        SELECT DISTINCT
            u.userid,
            CONCAT(u.first_name, ' ', u.last_name) AS name,
            l.LANGUAGE_NAME AS language
        FROM users u

        LEFT JOIN user_spoken_languages usl
            ON u.userid = usl.userid

        LEFT JOIN wf_languages l
            ON usl.language_id = l.LANGUAGE_ID

        WHERE 1=1
    ";

    if (!empty($userName)) {
        $safeUserName = addslashes($userName);

        $sql .= "
            AND CONCAT(u.first_name, ' ', u.last_name)
            LIKE '%$safeUserName%'
        ";
    }

    if (!empty($languageName)) {
        $languageId = (int)$languageName;
    
        $sql .= "
            AND l.LANGUAGE_ID = $languageId
        ";
    }

    return WFDatabase::getDataFromSQL($sql);
}


?>


?>