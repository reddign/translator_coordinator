<?php

header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";

$method = $_SERVER["REQUEST_METHOD"];

/*
------------------------------------------------------------
Currencies is a read-only reference resource.
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
Determine whether a CURRENCY_CODE was supplied in the URL.

Examples:
/api/curriences
/api/curriences/1
------------------------------------------------------------
*/

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", trim($path, "/"));
$currencyId = null;

$currenciesIndex = array_search("currencies", $parts);

if (
    $currenciesIndex !== false &&
    isset($parts[$currenciesIndex + 1]) &&
    $parts[$currenciesIndex + 1] !== ""
) {
    $currencyId = $parts[$currenciesIndex + 1];
}

/*
------------------------------------------------------------
GET /api/currencies/{id}
------------------------------------------------------------
*/

if ($currencyId !== null) {
    if (!ctype_alnum($currencyId) ) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Currency Code must be alphanumeric."
        ]);

        exit;
    }

    $sql = "
        SELECT
            CURRENCY_CODE,
            CURRENCY_NAME
        FROM wf_currencies
        WHERE CURRENCY_CODE = :currency_id
    ";

    $params = [
        ":currency_id" => $currencyId
    ];

    $currency = WFDatabase::getDataFromSQL($sql,$params);
    if (!$currency) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Currency not found."
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode([
        "success" => true,
        "data" => $currency
    ]);

    exit;
}

/*
------------------------------------------------------------
GET /api/currencies

Optional query parameter:
?search=english
?country_id=4
------------------------------------------------------------
*/

$search = $_GET["search"] ?? null;
$countryId = $_GET["countryid"] ?? null;

$sql = "
    SELECT
        CURRENCY_CODE,
        CURRENCY_NAME
    FROM wf_currencies
    WHERE 1 = 1
";

$params = [];

if ($search !== null && trim($search) !== "") {
    $search = trim($search);

    $sql .= " AND CURRENCY_NAME LIKE :search";
    $params[":search"] = "%" . $search . "%";
}
if ($countryId !== null && trim($countryId) !== "") {
    $search = trim($search);

    $sql .= " AND CURRENCY_CODE IN (SELECT CURRENCY_CODE FROM 
                                    wf_countries WHERE country_id= :country_id)";
    $params[":country_id"] = $countryId;
}

$sql .= " ORDER BY CURRENCY_NAME";

$currencies = WFDatabase::getDataFromSQL($sql,$params);

http_response_code(200);

echo json_encode([
    "success" => true,
    "count" => count($currencies),
    "data" => $currencies
]);
