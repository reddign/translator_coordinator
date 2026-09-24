<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
session_start();

/*
US-05: As a user, I want to associate myself with regions of the world
so that other users can discover people with regional experience
or interests.

This pages lets the logged in user see, add, and remove their regions.

- dropdown options and selections from database
- add/remove submit forms to manage_regions.php
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

$message = $_SESSION["regions_message"] ?? "";
unset($_SESSION["regions_message"]);


// ------------------------------------------------------------
// Get all regions (for the dropdown)
// ------------------------------------------------------------
$all_sql = "
    SELECT
        REGION_ID,
        REGION_NAME
    FROM wf_world_regions
    ORDER BY REGION_NAME";

$allResults = WFDatabase::getDataFromSQL($all_sql);

// ------------------------------------------------------------
// Get user's current regions
// ------------------------------------------------------------
$userResults = WFDatabase::getUserRegions($userid);

$selectedIds = array_column($userResults, "REGION_ID");
 
include __DIR__ . "/../includes/functions.php";
include __DIR__ . "/../includes/header.php";
include __DIR__ . "/../includes/navbar.php";
?>
 
<h1>Regions of Interest</h1>
 
<?php if ($message !== ""): ?>
 
    <p><b><?= htmlspecialchars($message) ?></b></p>
 
<?php endif; ?>

<!-- ========================================================
     CURRENT REGIONS OF INTEREST
========================================================= -->
<h2>Your Regions of Interest</h2>

<?php if (!empty($userResults)): ?>
    <?php foreach ($userResults as $item): ?>
        <form
            method="POST"
            action="../processes/manage_regions.php"
            style="margin-bottom: 5px;"
            >

            <input type="hidden" name="action" value="remove">

            <input
                type="hidden"
                name="region_id"
                value="<?= htmlspecialchars($item['REGION_ID']) ?>"
            >
 
            <?= htmlspecialchars($item['REGION_NAME']) ?>

            <button type="submit">Remove</button>

        </form>

    <?php endforeach; ?>

<?php else: ?>
    <p>No regions have been added.</p>

<?php endif; ?>


<!-- ========================================================
     ADD
========================================================= -->
 
<h2>Add a Region</h2>
 
<form method="POST" action="../processes/manage_regions.php">
 
    <input type="hidden" name="action" value="add">
 
    <select name="region_id" required>
 
        <option value="">-- Select a region --</option>
 
        <?php foreach ($allResults as $item): ?>
 
            <?php if (!in_array($item['REGION_ID'], $selectedIds)): ?>
 
                <option value="<?= htmlspecialchars($item['REGION_ID']) ?>">
                    <?= htmlspecialchars($item['REGION_NAME']) ?>
                </option>
 
            <?php endif; ?>
 
        <?php endforeach; ?>
 
    </select>
 
    <button type="submit">Add</button>
 
</form>
 
<br>
 
<a href="../profile.php">Back to My Profile</a>
 
<?php include __DIR__ . "/../includes/footer.php"; ?>