<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
session_start();

/*
US-05: As a user, I want to associate myself with additional countries so that
I can indidicate countries where I have lived, traveled, studied, or have
other connections.

This pages lets the logged in user see, add, and remove their countries.

- dropdown options and selections from database
- add/remove submit forms to manage_countries.php
*/

// check if the user is logged in
if (!isset($_SESSION["user"])) {
    $_SESSION["error"] = "You must be logged in.";
    header("Location: ../processes/login.php");
    exit;
}

require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";

// get user id
$userid = $_SESSION["user"]["userid"];

$message = $_SESSION["countries_message"] ?? "";
unset($_SESSION["countries_message"]);


// ------------------------------------------------------------
// Get all countries (for the dropdown)
// ------------------------------------------------------------
/*
$all_sql = "
    SELECT
        COUNTRY_ID,
        COUNTRY_NAME
    FROM wf_countries
    ORDER BY COUNTRY_NAME";

$allResults = WFDatabase::getDataFromSQL($all_sql);
*/

// ------------------------------------------------------------
// Get user's current countries
// ------------------------------------------------------------
$userResults = WFDatabase::getUserCountries($userid);

$selectedIds = array_column($userResults, "COUNTRY_ID");
 
include __DIR__ . "/../includes/functions.php";
include __DIR__ . "/../includes/header.php";
include __DIR__ . "/../includes/navbar.php";
?>
 
<h1>Countries of Interest</h1>
 
<?php if ($message !== ""): ?>
 
    <p><b><?= htmlspecialchars($message) ?></b></p>
 
<?php endif; ?>

<!-- ========================================================
     CURRENT COUNTRIES OF INTEREST
========================================================= -->
<h2>Your Countries of Interest</h2>

<?php if (!empty($userResults)): ?>
    <?php foreach ($userResults as $item): ?>
        <form
            method="POST"
            action="../processes/manage_countries.php"
            style="margin-bottom: 5px;"
            >

            <input type="hidden" name="action" value="remove">

            <input
                type="hidden"
                name="country_id"
                value="<?= htmlspecialchars($item['COUNTRY_ID']) ?>"
            >
 
            <?= htmlspecialchars($item['COUNTRY_NAME']) ?>

            <button type="submit">Remove</button>

        </form>

    <?php endforeach; ?>

<?php else: ?>
    <p>No countries have been added.</p>

<?php endif; ?>


<!-- ========================================================
     ADD
========================================================= -->

<h2>Add a Country</h2>

<form method="POST" action="../processes/manage_countries.php">

    <input type="hidden" name="action" value="add">

    <!-- filled in by the script below, from api/countries/ -->
    <select id="picker" name="country_id" required>
        <option value="">Loading...</option>
    </select>

    <button type="submit">Add</button>

</form>

<script>

// dropdown options from rest api
const API_URL = "../../api/countries/";
const selectedIds = <?= json_encode(array_map('strval', $selectedIds)) ?>;
const picker = document.getElementById("picker");

fetch(API_URL)
    .then(response => response.json())
    .then(payload => {

        picker.innerHTML = "";

        const placeholder = document.createElement("option");
        placeholder.value = "";
        placeholder.textContent = "-- Select a country --";
        picker.appendChild(placeholder);

        // payload looks like {success, count, data: [ {COUNTRY_ID, COUNTRY_NAME, ...}, ... ]}
        payload.data.forEach(item => {

            // skip ones the user already has
            if (selectedIds.includes(String(item.COUNTRY_ID))) {
                return;
            }

            const option = document.createElement("option");
            option.value = item.COUNTRY_ID;
            option.textContent = item.COUNTRY_NAME;
            picker.appendChild(option);
        });
    })
    .catch(() => {
        picker.innerHTML = "<option value=''>Could not load the list</option>";
    });
</script>

<br>
 
<a href="../profile.php">Back to My Profile</a>
 
<?php include __DIR__ . "/../includes/footer.php"; ?>