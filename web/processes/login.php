<?PHP

#TODO: Explore assigning sessions with user ids to track useage accurately and ensure security

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require_once __DIR__ . "/../../includes/config.php";
/*
------------------------------------------------------------
Make sure the page was accessed using POST.
------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php?page=login");
    exit;
}

/*
------------------------------------------------------------
Read form values.
------------------------------------------------------------
*/
$username = $_POST["user"];
$userpassword = $_POST["pass"];
$result = [];

/*
------------------------------------------------------------
Basic validation.
------------------------------------------------------------
*/
if (
    $username === ""        ||
    $userpassword === "" 
) {
    $_SESSION["error"] = "All fields are required.";
    header("Location: ../login.php?page=login");
    exit;
}

/*
------------------------------------------------------------
Build the request body expected by the REST API.
------------------------------------------------------------
*/

$data = [
    "email" => $username,
    "password" => $userpassword
];

$jsonData = json_encode($data);

/*
------------------------------------------------------------
Call POST /api/users/login
------------------------------------------------------------
*/

$url = rtrim($mainURL, "/") . "/api/users/login";

$options = [
    "http" => [
        "method" => "POST",
        "header" =>
            "Content-Type: application/json\r\n" .
            "Accept: application/json\r\n",
        "content" => $jsonData,
        "ignore_errors" => true
    ],
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
        "allow_self_signed" => true
    ]
];


$context = stream_context_create($options);

$response = file_get_contents(
    $url,
    false,
    $context
);


/*
------------------------------------------------------------
Make sure something was returned.
------------------------------------------------------------
*/

if ($response === false) {
    $_SESSION["error"] = "Unable to communicate with the login service.";
    header("Location: ../login.php?page=login");
    exit;
}

/*
------------------------------------------------------------
Convert JSON response into PHP array.
------------------------------------------------------------
*/

$result = json_decode($response, true);

if ($result === null) {
    $_SESSION["error"] = "Invalid response received from the login service.";
    $_SESSION["api_token"] = "";
    $_SESSION["LoginStatus"] = "NO";
    $_SESSION["user"] = "";
    $_SESSION["Name"] = "";
    header("Location: ../login.php?page=login");
    exit;
}

/*
------------------------------------------------------------
Successful login
------------------------------------------------------------
*/

# TODO: Store authenticated user ID in the session.
# TODO: Record login timestamp.
# TODO: Regenerate session ID after login.
# TODO: Associate API token with the current session.
# TODO: Record session activity for auditing.
# TODO: Save session information to the database.

if (
    isset($result["success"]) &&
    $result["success"] === true
) {

    /*
    The login endpoint logs the user in by returning
    an authentication token.
    */

    $_SESSION["api_token"] = $result["token"];
    $_SESSION["user"] = $result["user"];
    $_SESSION["LoginStatus"] = "YES";

    /*
    Future Feature:
    This is where the authenticated user's ID,
    login time, and session information will be
    stored for session tracking and security.
    */

    $_SESSION["error"] = "";

    header("location:../profile.php");
    exit;
}



$_SESSION["error"] = $result["message"] ?? "Login failed.";
header("Location: ../login.php?page=login");
exit;



?>