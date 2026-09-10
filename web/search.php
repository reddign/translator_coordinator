<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";

if (isset($_GET['query'])) {
    $search_string = trim($_GET['query']);
    if (!empty($search_string)) {
        // SQL Statement using search bar input
        $sql = "SELECT * FROM wf_countries WHERE country_name LIKE '$search_string%'";

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
        </div>

        <BR>
        <!-- Search bar -->
        <div class="topnav">
            <input type="text" id="search-bar" placeholder="Search..">
        </div>

        <div id="results"></div>


<?php
include "includes/footer.php";
?>