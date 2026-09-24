<?php

header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";

$method = $_SERVER["REQUEST_METHOD"];

/*
------------------------------------------------------------
Groups is currently a read-only reference resource.
Only GET requests are supported.

We need to add the rest of the CRUD operations for groups in the future.
We can currently only retrieve (GroupIDs and Group Names) from the database.
We can currently only recieve all the groups or all the groups with a specified GroupID.

- Isaac Widders 9/24/2026
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
