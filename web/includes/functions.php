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

$id = $_GET['id'] ?? null;

$countryDataURL  = "{$mainURL}/api/countries/{$id}";
$currencyDataURL = "{$mainURL}/api/currencies?countryid={$id}";
$languageDataURL = "{$mainURL}/api/languages?countryid={$id}";

$countryResponse  = getJSONFromURL("{$mainURL}/api/countries/{$id}");
$currencyResponse = getJSONFromURL("{$mainURL}/api/currencies?countryid={$id}");
$languageResponse = getJSONFromURL("{$mainURL}/api/languages?countryid={$id}");

$countries  = $countryResponse['data'] ?? [];
$currencies = $currencyResponse['data'] ?? [];
$languages  = $languageResponse['data'] ?? [];

$countryName = $countries[0]['COUNTRY_NAME'] ?? '';
$countryId   = $countries[0]['COUNTRY_ID'] ?? $id;
$languageId   = $languages[0]['LANGUAGE_ID'] ?? $id;



function searchTranslators($user = null, $language = null, $country = null, $region = null, $group = null): array
{
    $sql = "
        SELECT 
            u.userid,
            CONCAT(u.first_name, ' ', u.last_name) AS name,
            COALESCE(c.COUNTRY_NAME, 'N/A') AS country,
            COALESCE(r.REGION_NAME, 'N/A')  AS region_name,
            COALESCE(GROUP_CONCAT(DISTINCT l.LANGUAGE_NAME SEPARATOR ', '), 'None') AS language,
            COALESCE(GROUP_CONCAT(DISTINCT g.group_name SEPARATOR ', '), 'None')    AS group_name
        FROM users u
        LEFT JOIN wf_countries c            ON c.COUNTRY_ID = u.original_country_id
        LEFT JOIN wf_world_regions r        ON r.REGION_ID = c.REGION_ID
        LEFT JOIN user_spoken_languages usl ON usl.userid = u.userid
        LEFT JOIN wf_languages l            ON l.LANGUAGE_ID = usl.language_id
        LEFT JOIN group_members gm          ON gm.userid = u.userid
        LEFT JOIN `groups` g                ON g.groupid = gm.groupid
        WHERE 1=1
    ";

    $params = [];

    if (!empty($user)) {
        $sql .= " AND (u.first_name LIKE :user OR u.last_name LIKE :user OR CONCAT(u.first_name, ' ', u.last_name) LIKE :user)";
        $params[':user'] = '%' . trim($user) . '%';
    }
    if (!empty($language)) {
        $sql .= " AND l.LANGUAGE_ID = :language";
        $params[':language'] = $language;
    }
    if (!empty($country)) {
        $sql .= " AND c.COUNTRY_ID = :country";
        $params[':country'] = $country;
    }
    if (!empty($region)) {
        $sql .= " AND r.REGION_ID = :region";
        $params[':region'] = $region;
    }
    if (!empty($group)) {
        $sql .= " AND g.groupid = :group";
        $params[':group'] = $group;
    }

    $sql .= " GROUP BY u.userid, u.first_name, u.last_name, c.COUNTRY_NAME, r.REGION_NAME ORDER BY name ASC";

    return WFDatabase::getDataFromSQL($sql, $params);
}


?>