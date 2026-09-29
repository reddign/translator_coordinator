<?php 

// Just to push at least something because I do not understand why I do not get any updates that other people do.
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";
$translatorId = $_GET["id"] ?? null;

//Access the API to get data
$usersDataURL = $mainURL."/api/translators/{$translatorId}";

$usersResponse  = getJSONFromURL($usersDataURL);
$users = $usersResponse["data"];

// Display the translators's data
$user = $users[0];

    // Getting the origin country
    $countryId = $user["original_country_id"];
    $countryDataURL = $mainURL."/api/countries/{$countryId}";
    $countryResponse  = getJSONFromURL($countryDataURL);
    $countries = $countryResponse["data"];
    $country = $countries[0];
    $country_name = $country["COUNTRY_NAME"];

    $first_name = $user["first_name"];
    $last_name = $user["last_name"];
    $date_registered = $user["date_registered"];
    $flag = $user["flag"] ?? null;

    echo "<h1>{$first_name} {$last_name}</h1>";
    echo "<img src='images/flags/{$flag}' width='50px'> ";
    echo "<h3>Origin Country: {$country_name}</h3>";
    echo "Registered since: {$date_registered}";

echo "<h3>Translating Languages</h3>";
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

// TODO: needs link to the translatorEditor page if user looking at their own profile

?>
