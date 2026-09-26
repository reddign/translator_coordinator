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

function searchTranslators(array $filters): array
{
    $sql = "
        SELECT DISTINCT
            u.userid,
            CONCAT(u.first_name, ' ', u.last_name) AS full_name,
            COALESCE(l.LANGUAGE_NAME, 'N/A')       AS language_name,
            COALESCE(c.COUNTRY_NAME, 'N/A')        AS country_name,
            COALESCE(r.REGION_NAME, 'N/A')         AS region_name,
            COALESCE(g.group_name, 'N/A')          AS group_name
        FROM users u
        LEFT JOIN wf_countries c           ON c.COUNTRY_ID = u.original_country_id
        LEFT JOIN wf_world_regions r       ON r.REGION_ID = c.REGION_ID
        LEFT JOIN user_spoken_languages usl ON usl.userid = u.userid
        LEFT JOIN wf_languages l           ON l.LANGUAGE_ID = usl.language_id
        LEFT JOIN group_members gm         ON gm.userid = u.userid
        LEFT JOIN `groups` g               ON g.group_id = gm.group_id
        WHERE 1=1
    ";

    $params = [];

    if (!empty($filters['user'])) {$sql .= " AND (u.first_name LIKE :user OR u.last_name LIKE :user OR CONCAT(u.first_name, ' ', u.last_name) LIKE :user)";
        $params[':user'] = '\%' . trim($filters['user']) . '%';
    }
    if (!empty($filters['language'])) {$sql .= " AND l.LANGUAGE_ID = :language";
        $params[':language'] =$filters['language'];
    }
    if (!empty($filters['country'])) {$sql .= " AND c.COUNTRY_ID = :country";
        $params[':country'] =$filters['country'];
    }
    if (!empty($filters['region'])) {$sql .= " AND r.REGION_ID = :region";
        $params[':region'] =$filters['region'];
    }
    if (!empty($filters['group'])) {$sql .= " AND g.group_id = :group";
        $params[':group'] =$filters['group'];
    }

    $sql .= " ORDER BY full_name ASC";

    $results = WFDatabase::getDataFromSQL($sql,$params);
    return is_array($results) ?$results : [];
}


?>


?>