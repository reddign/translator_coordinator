<?php
/*
------------------------------------------------------------
People Page
------------------------------------------------------------

This page displays other users in the system.

Future functionality could include:
    - Search users
    - Filter by spoken language
    - Filter by country of origin
    - Filter by countries of interest
    - Filter by groups
    - View individual user profiles
------------------------------------------------------------
*/

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

session_start();

require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";


// ------------------------------------------------------------
// Check if the user is logged in
// ------------------------------------------------------------

if (!isset($_SESSION["user"])) {

    $_SESSION["error"] = "You must be logged in.";

    header("Location: processes/login.php");
    exit;
}


// ------------------------------------------------------------
// Get current user's ID
// ------------------------------------------------------------

$userid = $_SESSION["user"]["userid"];


// ------------------------------------------------------------
// Get other users
// ------------------------------------------------------------

$sql = "
    SELECT
        u.userid,
        u.first_name,
        u.last_name,
        u.email,
        u.original_country_id,
        u.bio,
        u.date_registered,
        c.COUNTRY_NAME AS country_of_origin
    FROM users u

    LEFT JOIN wf_countries c
        ON u.original_country_id = c.COUNTRY_ID

    WHERE u.userid != :userid

    ORDER BY u.last_name, u.first_name
";

$people = WFDatabase::getDataFromSQL(
    $sql,
    [
        ":userid" => $userid
    ]
);

?>

<h1>People</h1>

<p>
    Browse other users in the Translator Coordinator system.
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
                    $person["country_of_origin"] ?? "Not specified"
                ) ?>

            </p>


            <!-- Bio -->

            <p>

                <b>Bio:</b>

                <?= htmlspecialchars(
                    $person["bio"] ?? "No bio provided."
                ) ?>

            </p>


            <!-- View Profile -->

            <a
                href="person.php?userid=<?= htmlspecialchars($person["userid"]) ?>"
            >
                <button type="button">
                    View Profile
                </button>
            </a>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <p>
        There are no other users to display.
    </p>

<?php endif; ?>


<br>

<a href="profile.php">
    Back to My Profile
</a>


<?php

include "includes/footer.php";

?>
