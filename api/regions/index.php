<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";


$method = $_SERVER["REQUEST_METHOD"];

/*
------------------------------------------------------------
Regions is a read-only reference resource.
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
Determine whether a REGION_ID was supplied in the URL.

Examples:
/api/regions
/api/regions/3
------------------------------------------------------------
*/

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", trim($path, "/"));
$regionId = null;

$regionsIndex = array_search("regions", $parts);

if (
    $regionsIndex !== false &&
    isset($parts[$regionsIndex + 1]) &&
    $parts[$regionsIndex + 1] !== ""
) {
    $regionId = $parts[$regionsIndex + 1];
}

/*
------------------------------------------------------------
GET /api/regions/{id}
------------------------------------------------------------
*/

if ($regionId !== null) {
    if (!ctype_digit($regionId)) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Region ID must be numeric."
        ]);

        exit;
    }

    $sql = "
        SELECT
            REGION_ID,
            REGION_NAME
        FROM wf_world_regions
        WHERE REGION_ID = :region_id
    ";

    $params = [":region_id" => $regionId];
    $region = WFDatabase::getDataFromSQL($sql,$params);

    if (!$region) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Region not found."
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode([
        "success" => true,
        "data" => $region
    ]);

    exit;
}

/*
------------------------------------------------------------
GET /api/regions

Optional query parameter:
?search=asia
------------------------------------------------------------
*/

$search = $_GET["search"] ?? null;

$sql = "
    SELECT
        REGION_ID,
        REGION_NAME
    FROM wf_world_regions
    WHERE 1 = 1
";

$params = [];

if ($search !== null && trim($search) !== "") {
    $search = trim($search);

    $sql .= " AND REGION_NAME LIKE :search";
    $params[":search"] = "%" . $search . "%";
}

$sql .= " ORDER BY REGION_NAME";

$regions = WFDatabase::getDataFromSQL($sql,$params);
http_response_code(200);

echo json_encode([
    "success" => true,
    "count" => count($regions),
    "data" => $regions
]);
