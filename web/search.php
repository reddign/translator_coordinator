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

        <select class="dropdown" name="language" id="language-select">
            <option value="">--Select a language--</option>
            <?php
                $language_sql = "SELECT * FROM wf_languages;";
                $language_results = WFDatabase::getDataFromSQL($language_sql);
                if ($language_results) {
                    foreach ($language_results as $language_row) {
                        echo "<option value={$language_row['LANGUAGE_ID']}>{$language_row['LANGUAGE_NAME']}</option>";
                    }
                }
            ?>
        </select>

        <select class="dropdown" name="location" id="location-select">
            <option value="">--Select a location--</option>
            <?php
                $location_sql = "
                    SELECT REGION_ID AS ID, REGION_NAME AS NAME
                    FROM wf_world_regions
                    UNION ALL
                    SELECT COUNTRY_ID AS ID, COUNTRY_NAME AS NAME
                    FROM wf_countries
                    ORDER BY NAME;
                ";
                $location_results = WFDatabase::getDataFromSQL($location_sql);
                if ($location_results) {
                    foreach ($location_results as $location_row) {
                        echo "<option value={$location_row['ID']}>{$location_row['NAME']}</option>";
                    }
                }
            ?>
        </select>
    </aside>
</div>

<?php include "includes/footer.php"; ?>