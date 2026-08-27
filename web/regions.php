<?php
$regionid=$_GET["id"];
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";

// Define the API URL
$regionDataURL = $mainURL."/api/regions/{$regionid}";
$countryDataURL = $mainURL."/api/countries?regionid={$regionid}";

$regionResponse = getJSONFromURL($regionDataURL);
$countryResponse  = getJSONFromURL($countryDataURL);

// Access your data (Example structure: echo a specific property)
$countries = $countryResponse["data"];
$regionName = $regionResponse["data"][0]["REGION_NAME"];
?>

<div class="w3-padding">
<h1>Regions - <?PHP echo $regionName; ?> </h1>

<?php
foreach($countries as $country){
    $imageName = "images/flags/".$country["FLAG"];
    echo "<img src='{$imageName}' width='30px'> - ";
    echo "<a href='country.php?id=".$country["COUNTRY_ID"]."'>";
    echo $country["COUNTRY_NAME"];
    echo "</a>";
    echo "<BR>";
}


?>
</div>

<?PHP

include "includes/footer.php";

?>