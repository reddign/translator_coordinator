<?php

header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";
$method = $_SERVER["REQUEST_METHOD"];

/*
------------------------------------------------------------
Only GET is supported because countries is a reference table.
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
Determine whether a COUNTRY_ID was supplied in the URL.

Examples:

/api/countries
/api/countries/25
------------------------------------------------------------
*/

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", trim($path, "/"));
$countryId = null;


/*
Find "countries" in the URL.

Example:

api / countries / 25
      ^
*/

$countriesIndex = array_search("countries", $parts);

if (
    $countriesIndex !== false &&
    isset($parts[$countriesIndex + 1]) &&
    $parts[$countriesIndex + 1] !== ""
) {

    $countryId = $parts[$countriesIndex + 1];
}


/*
------------------------------------------------------------
GET /api/countries/{id}
------------------------------------------------------------
*/

if ($countryId !== null) {

    if (!ctype_digit($countryId)) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Country ID must be numeric."
        ]);

        exit;
    }


    $sql = "
        SELECT
            COUNTRY_ID,
            REGION_ID,
            COUNTRY_NAME,
            COUNTRY_TRANSLATED_NAME,
            LOCATION,
            CAPITOL,
            AREA,
            COASTLINE,
            LOWEST_ELEVATION,
            LOWEST_ELEV_NAME,
            HIGHEST_ELEVATION,
            HIGHEST_ELEV_NAME,
            DATE_OF_INDEPENDENCE,
            NATIONAL_HOLIDAY_NAME,
            NATIONAL_HOLIDAY_DATE,
            POPULATION,
            POPULATION_GROWTH_RATE,
            LIFE_EXPECT_AT_BIRTH,
            MEDIAN_AGE,
            AIRPORTS,
            CLIMATE,
            FIPS_ID,
            INTERNET_EXTENSION,
            CURRENCY_CODE,
            FLAG
        FROM wf_countries
        WHERE COUNTRY_ID = :country_id
    ";

    $params=[":country_id" => $countryId];

    $country = WFDatabase::getDataFromSQL($sql,$params);


    if (!$country) {

        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Country not found."
        ]);

        exit;
    }


    http_response_code(200);

    echo json_encode([
        "success" => true,
        "data" => $country
    ]);

    exit;
}


/*
------------------------------------------------------------
GET /api/countries

Optional query parameters:

?regionid=3
?search=united
?regionid=3&search=united
------------------------------------------------------------
*/

$regionId = $_GET["regionid"] ?? null;
$search = $_GET["search"] ?? null;


$sql = "
    SELECT
        COUNTRY_ID,
        REGION_ID,
        COUNTRY_NAME,
        COUNTRY_TRANSLATED_NAME,
        LOCATION,
        CAPITOL,
        POPULATION,
        FIPS_ID,
        INTERNET_EXTENSION,
        CURRENCY_CODE,
        FLAG
    FROM wf_countries
    WHERE 1 = 1
";


$params = [];


/*
------------------------------------------------------------
Filter by REGION_ID
------------------------------------------------------------
*/

if ($regionId !== null && $regionId !== "") {

    if (!ctype_digit($regionId)) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "regionid must be numeric."
        ]);

        exit;
    }


    $sql .= " AND REGION_ID = :region_id";

    $params[":region_id"] = $regionId;
}


/*
------------------------------------------------------------
Search by country name.

Searches both the standard name and translated name.
------------------------------------------------------------
*/

if ($search !== null && trim($search) !== "") {

    $search = trim($search);

    $sql .= "
        AND (
            COUNTRY_NAME LIKE :search
            OR COUNTRY_TRANSLATED_NAME LIKE :search
        )
    ";

    $params[":search"] = "%" . $search . "%";
}


/*
------------------------------------------------------------
Sort results alphabetically.
------------------------------------------------------------
*/

$sql .= " ORDER BY COUNTRY_NAME";


$countries = WFDatabase::getDataFromSQL($sql,$params);



http_response_code(200);

echo json_encode([
    "success" => true,
    "count" => count($countries),
    "data" => $countries
]);