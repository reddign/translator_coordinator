<?php
/*
------------------------------------------------------------
People Page
------------------------------------------------------------

This page displays other users in the system and lets the
logged-in user filter them by country of origin.

Future functionality could include:
    - Search users by name
    - Filter by spoken language
    - Filter by countries of interest
    - Filter by groups
------------------------------------------------------------
*/

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

session_start();

require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";


// ------------------------------------------------------------
// Check if the user is logged in
// (must happen BEFORE any HTML is output, or header() fails)
// ------------------------------------------------------------

if (!isset($_SESSION["user"])) {

    $_SESSION["error"] = "You must be logged in.";

    header("Location: processes/login.php");
    exit;
}

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";


// ------------------------------------------------------------
// Get current user's ID
// ------------------------------------------------------------

$userid = $_SESSION["user"]["userid"];


// ------------------------------------------------------------
// Read and validate the country filter
// ------------------------------------------------------------

$selected_country = $_GET["country"] ?? "";
$filter_error = "";

if ($selected_country !== "") {

    if (!ctype_digit((string)$selected_country) || (int)$selected_country <= 0) {

        $filter_error = "Invalid country selected. Showing all users.";
        $selected_country = "";

    } else {

        $selected_country = (int)$selected_country;

        // Confirm the country actually exists
        $countryCheck = WFDatabase::getDataFromSQL(
            "SELECT COUNTRY_ID FROM wf_countries WHERE COUNTRY_ID = :country_id",
            [":country_id" => $selected_country]
        );

        if (empty($countryCheck)) {
            $filter_error = "That country was not found. Showing all users.";
            $selected_country = "";
        }
    }
}


// ------------------------------------------------------------
// Get countries for the filter dropdown
// (only countries that at least one other user is from)
// ------------------------------------------------------------

$filterCountries = WFDatabase::getDataFromSQL(
    "
    SELECT DISTINCT
        c.COUNTRY_ID,
        c.COUNTRY_NAME
    FROM users u
    JOIN wf_countries c
        ON u.original_country_id = c.COUNTRY_ID
    WHERE u.userid != :userid
    ORDER BY c.COUNTRY_NAME
    ",
    [":userid" => $userid]
);


// ------------------------------------------------------------
// Get other users (optionally filtered by country of origin)
// ------------------------------------------------------------

$sql = "
    SELECT
        u.userid,
        u.first_name,
        u.last_name,
        u.original_country_id,
        u.bio,
        u.date_registered,
        c.COUNTRY_NAME AS country_of_origin
    FROM users u

    LEFT JOIN wf_countries c
        ON u.original_country_id = c.COUNTRY_ID

    WHERE u.userid != :userid
";

$params = [":userid" => $userid];

if ($selected_country !== "") {
    $sql .= " AND u.original_country_id = :country_id";
    $params[":country_id"] = $selected_country;
}

$sql .= " ORDER BY u.last_name, u.first_name";

$people = WFDatabase::getDataFromSQL($sql, $params);

?>

<h1>People</h1>

<p>
    Browse other users in the Translator Coordinator system.
</p>


<!-- ========================================================
     FILTER
========================================================= -->

<?php if ($filter_error !== ""): ?>
    <p style="color: red;">
        <?= htmlspecialchars($filter_error) ?>
    </p>
<?php endif; ?>

<form method="GET" action="people.php" style="margin-bottom: 20px;">

    <label for="country">Country of Origin:</label>

    <select id="country" name="country">

        <option value="">All Countries</option>

        <?php foreach ($filterCountries as $country): ?>

            <option
                value="<?= htmlspecialchars($country["COUNTRY_ID"]) ?>"
                <?= ($country["COUNTRY_ID"] == $selected_country) ? "selected" : "" ?>
            >
                <?= htmlspecialchars($country["COUNTRY_NAME"]) ?>
            </option>

        <?php endforeach; ?>

    </select>

    <button type="submit">Filter</button>

    <?php if ($selected_country !== ""): ?>
        <a href="people.php">Clear filter</a>
    <?php endif; ?>

</form>

<p>
    <?= count($people) ?>
    <?= count($people) === 1 ? "person" : "people" ?> found.
</p>


<!-- ========================================================
     USER LIST
========================================================= -->

<?php if (!empty($people)): ?>

    <?php foreach ($people as $person): ?>

        <div
            style="
                border: 1px solid #ccc;
                padding: 15px;
                margin-bottom: 15px;
                width: 500px;
            "
        >

            <!-- Name -->

            <h2>
                <?= htmlspecialchars($person["first_name"]) ?>
                <?= htmlspecialchars($person["last_name"]) ?>
            </h2>


            <!-- Country -->

            <p>

                <b>Country of Origin:</b>

                <?= htmlspecialchars(
                    $person["country_of_origin"] ?: "Not specified"
                ) ?>

            </p>


            <!-- Bio -->

            <p>

                <b>Bio:</b>

                <?= nl2br(htmlspecialchars(
                    $person["bio"] ?: "No bio provided."
                )) ?>

            </p>


            <!-- View Profile -->

            <a
                href="person.php?userid=<?= (int)$person["userid"] ?>"
            >
                <button type="button">
                    View Profile
                </button>
            </a>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <p>
        <?php if ($selected_country !== ""): ?>
            No users found for that country.
        <?php else: ?>
            There are no other users to display.
        <?php endif; ?>
    </p>

<?php endif; ?>


<br>

<a href="profile.php">
    Back to My Profile
</a>


<?php

include "includes/footer.php";

?>
