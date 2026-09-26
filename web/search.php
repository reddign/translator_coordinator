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
    <input
        type="text"
        name="user"
        class="w3-input"
        placeholder="Search users..."
        style="flex: 1; min-width: 150px;"
    >
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
// Call the database function
$results = searchTranslators($_GET);
?>

<h2>Search Results</h2>

<div class="w3-container">
    <?php if (empty($results)): ?>
        <p>No translators found matching your criteria.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($results as $translator): ?>
                <li>
                    <strong><?= htmlspecialchars($translator['full_name']) ?></strong> — 
                    Language: <?= htmlspecialchars($translator['language_name']) ?> | 
                    Country: <?= htmlspecialchars($translator['country_name']) ?> | 
                    Region: <?= htmlspecialchars($translator['region_name']) ?> | 
                    Group: <?= htmlspecialchars($translator['group_name']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>


<?php
include "includes/footer.php";
?>