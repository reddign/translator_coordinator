<?php

header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";
require_once __DIR__ . "/../../includes/json_functions.php";
require_once __DIR__ . "/../users/user_functions.php";
$method = $_SERVER['REQUEST_METHOD'];

// Only allow GET method for now.

if ($method !== "GET") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);

    exit;
}

// Determine whether a USER_ID (translator_id) was supplied in the URL.

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", trim($path, "/"));
$translatorId = null;

// Find "translators" in the URL.

$translatorsIndex = array_search("translators", $parts);

if (
    $translatorsIndex !== false &&
    isset($parts[$translatorsIndex + 1]) &&
    $parts[$translatorsIndex + 1] !== ""
) {

    $translatorId = $parts[$translatorsIndex + 1];
}

// GET /api/translators/{id}

if ($translatorId !== null) {

    if (!ctype_digit($translatorId)) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Translator ID must be numeric."
        ]);

        exit;
    }


    $sql = "
        SELECT
            u.userid,
            u.email,
            u.first_name,
            u.last_name,
            DATE_FORMAT(u.date_registered, '%Y-%m-%d') date_registered,
            l.language_name,
            usl.proficency_level,
            c.flag
        FROM users u 
            LEFT OUTER JOIN user_spoken_languages usl ON u.userid = usl.userid
            LEfT OUTER JOIN wf_languages l ON usl.language_id = l.language_id
            LEFT OUTER JOIN wf_countries c ON u.original_country_id = c.country_id
        WHERE u.userid = :user_id
    ";

    $params=[":user_id" => $translatorId];

    $translator = WFDatabase::getDataFromSQL($sql,$params);


    if (!$translator) {

        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Translator not found."
        ]);

        exit;
    }


    http_response_code(200);

    echo json_encode([
        "success" => true,
        "data" => $translator
    ]);

    exit;
}

?>