<?PHP

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require_once __DIR__ . "/../../../includes/config.php";

/*
------------------------------------------------------------
Make sure the page was accessed using POST.
------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../forms/createGroupForm.php");
    exit;
}

/*
------------------------------------------------------------
Must be logged in to create a group.
------------------------------------------------------------
NOTE: real token auth doesn't exist yet anywhere in this API
(see api/users/index.php — login never issues one currently).
Matching the app's current state, this checks the PHP-side
session set at login ($_SESSION["user"]) instead.
------------------------------------------------------------
*/
if (empty($_SESSION["user"]["userid"])) {
    $_SESSION["group_error"] = "You must be logged in to create a group.";
    header("Location: ../../login.php?page=login");
    exit;
}

$userId = (int)$_SESSION["user"]["userid"];

/*
------------------------------------------------------------
Read form values.
------------------------------------------------------------
*/
$groupName = trim($_POST["groupName"] ?? "");
$groupDescription = trim($_POST["groupDescription"] ?? "");
$countryIds = !empty($_POST["countryIds"]) ? explode(",", $_POST["countryIds"]) : [];
$languageIds = !empty($_POST["languageIds"]) ? explode(",", $_POST["languageIds"]) : [];

/*
------------------------------------------------------------
Basic validation.
------------------------------------------------------------
*/
if ($groupName === "") {
    $_SESSION["group_error"] = "Group name is required.";
    header("Location: ../../forms/createGroupForm.php");
    exit;
}

/*
------------------------------------------------------------
Build the request body expected by the REST API.
------------------------------------------------------------
*/
$data = [
    "userid" => $userId,
    "group_name" => $groupName,
    "group_description" => $groupDescription,
    "countryIds" => $countryIds,
    "languageIds" => $languageIds
];

$jsonData = json_encode($data);

/*
------------------------------------------------------------
Call POST /api/groups
------------------------------------------------------------
*/
$url = $mainURL . "/api/groups/";

$options = [
    "http" => [
        "method" => "POST",
        "header" =>
            "Content-Type: application/json\r\n" .
            "Accept: application/json\r\n",
        "content" => $jsonData,
        "ignore_errors" => true
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

/*
------------------------------------------------------------
Make sure something was returned.
------------------------------------------------------------
*/
if ($response === false) {
    $_SESSION["group_error"] = "Unable to communicate with the group service.";
    header("Location: ../../forms/createGroupForm.php");
    exit;
}

/*
------------------------------------------------------------
Convert JSON response into PHP array.
------------------------------------------------------------
*/
$result = json_decode($response, true);

if ($result === null) {
    $_SESSION["group_error"] = "Invalid response received from the group service.";
    header("Location: ../../forms/createGroupForm.php");
    exit;
}

/*
------------------------------------------------------------
Successful creation
------------------------------------------------------------
*/
if (isset($result["success"]) && $result["success"] === true) {
    $_SESSION["group_error"] = "";

    // ASSUMPTION — redirect target unknown. Point this at wherever
    // your display-teammate's group page lives once that route
    // exists (e.g. ?page=viewgroup&id=... using the new groupid).
    // Placeholder redirect back to the form with a success flag:
    header("Location: ../../forms/createGroupForm.php?created=1");
    exit;
}

$_SESSION["group_error"] = $result["message"] ?? "Group creation failed.";
header("Location: ../../forms/createGroupForm.php");
exit;

?>