<?php
session_start();
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../includes/functions.php";

// Only log out on POST so other sites can't log people out with a link
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php?page=login");
    exit;
}

apiRequest("POST", $mainURL . "/api/users/logout"); // revokes the token

$_SESSION = [];
session_destroy();
header("Location: ../login.php?page=login");
exit;