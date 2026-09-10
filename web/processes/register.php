<?PHP

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
    header("Location: ../login.php?page=register");
    exit;
}

/*
------------------------------------------------------------
Read form values.
------------------------------------------------------------
*/
$firstName = trim($_POST["first"] ?? "");
$lastName = trim($_POST["last"] ?? "");
$email = trim($_POST["email"] ?? "");
$userpassword = $_POST["pass"] ?? "";
$countryId = $_POST["country_origin"] ?? "";

/*
------------------------------------------------------------
Basic validation.
------------------------------------------------------------
*/
if (
    $firstName === "" ||
    $lastName === "" ||
    $email === "" ||
    $userpassword === "" ||
    $countryId === ""
) {
    $_SESSION["error"] = "All registration fields are required.";
    header("Location: ../login.php?page=register");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error"] = "Please enter a valid email address.";
    header("Location: ../login.php?page=register");
    exit;
}

if (!ctype_digit($countryId)) {
    $_SESSION["error"] = "Please select a valid country.";
    header("Location: ../login.php?page=register");
    exit;
}

/*
------------------------------------------------------------
Build the request body expected by the REST API.
------------------------------------------------------------
*/

$data = [
    "email" => $email,
    "password" => $userpassword,
    "first_name" => $firstName,
    "last_name" => $lastName,
    "original_country_id" => (int)$countryId
];

$jsonData = json_encode($data);

/*
------------------------------------------------------------
Call POST /api/users/register
------------------------------------------------------------
*/

$url = rtrim($mainURL, "/") . "/api/users/register";

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
    $_SESSION["error"] = "Unable to communicate with the registration service.";
    header("Location: ../login.php?page=register");
    exit;
}

/*
------------------------------------------------------------
Convert JSON response into PHP array.
------------------------------------------------------------
*/

$result = json_decode($response, true);

if ($result === null) {
    $_SESSION["error"] = "Invalid response received from the registration service.";
    header("Location: ../login.php?page=register");
    exit;
}

/*
------------------------------------------------------------
Successful registration
------------------------------------------------------------
*/

if (
    isset($result["success"]) &&
    $result["success"] === true
) {

    /*
    The registration endpoint logs the user in by returning
    an authentication token.
    */
    $_SESSION["api_token"] = $result["token"];
    $_SESSION["user"] = $result["user"];
    $_SESSION["error"] = "";
    header("Location: ../login.php?page=login");
    exit;
}


/*
------------------------------------------------------------
API returned an error.
------------------------------------------------------------
*/

$_SESSION["error"] = $result["message"] ?? "Registration failed.";
header("Location: ../login.php?page=register");
exit;
