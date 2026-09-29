<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
 
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";

/*
US-05: As a user, I want to associate myself with additional countries so that
I can indicate countries where I have lived, traveled, studied, or have
other connections.

This is used as a process file.
*/

$back = "../forms/countriesForm.php";
 
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
$id     = $_POST["country_id"] ?? "";

 
if (!ctype_digit((string)$id)) {
 
    $_SESSION["countries_message"] = "Please select a country.";
 
} elseif ($action === "add") {
 
    // the selected country is saved to the database.
    $result = WFDatabase::addUserCountry($userId, (int)$id);
    $_SESSION["countries_message"] = $result["message"];
 
} elseif ($action === "remove") {
 
    // the selected country is removed from the database.
    $result = WFDatabase::removeUserCountry($userId, (int)$id);
    $_SESSION["countries_message"] = $result["message"];
 
} else {
 
    $_SESSION["countries_message"] = "Invalid request.";
}
 
// go back to the page so the user sees the updated list and the message
header("Location: " . $back);
exit;