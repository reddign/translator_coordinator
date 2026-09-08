<?php
require_once __DIR__ . "/../includes/config.php";


include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";
$error = $_SESSION["error"]??"";
$page = $_GET["page"]??"login";

echo "<div class='gerror'>{$error}</div>";

if($page=="login"){
    require_once "forms/loginForm.php";
}else if($page=="register"){
    require_once "forms/registrationForm.php";
}else{
    $profile_data = $_SESSION["user"];
    //Main column data
    echo "<div style='float:left'>";
    echo "<h1>User Profile</h1>";
    echo "<b>Name:</b>  {$profile_data["first_name"]} {$profile_data["last_name"]}<BR>";
    echo "<b>Country of Origin:</b>  {$profile_data["original_country_name"]}<BR>";
    echo "<b>Email:</b>  <a href='mailto:{$profile_data["email"]}'>{$profile_data["email"]}</a><BR>";
    echo "<b>Role:</b>  {$profile_data["role"]}<BR>";
    echo "<b>Member Since:</b>  {$profile_data["date_registered"]}<BR>";
    echo "</div>";

    //Side Column Data
    echo "<div style='float:right;margin-right:50px;'>";
    //Languages
    echo "<h2>Spoken Languages</h2>";
    echo "<BR><BR><BR>";

    //Countries
    echo "<h2>Countries of Interest</h2>";
    echo "<BR><BR><BR>";

    //Countries
    echo "<h2>Groups</h2>";
    echo "<BR><BR><BR>";
    echo "</div>";
}

include "includes/footer.php";
?>