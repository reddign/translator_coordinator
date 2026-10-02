<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";
require_once __DIR__ . "/../../includes/json_functions.php";

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

if ($method !== "GET" && $method !== "POST") {
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
 
/*
------------------------------------------------------------
POST /api/groups - create a group Scott D 10/1/26
------------------------------------------------------------
NOTE: no real token/session auth exists anywhere in this API
yet (see api/users/index.php TODOs). Matching the app's current
state, userid is sent directly in the body by
web/processes/creategroup.php, taken from the PHP-side session.
------------------------------------------------------------
*/
if ($method === "POST") {
    $data = getJsonBody();
 
    $userId = (int)($data["userid"] ?? 0);
    if ($userId <= 0) {
        sendJson(401, ["success" => false, "message" => "A valid userid is required."]);
    }
 
    $groupName = trim((string)($data["group_name"] ?? ""));
    $groupDescription = trim((string)($data["group_description"] ?? ""));
    $countryIds = is_array($data["countryIds"] ?? null) ? $data["countryIds"] : [];
    $languageIds = is_array($data["languageIds"] ?? null) ? $data["languageIds"] : [];
 
    if ($groupName === "") {
        sendJson(400, ["success" => false, "message" => "Group name is required."]);
    }
 
    WFDatabase::startTransaction();
 
    try {
        $groupId = WFDatabase::executeSQL(
            "INSERT INTO groups (Group_Name, group_description, createdByID, createdOn)
             VALUES (:group_name, :group_description, :created_by, NOW())",
            [
                ":group_name" => $groupName,
                ":group_description" => $groupDescription,
                ":created_by" => $userId
            ],
            true
        );
 
        // Creator becomes the first member, with the "group creator" role (roleId = 1)
        WFDatabase::executeSQL(
            "INSERT INTO group_members (groupid, userid, joinedOn, approved, approvedOn, roleId)
             VALUES (:groupid, :userid, NOW(), 1, NOW(), 1)",
            [":groupid" => $groupId, ":userid" => $userId]
        );
 
        foreach ($countryIds as $countryId) {
            WFDatabase::executeSQL(
                "INSERT INTO groups_to_countries (groupid, countryid) VALUES (:groupid, :countryid)",
                [":groupid" => $groupId, ":countryid" => $countryId]
            );
        }
 
        foreach ($languageIds as $languageId) {
            WFDatabase::executeSQL(
                "INSERT INTO group_to_languages (groupid, languageid) VALUES (:groupid, :languageid)",
                [":groupid" => $groupId, ":languageid" => $languageId]
            );
        }
 
        WFDatabase::commitTransaction();
 
    } catch (Exception $e) {
        WFDatabase::rollbackTransaction();
        sendJson(500, ["success" => false, "message" => "Failed to create group."]);
    }
 
    sendJson(201, [
        "success" => true,
        "message" => "Group created.",
        "data" => ["groupid" => $groupId, "group_name" => $groupName]
    ]);
}
 
