<?php 

// Just to push at least something because I do not understand why I do not get any updates that other people do.
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";
$translatorId = $_GET["id"];

//Access the API to get data
$usersDataURL = $mainURL."/api/translators/{$translatorId}";

// echo $usersDataURL;

$usersResponse  = getJSONFromURL($usersDataURL);
$users = $usersResponse["data"];
/* checking the data that comes back from the API call
echo "<pre>";
var_dump($users); 
echo "</pre>";
*/

// Hopefully at some point this will get the users' data and put it on the page.
$user = $users[0];

    $first_name = $user["first_name"];
    $last_name = $user["last_name"];
    $date_registered = $user["date_registered"];
    $flag = $user["flag"];

    echo "<h1>{$first_name} {$last_name}</h1>";
    echo "<img src='images/flags/{$flag}' width='50px'> ";
    echo "<h3>Registered since: {$date_registered}</h3>";

echo "<h3>Spoken Languages</h3>";
foreach($users as $user){

    $languge_name = $user["language_name"];
    $proficency_level = $user["proficency_level"];

    echo "Name: {$languge_name} | Proficiency Level: ";
    if ($proficency_level === null) {
    echo "Not provided";
    } else {
    echo $proficency_level;
    }
    echo "<br>";
}

?>
