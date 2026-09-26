<?php
/*
------------------------------------------------------------
Person Profile Page
------------------------------------------------------------

Displays another user's public profile.

URL:
    person.php?userid=2

Displays:
    - Name
    - Country of Origin
    - Bio
    - Spoken Languages
    - Countries of Interest
    - Groups

Does NOT display:
    - Password
    - Private account information
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
// Get the person ID from the URL
// ------------------------------------------------------------

$personid = $_GET["userid"] ?? "";


// ------------------------------------------------------------
// Validate user ID
// ------------------------------------------------------------

if (!is_numeric($personid) || (int)$personid <= 0) {

    echo "<h1>Person Not Found</h1>";
    echo "<p>Invalid user ID.</p>";
    echo "<a href='people.php'>Back to People</a>";

    include "includes/footer.php";
    exit;
}

$personid = (int)$personid;


// ------------------------------------------------------------
// Get person's basic profile information
// ------------------------------------------------------------

$sql = "
    SELECT
        u.userid,
        u.first_name,
        u.last_name,
        u.email,
        u.bio,
        u.date_registered,
        u.original_country_id,
        c.COUNTRY_NAME AS country_of_origin
    FROM users u

    LEFT JOIN wf_countries c
        ON u.original_country_id = c.COUNTRY_ID

    WHERE u.userid = :userid
";

$personResults = WFDatabase::getDataFromSQL(
    $sql,
    [
        ":userid" => $personid
    ]
);


// ------------------------------------------------------------
// Make sure the user exists
// ------------------------------------------------------------

if (empty($personResults)) {

    echo "<h1>Person Not Found</h1>";
    echo "<p>The requested user could not be found.</p>";
    echo "<a href='people.php'>Back to People</a>";

    include "includes/footer.php";
    exit;
}

$person = $personResults[0];


// ------------------------------------------------------------
// Get spoken languages
// ------------------------------------------------------------

$language_sql = "
    SELECT
        l.LANGUAGE_NAME,
        usl.proficency_level
    FROM user_spoken_languages usl

    JOIN wf_languages l
        ON usl.language_id = l.LANGUAGE_ID

    WHERE usl.userid = :userid

    ORDER BY l.LANGUAGE_NAME
";

$languages = WFDatabase::getDataFromSQL(
    $language_sql,
    [
        ":userid" => $personid
    ]
);


// ------------------------------------------------------------
// Get countries of interest
// ------------------------------------------------------------

$country_sql = "
    SELECT
        c.COUNTRY_NAME
    FROM user_country_interests uci

    JOIN wf_countries c
        ON uci.country_id = c.COUNTRY_ID

    WHERE uci.userid = :userid

    ORDER BY c.COUNTRY_NAME
";

$countries = WFDatabase::getDataFromSQL(
    $country_sql,
    [
        ":userid" => $personid
    ]
);

// ------------------------------------------------------------
// Get associated regions (US-06)
// ------------------------------------------------------------
$regions = WFDatabase::getUserRegions($personid);


// ------------------------------------------------------------
// Get groups
// ------------------------------------------------------------

$group_sql = "
    SELECT
        g.group_name,
        gm.approved
    FROM group_members gm

    JOIN groups g
        ON gm.groupid = g.groupid

    WHERE gm.userid = :userid

    ORDER BY g.group_name
";

$groups = WFDatabase::getDataFromSQL(
    $group_sql,
    [
        ":userid" => $personid
    ]
);

?>

<h1>
    <?= htmlspecialchars($person["first_name"]) ?>
    <?= htmlspecialchars($person["last_name"]) ?>
</h1>


<!-- ========================================================
     BASIC PROFILE
========================================================= -->

<h2>Profile</h2>

<p>

    <b>Country of Origin:</b>

    <?= htmlspecialchars(
        $person["country_of_origin"] ?? "Not specified"
    ) ?>

</p>


<p>

    <b>Bio:</b><br>

    <?php if (!empty($person["bio"])): ?>

        <?= nl2br(htmlspecialchars($person["bio"])) ?>

    <?php else: ?>

        No bio provided.

    <?php endif; ?>

</p>


<p>

    <b>Member Since:</b>

    <?= htmlspecialchars(
        $person["date_registered"] ?? "Unknown"
    ) ?>

</p>


<!-- ========================================================
     SPOKEN LANGUAGES
========================================================= -->

<h2>Spoken Languages</h2>

<?php if (!empty($languages)): ?>

    <table border="1" cellpadding="5" cellspacing="0">

        <tr>
            <th>Language</th>
            <th>Proficiency</th>
        </tr>

        <?php foreach ($languages as $language): ?>

            <tr>

                <td>
                    <?= htmlspecialchars(
                        $language["LANGUAGE_NAME"]
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $language["proficency_level"]
                    ) ?>
                    / 5
                </td>

            </tr>

        <?php endforeach; ?>

    </table>

<?php else: ?>

    <p>
        No spoken languages have been added.
    </p>

<?php endif; ?>


<!-- ========================================================
     COUNTRIES OF INTEREST
========================================================= -->

<h2>Countries of Interest</h2>

<?php if (!empty($countries)): ?>

    <ul>

        <?php foreach ($countries as $country): ?>

            <li>
                <?= htmlspecialchars(
                    $country["COUNTRY_NAME"]
                ) ?>
            </li>

        <?php endforeach; ?>

    </ul>

<?php else: ?>

    <p>
        No countries of interest have been added.
    </p>

<?php endif; ?>

<!-- ========================================================
     REGIONS OF INTEREST
========================================================= -->

<h2>Regions of Interest</h2>

<?php if (!empty($regions)): ?>

    <ul>
        <?php foreach ($regions as $region): ?>
            <li>
                <?= htmlspecialchars($region["REGION_NAME"]) ?>
            </li>
        <?php endforeach; ?>
        </ul>

<?php else: ?>
    <p>
        No regions of interest have been added.
    </p>

<?php endif; ?>

<!-- ========================================================
     GROUPS
========================================================= -->

<h2>Groups</h2>

<?php if (!empty($groups)): ?>

    <table border="1" cellpadding="5" cellspacing="0">

        <tr>
            <th>Group</th>
            <th>Status</th>
        </tr>

        <?php foreach ($groups as $group): ?>

            <tr>

                <td>
                    <?= htmlspecialchars(
                        $group["group_name"]
                    ) ?>
                </td>

                <td>

                    <?php if ($group["approved"] == "0"): ?>

                        Pending Approval

                    <?php else: ?>

                        Approved

                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

    </table>

<?php else: ?>

    <p>
        This person is not currently a member of any groups.
    </p>

<?php endif; ?>


<br><br>


<!-- ========================================================
     NAVIGATION
========================================================= -->

<a href="people.php">
    <button type="button">
        Back to People
    </button>
</a>

&nbsp;

<a href="profile.php">
    <button type="button">
        My Profile
    </button>
</a>


<?php

include "includes/footer.php";

?>
