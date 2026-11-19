
<?php
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

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
    }
}

// Selected filters (0 = any)
$country  = (int)($_GET['country'] ?? 0);
$region   = (int)($_GET['region'] ?? 0);
$language = (int)($_GET['language'] ?? 0);
$searched = $country > 0 || $region > 0 || $language > 0;

// Load dropdown lists
$options = apiRequest("GET", $mainURL . "/api/users/search-options");
$countries = $options['countries'] ?? [];
$regions   = $options['regions'] ?? [];
$languages = $options['languages'] ?? [];

// Run the search
$users = [];
$error = "";
if ($searched) {
    $query = http_build_query(array_filter([
        'country'  => $country,
        'region'   => $region,
        'language' => $language,
    ]));
    $result = apiRequest("GET", $mainURL . "/api/users/search?" . $query);

    if ($result === null || empty($result['success'])) {
        $error = $result['message'] ?? "Search isn't working right now. Try again.";
    } else {
        $users = $result['data'] ?? [];
    }
}
?>

<h2>Native Tweet - Find Students</h2>

<div class="w3-section" id="search">
    <p>Search by country, region, language, or any combination.</p>

    <form action="search.php" method="get">
        Country:
        <select name="country">
            <option value="0">Any country</option>
            <?php foreach ($countries as $c): ?>
                <option value="<?php echo (int)$c['COUNTRY_ID']; ?>"
                    <?php if ($country === (int)$c['COUNTRY_ID']) echo 'selected'; ?>>
                    <?php echo e($c['COUNTRY_NAME']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        Region:
        <select name="region">
            <option value="0">Any region</option>
            <?php foreach ($regions as $r): ?>
                <option value="<?php echo (int)$r['REGION_ID']; ?>"
                    <?php if ($region === (int)$r['REGION_ID']) echo 'selected'; ?>>
                    <?php echo e($r['REGION_NAME']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        Language:
        <select name="language">
            <option value="0">Any language</option>
            <?php foreach ($languages as $l): ?>
                <option value="<?php echo (int)$l['LANGUAGE_ID']; ?>"
                    <?php if ($language === (int)$l['LANGUAGE_ID']) echo 'selected'; ?>>
                    <?php echo e($l['LANGUAGE_NAME']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <input type="submit" value="Search">
        <a href="search.php">Clear</a>
    </form>
</div>

<?php if ($error !== ""): ?>
    <div class="gerror"><?php echo e($error); ?></div>
<?php elseif ($searched && count($users) === 0): ?>
    <p>No students found. Try a different filter.</p>
<?php elseif (count($users) > 0): ?>
    <div class="w3-section" id="search-results">
        <h3><?php echo count($users); ?> student(s) found</h3>
        <table class="w3-table w3-bordered">
            <tr>
                <th>Name</th>
                <th>Country</th>
                <th>Region</th>
                <th>Languages</th>
            </tr>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo e($u['first_name'] . ' ' . $u['last_name']); ?></td>
                    <td><?php echo e($u['COUNTRY_NAME']); ?></td>
                    <td><?php echo e($u['REGION_NAME']); ?></td>
                    <td><?php echo e($u['languages']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>

<BR><BR><BR><BR>

<?php



include "includes/footer.php";

?>