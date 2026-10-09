<?php
require_once __DIR__ . "/../includes/config.php";

include "includes/functionsAlpha.php";
include "includes/header.php";
include "includes/navbar.php";


$languageDataURL = $mainURL . "/api/languages";
$countryDataURL  = $mainURL . "/api/countries";
$regionDataURL   = $mainURL . "/api/regions";

$languageResponse = getJSONFromURL($languageDataURL);
$countryResponse  = getJSONFromURL($countryDataURL);
$regionResponse   = getJSONFromURL($regionDataURL);

$languages = $languageResponse["data"] ?? [];
$countries = $countryResponse["data"] ?? [];
$regions   = $regionResponse["data"] ?? [];
?>

<h2>Translator Coordinator - Search Feature</h2>

<div class="w3-container w3-margin-bottom w3-section">
    <form action="search.php" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <input
            type="text"
            name="user"
            class="w3-input"
            placeholder="Search users..."
            value="<?= htmlspecialchars($_GET['user'] ?? '') ?>"
            style="flex: 1; min-width: 150px;"
        >

        <select name="language" class="w3-select" style="flex: 1; min-width: 50px;">
            <option value="">Language</option>
            <?php foreach ($languages as $language): ?>
                <option value="<?= $language['LANGUAGE_ID'] ?>" <?= (isset($_GET['language']) && $_GET['language'] == $language['LANGUAGE_ID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($language['LANGUAGE_NAME']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="country" class="w3-select" style="flex: 1; min-width: 50px;">
            <option value="">Country</option>
            <?php foreach ($countries as $country): ?>
                <option value="<?= $country['COUNTRY_ID'] ?>" <?= (isset($_GET['country']) && $_GET['country'] == $country['COUNTRY_ID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($country['COUNTRY_NAME']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="region" class="w3-select" style="flex: 1; min-width: 50px;">
            <option value="">Region</option>
            <?php foreach ($regions as $region): ?>
                <option value="<?= $region['REGION_ID'] ?>" <?= (isset($_GET['region']) && $_GET['region'] == $region['REGION_ID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($region['REGION_NAME']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="group" class="w3-select" style="flex: 1; min-width: 50px;">
            <option value="">Group</option>
            <option value="beginners" <?= (isset($_GET['group']) && $_GET['group'] === 'beginners') ? 'selected' : '' ?>>Beginners</option>
            <option value="advanced" <?= (isset($_GET['group']) && $_GET['group'] === 'advanced') ? 'selected' : '' ?>>Advanced</option>
        </select>

        <button type="submit" class="w3-button w3-blue" style="white-space: nowrap;">Search</button>
    </form>
</div>

<?php
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
                    <strong><?= htmlspecialchars($translator['full_name'] ?? ($translator['first_name'] . ' ' . $translator['last_name'])) ?></strong> — 
                    Language: <?= htmlspecialchars($translator['language_name'] ?? 'N/A') ?> | 
                    Country: <?= htmlspecialchars($translator['country_name'] ?? $translator['original_country_name'] ?? 'N/A') ?> | 
                    Region: <?= htmlspecialchars($translator['region_name'] ?? 'N/A') ?> | 
                    Group: <?= htmlspecialchars($translator['group_name'] ?? 'N/A') ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php
include "includes/footer.php";
?>