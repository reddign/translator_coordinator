<?php
    require_once __DIR__ . "/../includes/config.php";
    require_once __DIR__ . "/../includes/WFDatabase.php";
    require_once __DIR__ . "/../api/users/user_functions.php";
    include "includes/functions.php";
    include "includes/header.php";
    include "includes/navbar.php";

    #REST API grabs

    $userDataURL = $mainURL."/api/translators";
    $userResponse  = getJSONFromURL($userDataURL);
    $users = $userResponse["data"];

    //Grab whats in search bar
    if (isset($_GET['query'])) {
        $search_string = trim($_GET['query']);
        if (!empty($search_string)) {
            // Displaying searched for users
            $displayedUsers = [];
            foreach ($users as $user){
                $name = $user["first_name"] . " " . $user["last_name"];
                if(str_contains(strtolower($name), strtolower($search_string)) && !in_array($user["userid"], $displayedUsers)){
                    echo "<div class='search-item'>
                        <a href='translator.php?id={$user["userid"]}'>" .
                        htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) .
                        "</a>
                    </div>";
                    // Made so duplicates don't appear when searching due to multiple entries from a user having multiple langauges listed
                    $displayedUsers[] = $user["userid"];
                }
            }
        }
        exit;
    }


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
                You will also be able to search for groups to join that have interest in the language you are learning.
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