<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
session_start();

require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";

/*
------------------------------------------------------------
SCRUM STORY: As a user, I want to view another user's public
profile so that I can learn about their countries, regions,
languages, and interests.

STUB - development in progress on team branch

This page is like profile.php but it:
  - Loads a TARGET user (via ?user_id=) instead of $_SESSION["user"]
  - Is read-only - no edit controls, no "Log out" link

NOTE: search.php (owned by another teammate) is the intended
entry point into this page once it's built out, so you can search up the user's profile
------------------------------------------------------------
*/

// Check if the viewer is logged in (viewing still requires being a user)
if (!isset($_SESSION["user"])) {
    $_SESSION["error"] = "";
    header("Location:..\web\processes\login.php");
    exit;
}

/*
------------------------------------------------------------
Read and validate the target user id.
------------------------------------------------------------
*/
$targetUserId = $_GET["user_id"] ?? null;

if (empty($targetUserId)) {
    $_SESSION["error"] = "No user selected to view.";
    header("Location: profile.php");
    exit;
}

// WILL BE ABLE TO LOOKUP OTHERS PROFILES WHEN these WFDatabase methods are implemented
// $db = new WFDatabase();
// $profile_data = $db->getPublicProfile($targetUserId);
// $associated_countries = $db->getAssociatedCountries($targetUserId);
// $languages = $db->getUserLanguages($targetUserId);
// $interests = $db->getUserInterests($targetUserId);

// placeholder data
$profile_data = [
    "first_name"            => null,
    "last_name"             => null,
    "original_country_name" => null,
];
$associated_countries = []; // WILL NEED from getAssociatedCountries()
$languages = [];            // from getUserLanguages(), if available
$interests = [];            // from getUserInterests(), if available

echo "<!-- This is where you will see the selected user's public profile. -->";

// Main column data
echo "<div style='float:left'>";
echo "<h1>User Profile</h1>";
echo "<b>Name:</b>  " . ($profile_data["first_name"] ?? "This is where the user's name will display.") . " " . ($profile_data["last_name"] ?? "") . "<BR>";
echo "<b>Country of Origin:</b>  " . ($profile_data["original_country_name"] ?? "This is where the country of origin will display.") . "<BR>";
// NOTE: Email and Role are intentionally left off this public view.
echo "</div>";
// no "Log out" link here - this is not the viewer's own profile.

// Side Column Data
echo "<div style='float:right;margin-right:50px;'>";
// Languages
echo "<h2>Spoken Languages</h2>";
echo "This is where the user's spoken languages will display.<BR><BR><BR>";
// Countries of Interest / Associated Countries
// NOTE: data source is getAssociatedCountries() - being built as part
// of the separate "additional countries" story on this same branch.
echo "<h2>Countries of Interest</h2>";
echo "This is where the user's associated countries and regions will display.<BR><BR><BR>";
// Groups
echo "<h2>Groups</h2>";
echo "This is where the user's groups will display.<BR><BR><BR>";
echo "</div>";

include "includes/footer.php";
?>