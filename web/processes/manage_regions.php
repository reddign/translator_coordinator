<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
 
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";

/*
US-06: As a user, I want to associate myself with regions of the world so that
other users can discover people with regional experience or interests.

This is a process file that receives the post and does the work.
*/

$back = "../forms/regionsForm.php";
 
// must be logged in
if (!isset($_SESSION["user"])) {
    $_SESSION["error"] = "You must be logged in.";
    header("Location: login.php");
    exit;
}
 
// accept a form submission
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . $back);
    exit;
}
 
$userId = $_SESSION["user"]["userid"];
$action = $_POST["action"] ?? "";
$id     = $_POST["region_id"] ?? "";
 
if (!ctype_digit((string)$id)) {
 
    $_SESSION["regions_message"] = "Please select a region.";
 
} elseif ($action === "add") {
 
    // the selected region is saved to the database
    $result = WFDatabase::addUserRegion($userId, (int)$id);
    $_SESSION["regions_message"] = $result["message"];
 
} elseif ($action === "remove") {
 
    // the selected region is removed from the database
    $result = WFDatabase::removeUserRegion($userId, (int)$id);
    $_SESSION["regions_message"] = $result["message"];
 
} else {
 
    $_SESSION["regions_message"] = "Invalid request.";
}
 
// go back to the page so the user sees the updated list and the message
header("Location: " . $back);
exit;