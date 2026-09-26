<?PHP
 
 /*
 ------------------------------------------------------------
 Secure Logout
 ------------------------------------------------------------
 */
 ini_set('display_errors', 1);
 error_reporting(E_ALL & ~E_NOTICE);

 session_start();

 require_once __DIR__ . "/../../includes/config.php";
 
/*
Build Request body for logout endpoint.
*/
$data = [
   "token"      => $_SESSION["api_token"] ?? "",
   "session_id" => session_id(),
   "userid"     => $_SESSION["user"]["userid"] ?? null
];

$jsonData = json_encode($data);

/*
API call.

Currently unused due to API - database requesting stuff
need to configure the info request to join the api_sessions table to the users table 
*/
$url = rtrim($mainURL, "/") . "/api/users/logout";

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


 // Remove authentication data
 unset($_SESSION["user"]);
 unset($_SESSION["LoginStatus"]);
 unset($_SESSION["api_token"]);
 

 // Clear session variables
 $_SESSION = [];

 /*
 ---------------------------------------------------------
 Clear session data.
 ---------------------------------------------------------
 */

 session_destroy();

 session_start();
 session_regenerate_id(true);

 /*
 ---------------------------------------------------------
 Return user to login page.
 ---------------------------------------------------------
 */
 header("location:../login.php?page=login");
 exit;
?>