<?PHP

// Don't show PHP errors to visitors on the live site (log them instead)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . "/../../includes/config.php";

/*
--------------------------------------------------------------
Make sure the page was accessed using POST.
--------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php?page=login");
    exit;
}


/*
--------------------------------------------------------------
Forgot Password Feature
--------------------------------------------------------------
*/

if (($_POST["action"] ?? "") === "forgot-password") {

    $email = trim($_POST["email"] ?? "");
    $newPassword = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    // This makes sure all fields are filled out
    if ($email === "" || $newPassword === "" || $confirmPassword === "") {
        $_SESSION["error"] = "All fields are required to be filled.";
        header("Location: ../login.php?page=forgot-password");
        exit;
    }

    // Check email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"] = "Please enter a valid email address.";
        header("Location: ../login.php?page=forgot-password");
        exit;
    }

    // Checks the password length
    if (strlen($newPassword) < 8) {
        $_SESSION["error"] = "Password must be at least 8 characters long.";
        header("Location: ../login.php?page=forgot-password");
        exit;
    }

    // Check if passwords match
    if ($newPassword !== $confirmPassword) {
        $_SESSION["error"] = "Password doesn't match.";
        header("Location: ../login.php?page=forgot-password");
        exit;
    }

    // Updates information for the API
    $data = [
        "email" => $email,
        "password" => $newPassword
    ];

    $options = [
        "http" => [
            "method" => "POST",
            "header" => "Content-Type: application/json\r\n",
            "content" => json_encode($data),
            "ignore_errors" => true
        ]
    ];

    $context = stream_context_create($options);

    $response = file_get_contents(
        $mainURL . "/api/users/reset-password",
        false,
        $context
    );

    if ($response === false) {
        $_SESSION["error"] = "Unable to reset password.";
        header("Location: ../login.php?page=forgot-password");
        exit;
    }

    $result = json_decode($response, true);

    if (is_array($result) && ($result["success"] ?? false) === true) {
        $_SESSION["error"] = "Password updated successfully. You can now log in.";
        header("Location: ../login.php?page=login");
        exit;
    }

    $_SESSION["error"] = $result["message"] ?? "Unable to reset password.";

    header("Location: ../login.php?page=forgot-password");
    exit;
}


/*
--------------------------------------------------------------
Read form values.
--------------------------------------------------------------
*/
$username     = trim($_POST["user"] ?? "");
$userpassword = $_POST["pass"] ?? "";
$result       = [];

/*
--------------------------------------------------------------
Basic validation.
--------------------------------------------------------------
*/
if ($username === "" || $userpassword === "") {
    $_SESSION["error"] = "All fields are required.";
    header("Location: ../login.php?page=login");
    exit;
}

/*
--------------------------------------------------------------
Build the request body expected by the REST API.
--------------------------------------------------------------
*/
$data = [
    "email"    => $username,
    "password" => $userpassword
];

$jsonData = json_encode($data);

/*
--------------------------------------------------------------
Call POST /api/users/login
--------------------------------------------------------------
*/
$url = $mainURL . "/api/users/login";

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

$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);

/*
--------------------------------------------------------------
Make sure something was returned.
--------------------------------------------------------------
*/
if ($response === false) {
    $_SESSION["error"] = "Unable to communicate with the login service.";
    header("Location: ../login.php?page=login");
    exit;
}

/*
--------------------------------------------------------------
Convert JSON response into PHP array.
--------------------------------------------------------------
*/
$result = json_decode($response, true);

if ($result === null) {
    $_SESSION["error"]       = "Invalid response received from the login service.";
    $_SESSION["api_token"]   = "";
    $_SESSION["LoginStatus"] = "NO";
    $_SESSION["user"]        = "";
    $_SESSION["Name"]        = "";
    header("Location: ../login.php?page=login");
    exit;
}

/*
--------------------------------------------------------------
Successful login
--------------------------------------------------------------
*/
if (isset($result["success"]) && $result["success"] === true) {

    // New session ID on login - prevents session fixation attacks
    session_regenerate_id(true);

    $user = $result["user"] ?? [];
    unset($user["password"]);   // never keep the password hash in the session

    /*
    The login endpoint logs the user in by returning
    an authentication token.
    */
    $_SESSION["api_token"]   = $result["token"] ?? "";   // API doesn't return a token yet
    $_SESSION["user"]        = $user;
    $_SESSION["LoginStatus"] = "YES";
    $_SESSION["error"]       = "";
    header("Location: ../login.php?page=profile");
    exit;
}

/*
--------------------------------------------------------------
Failed login - same message every time so attackers can't
tell whether the email exists.
--------------------------------------------------------------
*/
$_SESSION["error"] = "Invalid email or password.";
header("Location: ../login.php?page=login");
exit;