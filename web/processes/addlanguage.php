
<?php
session_start();

require_once __DIR__ . "/../../includes/config.php";

// Make sure the user is logged in
if (($_SESSION["LoginStatus"] ?? "") !== "YES") {
    header("Location: ../login.php?page=login");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php?page=profile");
    exit;
}

$userId = (int)($_SESSION["user"]["userid"] ?? 0);
$token = $_SESSION["api_token"] ?? "";

$languageId = (int)($_POST["language_id"] ?? 0);
$proficiency = (int)($_POST["proficiency_level"] ?? 0);

// Check the information
if ($userId <= 0 || $token === "") {
    $_SESSION["error"] = "Please log in again.";
    header("Location: ../login.php?page=login");
    exit;
}

if ($languageId <= 0 || $proficiency < 1 || $proficiency > 5) {
    $_SESSION["error"] = "Please select a language and proficiency level.";
    header("Location: ../login.php?page=profile");
    exit;
}

// Send the language information to the API
$data = [
    "language_id" => $languageId,
    "proficency_level" => $proficiency
];

$url = $mainURL . "/api/users/" . $userId . "/languages";

$options = [
    "http" => [
        "method" => "POST",
        "header" =>
            "Content-Type: application/json\r\n" .
            "Authorization: Bearer " . $token . "\r\n",
        "content" => json_encode($data),
        "ignore_errors" => true
    ]
];

$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

$result = json_decode($response ?: "", true);

if (is_array($result) && ($result["success"] ?? false) === true) {
    $_SESSION["error"] = "Language added successfully!";
} else {
    $_SESSION["error"] = $result["message"] ?? "Unable to add language.";
}

// Return to the profile page
header("Location: ../login.php?page=profile");
exit;
?>
