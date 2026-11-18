
<?php
require_once "../includes/config.php";
require_once "../includes/WFDatabase.php";
include "includes/functions.php";

// Search for Native Tweet users
if (isset($_GET['ajax'])) {

    $search = trim($_GET['query'] ?? '');
    $language = (int)($_GET['language'] ?? 0);
    $region = (int)($_GET['region'] ?? 0);
    $country = (int)($_GET['country'] ?? 0);

    // Nothing entered yet
    if ($search == '' && $language == 0 && $region == 0 && $country == 0) {
        exit;
    }

    // Get users from the database
    $sql = "SELECT DISTINCT u.userid, u.first_name, u.last_name,
                   c.COUNTRY_NAME
            FROM users u
            LEFT JOIN wf_countries c
            ON u.original_country_id = c.COUNTRY_ID
            WHERE 1=1";

    $params = array();

    // Search by first or last name
    if ($search != '') {
        $sql .= " AND (u.first_name LIKE :first
                   OR u.last_name LIKE :last
                   OR CONCAT(u.first_name, ' ', u.last_name) LIKE :fullname)";

        $params[':first'] = '%' . $search . '%';
        $params[':last'] = '%' . $search . '%';
        $params[':fullname'] = '%' . $search . '%';
    }

    // Search by spoken language
    if ($language > 0) {
        $sql .= " AND EXISTS (
                    SELECT 1
                    FROM user_spoken_languages ul
                    WHERE ul.userid = u.userid
                    AND ul.language_id = :language
                  )";

        $params[':language'] = $language;
    }

    // Search by region
    if ($region > 0) {
        $sql .= " AND c.REGION_ID = :region";
        $params[':region'] = $region;
    }

    // Search by country
    if ($country > 0) {
        $sql .= " AND u.original_country_id = :country";
        $params[':country'] = $country;
    }

    $sql .= " ORDER BY u.first_name, u.last_name";

    $users = WFDatabase::getDataFromSQL($sql, $params);

    // Display search results
    if (count($users) == 0) {
        echo "<p>no user found</p>";
    } else {

        foreach ($users as $user) {

            $id = (int)$user['userid'];

            $name = htmlspecialchars(
                trim($user['first_name'] . " " . $user['last_name']),
                ENT_QUOTES,
                'UTF-8'
            );

            $countryName = htmlspecialchars(
                $user['COUNTRY_NAME'] ?? '',
                ENT_QUOTES,
                'UTF-8'
            );

            echo "<div class='search-item'>";
            echo "<a href='search.php?userid=$id'>$name</a>";

            if ($countryName != '') {
                echo " ($countryName)";
            }

            echo "</div>";
        }
    }

    exit;
}

// Get languages for dropdown
$languages = WFDatabase::getDataFromSQL(
    "SELECT LANGUAGE_ID, LANGUAGE_NAME
     FROM wf_languages
     ORDER BY LANGUAGE_NAME"
);

// Get regions for dropdown
$regions = WFDatabase::getDataFromSQL(
    "SELECT REGION_ID, REGION_NAME
     FROM wf_world_regions
     ORDER BY REGION_NAME"
);

// Get countries for dropdown
$countries = WFDatabase::getDataFromSQL(
    "SELECT COUNTRY_ID, COUNTRY_NAME
     FROM wf_countries
     ORDER BY COUNTRY_NAME"
);

// Get a selected student's information
$selectedUser = null;
$selectedLanguages = array();

if (isset($_GET['userid']) && ctype_digit($_GET['userid'])) {

    $selected = WFDatabase::getDataFromSQL(
        "SELECT u.userid, u.first_name, u.last_name,
                c.COUNTRY_NAME
         FROM users u
         LEFT JOIN wf_countries c
         ON u.original_country_id = c.COUNTRY_ID
         WHERE u.userid = :userid",
        array(':userid' => (int)$_GET['userid'])
    );

    if (count($selected) > 0) {

        $selectedUser = $selected[0];

        $selectedLanguages = WFDatabase::getDataFromSQL(
            "SELECT l.LANGUAGE_NAME
             FROM user_spoken_languages ul
             JOIN wf_languages l
             ON ul.language_id = l.LANGUAGE_ID
             WHERE ul.userid = :userid
             ORDER BY l.LANGUAGE_NAME",
            array(':userid' => (int)$_GET['userid'])
        );
    }
}

include "includes/header.php";
include "includes/navbar.php";
?>

<link rel="stylesheet" href="search.css">

<div class="search-container">

    <main>

        <h2>Search for your NATIVE friend</h2>

        <div class="w3-section">
            Search for students by name, language, region, or country.
        </div>

        <h3>Find and connect with other Native Tweet students!</h3>

        <br>

        <!-- Search bar -->
        <div class="topnav">

            <input
                type="text"
                id="search-bar"
                placeholder="Search for a student..."
                autocomplete="off"
            >

        </div>

        <!-- Search results -->
        <div id="results"></div>

        <!-- Selected student's profile -->
        <?php if ($selectedUser != null): ?>

            <div class="public-profile">

                <h3>
                    <?php
                    echo htmlspecialchars(
                        $selectedUser['first_name'] . " " .
                        $selectedUser['last_name'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </h3>

                <p>
                    <b>Country of Origin:</b>

                    <?php
                    echo htmlspecialchars(
                        $selectedUser['COUNTRY_NAME'] ?? 'Not listed',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </p>

                <p>
                    <b>Spoken Languages:</b>

                    <?php
                    if (count($selectedLanguages) > 0) {

                        $names = array();

                        foreach ($selectedLanguages as $language) {
                            $names[] = $language['LANGUAGE_NAME'];
                        }

                        echo htmlspecialchars(
                            implode(", ", $names),
                            ENT_QUOTES,
                            'UTF-8'
                        );

                    } else {
                        echo "No languages listed";
                    }
                    ?>
                </p>

            </div>

        <?php endif; ?>

    </main>

    <!-- Filters on the right side -->
    <aside class="sidebar">

        <h3>Filters</h3>

        <!-- Language filter -->
        <select id="language-select" class="w3-select">

            <option value="">Select a language</option>

            <?php foreach ($languages as $language): ?>

                <option value="<?php echo $language['LANGUAGE_ID']; ?>">

                    <?php
                    echo htmlspecialchars($language['LANGUAGE_NAME']);
                    ?>

                </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <!-- Region filter -->
        <select id="region-select" class="w3-select">

            <option value="">Select a region</option>

            <?php foreach ($regions as $region): ?>

                <option value="<?php echo $region['REGION_ID']; ?>">

                    <?php
                    echo htmlspecialchars($region['REGION_NAME']);
                    ?>

                </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <!-- Country filter -->
        <select id="country-select" class="w3-select">

            <option value="">Select a country</option>

            <?php foreach ($countries as $country): ?>

                <option value="<?php echo $country['COUNTRY_ID']; ?>">

                    <?php
                    echo htmlspecialchars($country['COUNTRY_NAME']);
                    ?>

                </option>

            <?php endforeach; ?>

        </select>

    </aside>

</div>

<script src="search.js" defer></script>

<?php
include "includes/footer.php";
?>
