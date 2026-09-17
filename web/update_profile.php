<?php

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

session_start();

require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";


// Check if the user is logged in
if (!isset($_SESSION["user"])) {
    $_SESSION["error"] = "You must be logged in.";
    header("Location: processes/login.php");
    exit;
}


// Display the update profile form
require_once "forms/updateProfileForm.php";


include "includes/footer.php";

?>