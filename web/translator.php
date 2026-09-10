<?php 

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

$users = $users[0];
    echo "<h1>";
    echo $users["FIRST_NAME"];
    echo "</h1>";




?>