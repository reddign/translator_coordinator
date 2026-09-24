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

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", trim($path, "/"));
$groupID = null;

$groupsIndex = array_search("groups", $parts);

if (
    $groupsIndex !== false &&
    isset($parts[$groupsIndex + 1]) &&
    $parts[$groupsIndex + 1] !== ""
) {
    $groupID = $parts[$groupsIndex + 1];
} 

/*
------------------------------------------------------------
GET /api/languages/{id}
------------------------------------------------------------
*/

if ($groupID !== null) {
    if (!ctype_digit($groupID)) { 
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Group ID must be numeric."
        ]);

        exit;
    }

    $sql = "
        SELECT
            GROUPID,
            Group_Name
        FROM groups
        WHERE GROUPID = :group_id 
    ";

    $params = [
        ":group_id" => $groupID 
    ];

    $group = WFDatabase::getDataFromSQL($sql,$params);
    if (!$group) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Group not found."
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode([
        "success" => true,
        "data" => $group
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

// $search = $_GET["search"] ?? null;
// $countryId = $_GET["countryid"] ?? null;
$sql = "
    SELECT
        GROUPID,
        Group_Name
    FROM groups
    WHERE 1 = 1
";

// $params = [];

// if ($search !== null && trim($search) !== "") {
//     $search = trim($search);

//     $sql .= " AND LANGUAGE_NAME LIKE :search";
//     $params[":search"] = "%" . $search . "%";
// }
// if ($countryId !== null && trim($countryId) !== "") {
//     $countryId = trim($countryId);

//     $sql .= " AND LANGUAGE_ID IN (SELECT language_id 
//                                    from wf_spoken_languages 
//                                    WHERE country_id = :countryId)";
//     $params[":countryId"] = $countryId;
// }

// $sql .= " ORDER BY LANGUAGE_NAME";

$languages = WFDatabase::getDataFromSQL($sql,$params);

http_response_code(200);

echo json_encode([
    "success" => true,
    "count" => count($languages),
    "data" => $languages
]);
