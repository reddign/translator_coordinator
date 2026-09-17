<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";


$method = $_SERVER["REQUEST_METHOD"];

/*
------------------------------------------------------------
GET /api/locations
------------------------------------------------------------
*/

$sql = "
    SELECT REGION_ID AS ID, REGION_NAME AS NAME
    FROM wf_world_regions
    UNION ALL
    SELECT COUNTRY_ID AS ID, COUNTRY_NAME AS NAME
    FROM wf_countries
    ORDER BY NAME;
";

$params = [];

$regions = WFDatabase::getDataFromSQL($sql,$params);
http_response_code(200);

echo json_encode([
    "success" => true,
    "count" => count($regions),
    "data" => $regions
]);
