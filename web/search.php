<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";
require_once "includes/functions.php";
require_once "includes/header.php";
require_once "includes/navbar.php";

$languageDataURL = $mainURL."/api/languages/";
$languageResponse = getJSONFromURL($languageDataURL);
$languages = $languageResponse["data"];
?>

<h2>Translator Coordinator - Search Feature</h2>
<!--Dropdown menus for different filters.-->
<div class="w3-container w3-margin-bottom w3-section">
    <form action="search.php" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
    <input
        type="text"
        name="user"
        class="w3-input"
        placeholder="Search users..."
        style="flex: 1; min-width: 150px;"
    >
        <select name="language" class="w3-select" style="flex: 1; min-width: 50px;">
            <option value="" disabled selected>Language</option>
            <?php
                if($languages) {
                    foreach ($languages as $language){
                        echo "<option value={$language['LANGUAGE_ID']}>{$language['LANGUAGE_NAME']}</option>";
                    }
                }
            ?>
        </select>

        <select name="country" class="w3-select" style="flex: 1; min-width: 50px;">
            <option value="" disabled selected>Country</option>
            <option value="us">United States</option>
            <option value="mx">Mexico</option>
        </select>

        <select name="region" class="w3-select" style="flex: 1; min-width: 50px;">
            <option value="" disabled selected>Region</option>
            <option value="north">North</option>
            <option value="south">South</option>
        </select>

        <select name="group" class="w3-select" style="flex: 1; min-width: 50px;">
            <option value="" disabled selected>Group</option>
            <option value="beginners">Beginners</option>
            <option value="advanced">Advanced</option>
        </select>
        <!--Sends select GET parameters back to search.php-->
        <button type="submit" class="w3-button w3-blue" style="white-space: nowrap;">Search</button>
    </form>
    
    
    
</div>

<?php
// GET Filters
$user = $_GET['user']         ?? null;
$language = $_GET['language'] ?? null;
$country  = $_GET['country'] ?? null;
$region   = $_GET['region'] ?? null;
$group    = $_GET['group'] ?? null; 


$countries  = [];
$currencies = [];
$languages  = [];

// Fetch API data only if ID exists
if ($id) {
    $countryResponse  = getJSONFromURL("{$mainURL}/api/countries/{$id}");
    $currencyResponse = getJSONFromURL("{$mainURL}/api/currencies?countryid={$id}");
    $languageResponse = getJSONFromURL("{$mainURL}/api/languages?countryid={$id}");

    $countries  = $countryResponse['data']  ?? [];
    $currencies = $currencyResponse['data'] ?? [];
    $languages  = $languageResponse['data'] ?? [];
}

$countryName = $countries[0]['COUNTRY_NAME'] ?? '';
$countryId   = $countries[0]['COUNTRY_ID']   ?? $id;

// Parameters aligned: ($userName, $languageName, $countryName, $regionName, $group)
$results = searchTranslators($user, $language, $countryName, $region, $group);
?>

<h2>Search Results</h2>

<div class="w3-container">
    <p>Showing search results for Language: <strong><?= htmlspecialchars($language ?? 'All') ?></strong></p>
    
    <ul>
        <?php foreach ($results as $translator): ?>
            <li><?= $translator['name'] ?> - <?= $translator['language'] ?> (<?= $translator['country'] ?>)</li>
        <?php endforeach; ?>
    </ul>
</div>


<?php
include "includes/footer.php";
?>