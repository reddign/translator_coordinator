<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
session_start();

require_once __DIR__ . "/processes/authenticate.php";
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/WFDatabase.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";

// Check if the user is logged in
if (!isset($_SESSION["user"])) {
    $_SESSION["error"] = "";
    header("Location:..\web\processes\login.php");
    exit;
}

/*
------------------------------------------------------------
Read form values.
------------------------------------------------------------
*/
$page = $_GET["page"] ?? "profile";

/*
------------------------------------------------------------
Basic validation.
------------------------------------------------------------
*/
if (
    $page !== "profile" &&
    $page !== "login" &&
    $page !== "register"
) {
    $_SESSION["error"] = "Invalid page request.";
    header("Location: ../login.php?page=login");
    exit;
}

$error = $_SESSION["error"] ?? "";

/*
------------------------------------------------------------
Display different content based on the page.
------------------------------------------------------------
*/
if ($page == "login") {

    require_once "forms/loginForm.php";

} elseif ($page == "register") {

    require_once "forms/registrationForm.php";

} else {

    $profile_data = $_SESSION["user"];

    /*
    ------------------------------------------------------------
    Get current user's ID
    ------------------------------------------------------------
    */
    $userid = $profile_data["userid"];


    /*
    ------------------------------------------------------------
    Get Bio
    ------------------------------------------------------------
    */
    $bio_sql = "
        SELECT bio
        FROM users
        WHERE userid = :userid
    ";

    $bio_result = WFDatabase::getDataFromSQL(
        $bio_sql,
        [
            ":userid" => $userid
        ]
    );

    $bio = $bio_result[0]["bio"] ?? "";


    /*
    ------------------------------------------------------------
    Get Country of Origin
    ------------------------------------------------------------
    */
    $country_origin_sql = "
        SELECT COUNTRY_NAME
        FROM wf_countries
        WHERE COUNTRY_ID = :country_id
    ";

    $country_origin_result = WFDatabase::getDataFromSQL(
        $country_origin_sql,
        [
            ":country_id" => $profile_data["original_country_id"]
        ]
    );

    $country_of_origin = $country_origin_result[0]["COUNTRY_NAME"] ?? "";


    /*
    ------------------------------------------------------------
    Get Countries of Interest
    ------------------------------------------------------------
    */
    $country_interest_sql = "
        SELECT
            c.COUNTRY_NAME
        FROM user_country_interests uci
        JOIN wf_countries c
            ON uci.country_id = c.COUNTRY_ID
        WHERE uci.userid = :userid
        ORDER BY c.COUNTRY_NAME
    ";

    $countries = WFDatabase::getDataFromSQL(
        $country_interest_sql,
        [
            ":userid" => $userid
        ]
    );


    /*
    ------------------------------------------------------------
    Main column data
    ------------------------------------------------------------
    */

    echo "<div style='float:left'>";

    echo "<h1>User Profile</h1>";

    echo "<b>Name:</b> "
        . htmlspecialchars($profile_data["first_name"])
        . " "
        . htmlspecialchars($profile_data["last_name"])
        . "<BR>";

    echo "<b>Country of Origin:</b> "
        . htmlspecialchars($country_of_origin)
        . "<BR>";

    echo "<b>Bio:</b> "
        . htmlspecialchars($bio)
        . "<BR>";

    echo "<b>Email:</b> "
        . "<a href='mailto:"
        . htmlspecialchars($profile_data["email"])
        . "'>"
        . htmlspecialchars($profile_data["email"])
        . "</a><BR>";

    echo "<b>Role:</b> "
        . htmlspecialchars($profile_data["role"])
        . "<BR>";

    echo "<b>Member Since:</b> "
        . htmlspecialchars($profile_data["date_registered"])
        . "<BR><BR><BR>";

    /*
    ------------------------------------------------------------
    Edit Profile
    ------------------------------------------------------------
    */

    echo "<a href='update_profile.php'>";
    echo "<button type='button'>Edit Profile</button>";
    echo "</a>";


    echo "<BR><BR>";

    /*
    ------------------------------------------------------------
    People
    ------------------------------------------------------------
    */

    echo "<a href='people.php'>";
    echo "<button type='button'>View Other Users</button>";
    echo "</a>";

    echo "</div>";


    /*
    ------------------------------------------------------------
    Log Out
    ------------------------------------------------------------
    */

    echo "<a href='processes/logout.php'>Log out</a>";


    /*
    ============================================================
    SIDE COLUMN
    ============================================================
    */

    echo "<div style='float:right;margin-right:50px;'>";


    /*
    ------------------------------------------------------------
    Spoken Languages
    ------------------------------------------------------------
    */

    echo "<h2>Spoken Languages</h2>";

    $sql = "
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
        $sql,
        [
            ":userid" => $userid
        ]
    );

    if (!empty($languages)) {

        echo "<table border='1' cellpadding='5'>";

        echo "<tr>";
        echo "<th>Language</th>";
        echo "<th>Proficiency Level</th>";
        echo "</tr>";

        foreach ($languages as $language) {

            echo "<tr>";

            echo "<td>"
                . htmlspecialchars($language["LANGUAGE_NAME"])
                . "</td>";

            echo "<td>"
                . htmlspecialchars($language["proficency_level"])
                . "</td>";

            echo "</tr>";
        }

        echo "</table>";

    } else {

        echo "No spoken languages added.";

    }


    echo "<BR><BR><BR>";


    /*
    ------------------------------------------------------------
    Countries of Interest
    ------------------------------------------------------------
    */

    echo "<h2>Countries of Interest</h2>";

    if (!empty($countries)) {

        echo "<table border='1' cellpadding='5'>";

        echo "<tr>";
        echo "<th>Country</th>";
        echo "</tr>";

        foreach ($countries as $country) {

            echo "<tr>";

            echo "<td>"
                . htmlspecialchars($country["COUNTRY_NAME"])
                . "</td>";

            echo "</tr>";
        }

        echo "</table>";

    } else {

        echo "No countries of interest added.";

    }


    echo "<BR><BR><BR>";


    /*
    ------------------------------------------------------------
    Groups
    ------------------------------------------------------------
    */

    echo "<h2>Groups</h2>";

    $sql = "
        SELECT
            g.group_name,
            gm.joinedOn,
            gm.approved,
            gm.roleid
        FROM group_members gm
        JOIN groups g
            ON gm.groupid = g.groupid
        WHERE gm.userid = :userid
        ORDER BY g.group_name
    ";

    $groups = WFDatabase::getDataFromSQL(
        $sql,
        [
            ":userid" => $userid
        ]
    );

    if (!empty($groups)) {

        echo "<table border='1' cellpadding='5'>";

        echo "<tr>";
        echo "<th>Group</th>";
        echo "<th>Joined On</th>";
        echo "<th>Approved</th>";
        echo "<th>Role ID</th>";
        echo "</tr>";

        foreach ($groups as $group) {

            echo "<tr>";

            echo "<td>"
                . htmlspecialchars($group["group_name"])
                . "</td>";

            echo "<td>"
                . htmlspecialchars($group["joinedOn"] ?? "")
                . "</td>";

            echo "<td>"
                . htmlspecialchars($group["approved"] ?? "")
                . "</td>";

            echo "<td>"
                . htmlspecialchars($group["roleid"] ?? "")
                . "</td>";

            echo "</tr>";
        }

        echo "</table>";

    } else {

        echo "You are not currently a member of any groups.";

    }


    echo "<BR><BR><BR>";

    echo "</div>";
}

include "includes/footer.php";
?>