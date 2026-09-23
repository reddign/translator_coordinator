<?php
include "../includes/config.php";
include "../includes/WFDatabase.php";
include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";
?>

<h2>Translator Coordinator - Search Feature</h2>
<!--Dropdown menus for different filters.-->
<div class="w3-container w3-margin-bottom w3-section">
    <form action="search.php" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <select name="language" class="w3-select" style="flex: 1; min-width: 50px;">
            <option value="">Language</option>
            <?php
            $languages = WFDatabase::getDataFromSQL("
            SELECT LANGUAGE_ID, LANGUAGE_NAME
        FROM wf_languages
        ORDER BY LANGUAGE_NAME
        ");
        ?>

        <?php foreach ($languages as $language): ?>

            <option value="<?= $language['LANGUAGE_ID'] ?>">
            <?= $language['LANGUAGE_NAME'] ?>
        </option>

    <?php endforeach; ?>
        </select>

        <select name="country" class="w3-select" style="flex: 1; min-width: 50px;">
            <option value="">Country</option>
            <?php
            $countries = WFDatabase::getDataFromSQL("
            SELECT COUNTRY_ID, COUNTRY_NAME
        FROM wf_countries
        ORDER BY COUNTRY_NAME
        ");
        ?>

        <?php foreach ($countries as $country): ?>

            <option value="<?= $country['COUNTRY_ID'] ?>">
            <?= $country['COUNTRY_NAME'] ?>
        </option>

    <?php endforeach; ?>
        </select>

        <select name="region" class="w3-select" style="flex: 1; min-width: 50px;">
            <option value="">Region</option>
            <?php
            $regions = WFDatabase::getDataFromSQL("
            SELECT REGION_ID, REGION_NAME
        FROM wf_world_regions
        ORDER BY REGION_NAME
        ");
        ?>

        <?php foreach ($regions as $region): ?>

            <option value="<?= $region['REGION_ID'] ?>">
            <?= $region['REGION_NAME'] ?>
        </option>

    <?php endforeach; ?>
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
//Checks if filter data exists before trying to get it. Also sets default to null incase of failure.
$language = $_GET['language'] ?? null;
$country  = $_GET['country'] ?? null;
$region   = $_GET['region'] ?? null;
$group    = $_GET['group'] ?? null; 

// Call stub function. Currently a placeholder for demos.
$results = searchTranslators($language, $country, $region, $group);
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