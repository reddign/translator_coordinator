<?php    

require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";
$id = $_GET["id"];

//Access the API to get data
//SQL will only be in the api.
$countryDataURL = $mainURL."/api/countries/{$id}";
$countryResponse  = getJSONFromURL($countryDataURL);
$currencyDataURL = $mainURL."/api/currencies?countryid={$id}";

$currencyResponse  = getJSONFromURL($currencyDataURL);
$languageDataURL = $mainURL."/api/languages?countryid={$id}";
$languageResponse  = getJSONFromURL($languageDataURL);

// Access your data (Example structure: echo a specific property)
$countries = $countryResponse["data"];
$languages = $languageResponse["data"];
$currencies = $currencyResponse["data"];

$country = $countries[0];

    $name = $country["COUNTRY_NAME"];
    $id = $country["COUNTRY_ID"];
    $ext = $country["INTERNET_EXTENSION"];
    $flag =  $country["FLAG"];
    echo "<img src='images/flags/{$flag}' width='100px'> ";
    
    echo "<h1>";
    echo $name;
    echo "</h1>";
    echo "<BR>";
    $fields=array_keys($country);
   foreach($fields as $index => $fieldName){
        
        if(strstr($fieldName,"ID") || strstr($fieldName,"CODE") || strstr($fieldName,"FLAG")){
            continue;
        }
        if($fieldName == "FIPS_ID"){
            break;
        }
        echo "<b>";
        echo ucwords(strtolower(str_replace("_"," ",$fieldName)));
        echo ":</b> ";
        echo $country[$fieldName];
        echo "<BR>";
   }
   // print_r($rows[0]);
echo "<BR><BR><BR>";
   

echo "<h3>Currency Code</h3>";
foreach($currencies as $currency){
    echo $currency["CURRENCY_NAME"];
}
echo "<h3>Spoken Languages</h3>";
foreach($languages as $language){
    echo $language["LANGUAGE_NAME"];
    echo "<br>";
}
echo "<BR><BR><BR><br>";
include "includes/footer.php";
?>