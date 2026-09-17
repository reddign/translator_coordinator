<?php
    require_once __DIR__ . "/../includes/config.php";
    require_once __DIR__ . "/../includes/WFDatabase.php";

    if (isset($_GET['query'])) {
        $search_string = trim($_GET['query']);
        if (!empty($search_string)) {
            // SQL Statement using search bar input
            $sql = "SELECT * FROM wf_countries WHERE country_name LIKE '%$search_string%'";

            // Using WFDatabase functions to prevent redundancy
            $results = WFDatabase::getDataFromSQL($sql);
            // Give results from SQL search to javascript file
            if ($results) {
                /**echo "<pre>";
                print_r($results);
                echo "</pre>";*/
                foreach ($results as $row) {
                    echo "<div class='search-item'>" . htmlspecialchars($row['COUNTRY_NAME']) . "</div>";
                }
            } else {
                echo "<div class='no-results'>No results found.</div>";
            }
        }
        exit;
    }

    include "includes/functions.php";
    include "includes/header.php";
    include "includes/navbar.php";

    $locationDataURL = $mainURL."/api/locations/";
    $locationResponse  = getJSONFromURL($locationDataURL);
    $languageDataURL = $mainURL."/api/languages/";
    $languageResponse  = getJSONFromURL($languageDataURL);

    $locations = $locationResponse["data"];
    $languages = $languageResponse["data"];
?>

<link rel="stylesheet" href="search.css">

<div class="search-container">
    <main>
        <h2>Translator Coordinator - Search Feature</h2>
        <!-- Link javascript file to do searching -->
        <!-- js script makes it so search automatically updates with each key input -->
        <script src="search.js" defer></script>
        <div class="w3-section" id="team-names">
            <span class="name">Once completed you will be able to search for translators by language, country or region.
                You will also be able to search for groups to join that have interest in the lanugae you are learning.
            </span>
        </div>
        <h3>Stay tuned for more updates.</h3>

        <BR>
        <!-- Search bar -->
        <div class="topnav">
            <input type="text" id="search-bar" placeholder="Search..">
        </div>

        <div id="results"></div>
    </main>

    <aside class="sidebar" aria-label="filter-sidebar">
        <h3>Filters</h3>

        <!-- Filter by language -->
        <label for="language-select">Filter by language:</label>
        <select id="language-select">
            <option value="" selected>--Select a language--</option>
            <?php
                if ($languages) {
                    foreach ($languages as $language) {
                        echo "<option value={$language['LANGUAGE_ID']}>{$language['LANGUAGE_NAME']}</option>";
                    }
                }
            ?>
        </select>

        <!-- Filter by region and country -->
        <label for="location-select">Filter by region or country:</label>
        <select id="location-select">
            <option value="" selected>--Select a location--</option>
            <?php
                if ($locations) {
                    foreach ($locations as $location) {
                        echo "<option value={$location['ID']}>{$location['NAME']}</option>";
                    }
                }
            ?>
        </select>
    </aside>
</div>

<?php include "includes/footer.php"; ?>