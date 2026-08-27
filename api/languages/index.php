<?php

header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";

$method = $_SERVER["REQUEST_METHOD"];

/*
------------------------------------------------------------
Languages is a read-only reference resource.
Only GET requests are supported.
------------------------------------------------------------
*/

if ($method !== "GET") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);

    exit;
}

/*
------------------------------------------------------------
Determine whether a LANGUAGE_ID was supplied in the URL.

Examples:
/api/languages
/api/languages/1
------------------------------------------------------------
*/

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", trim($path, "/"));
$languageId = null;

$languagesIndex = array_search("languages", $parts);

if (
    $languagesIndex !== false &&
    isset($parts[$languagesIndex + 1]) &&
    $parts[$languagesIndex + 1] !== ""
) {
    $languageId = $parts[$languagesIndex + 1];
}

/*
------------------------------------------------------------
GET /api/languages/{id}
------------------------------------------------------------
*/

if ($languageId !== null) {
    if (!ctype_digit($languageId)) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Language ID must be numeric."
        ]);

        exit;
    }

    $sql = "
        SELECT
            LANGUAGE_ID,
            LANGUAGE_NAME
        FROM wf_languages
        WHERE LANGUAGE_ID = :language_id
    ";

    $params = [
        ":language_id" => $languageId
    ];

    $language = WFDatabase::getDataFromSQL($sql,$params);
    if (!$language) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Language not found."
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode([
        "success" => true,
        "data" => $language
    ]);

    exit;
}

/*
------------------------------------------------------------
GET /api/languages

Optional query parameter:
?search=english
?countryid=14
------------------------------------------------------------
*/

$search = $_GET["search"] ?? null;
$countryId = $_GET["countryid"] ?? null;
$sql = "
    SELECT
        LANGUAGE_ID,
        LANGUAGE_NAME
    FROM wf_languages
    WHERE 1 = 1
";

$params = [];

if ($search !== null && trim($search) !== "") {
    $search = trim($search);

    $sql .= " AND LANGUAGE_NAME LIKE :search";
    $params[":search"] = "%" . $search . "%";
}
if ($countryId !== null && trim($countryId) !== "") {
    $countryId = trim($countryId);

    $sql .= " AND LANGUAGE_ID IN (SELECT language_id 
                                   from wf_spoken_languages 
                                   WHERE country_id = :countryId)";
    $params[":countryId"] = $countryId;
}

$sql .= " ORDER BY LANGUAGE_NAME";

$languages = WFDatabase::getDataFromSQL($sql,$params);

http_response_code(200);

echo json_encode([
    "success" => true,
    "count" => count($languages),
    "data" => $languages
]);
