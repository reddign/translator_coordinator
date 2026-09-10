<?php 

// Just to push at least something because I do not understand why I do not get any updates that other people do.
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";
$id = $_GET["id"];

//Access the API to get data
$usersDataURL = $mainURL."/api/users/{$id}";
$usersResponse  = getJSONFromURL($usersDataURL);

// Access your data
$users = $usersResponse["data"];

// Hopefully at some point this will get the users' data and put it on the page.
$users = $users[0];
    echo "<h1>";
    echo $users["FIRST_NAME"];
    echo "</h1>";




?>
// Comment 3.0 because two previous times I was off the branch
// Again.. this file might get deleted. And I do not care anymore because I just want to get back to the right branch.
// I am still concerned that my comments are white and not gray for some reason.

// Just testing some more