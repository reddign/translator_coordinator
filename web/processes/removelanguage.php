
<?php
session_start();

require_once __DIR__ . "/../../includes/config.php";

// Only logged-in users can remove languages
if (($_SESSION["LoginStatus"] ?? "") !== "YES") {
    header("Location: ../login.php?page=login");
    exit;
}

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php?page=profile");
    exit;
}

$userId = (int)($_SESSION["user"]["userid"] ?? 0);
$token = $_SESSION["api_token"] ?? "";
$languageId = (int)($_POST["language_id"] ?? 0);

// Check the user and language
if ($userId <= 0 || $token === "" || $languageId <= 0) {
    $_SESSION["error"] = "Unable to remove language.";
    header("Location: ../login.php?page=profile");
    exit;
}

// Send DELETE request to the API
$url = $mainURL . "/api/users/" . $userId .
       "/languages/" . $languageId;

$options = [
    "http" => [
        "method" => "DELETE",
        "header" =>
            "Accept: application/json\r\n" .
            "Authorization: Bearer " . $token . "\r\n",
        "ignore_errors" => true,
        "timeout" => 10
    ]
];

$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

$result = json_decode($response ?: "", true);

// Check if the language was removed
if (is_array($result) && ($result["success"] ?? false) === true) {
    $_SESSION["error"] = "Language removed successfully!";
} else {
    $_SESSION["error"] = $result["message"] ?? "Unable to remove language.";
}

// Return to profile
header("Location: ../login.php?page=profile");
exit;
?>
