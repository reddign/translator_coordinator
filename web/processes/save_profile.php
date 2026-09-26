<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";

// ------------------------------------------------------------
// Check if the user is logged in
// ------------------------------------------------------------

if (!isset($_SESSION["user"])) {
    $_SESSION["error"] = "You must be logged in.";
    header("Location: login.php");
    exit;
}

// Get the user ID from the session.
// Do NOT trust the hidden userid field from the form.
$userid = $_SESSION["user"]["userid"];


// ------------------------------------------------------------
// Only allow POST requests
// ------------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../update_profile.php");
    exit;
}


// ------------------------------------------------------------
// Get form values
// ------------------------------------------------------------

$first_name = trim($_POST["first_name"] ?? "");
$last_name  = trim($_POST["last_name"] ?? "");
$bio        = trim($_POST["bio"] ?? "");
$country_id = $_POST["country"] ?? "";

// Existing language rows: each has id, language_id, proficency_level
$spoken_languages = $_POST["spoken_languages"] ?? [];

// New rows added with the "Add another" buttons
$new_languages = $_POST["new_languages"] ?? [];   // [ [language_id, proficency_level], ... ]
$new_countries = $_POST["new_countries"] ?? [];   // [ country_id, ... ]
$new_groups    = $_POST["new_groups"] ?? [];      // [ groupid, ... ]

// Guard against tampered input that isn't an array
if (!is_array($spoken_languages)) { $spoken_languages = []; }
if (!is_array($new_languages))    { $new_languages = []; }
if (!is_array($new_countries))    { $new_countries = []; }
if (!is_array($new_groups))       { $new_groups = []; }


// ------------------------------------------------------------
// Validate basic profile information
// ------------------------------------------------------------

if ($first_name === "" || $last_name === "") {
    $_SESSION["error"] = "First name and last name are required.";
    header("Location: ../profile.php");
    exit;
}

if (!is_numeric($country_id) || (int)$country_id <= 0) {
    $_SESSION["error"] = "Please select a valid country.";
    header("Location: ../profile.php");
    exit;
}

$country_id = (int)$country_id;

// Confirm the country of origin exists (and get its name for the message)
$countryCheck = WFDatabase::getDataFromSQL(
    "SELECT COUNTRY_ID, COUNTRY_NAME FROM wf_countries WHERE COUNTRY_ID = :country_id",
    [":country_id" => $country_id]
);

if (empty($countryCheck)) {
    $_SESSION["error"] = "Please select a valid country.";
    header("Location: ../profile.php");
    exit;
}

$country_name = $countryCheck[0]["COUNTRY_NAME"];

// What is saved right now? Used to tell whether the country changed.
$currentRow = WFDatabase::getDataFromSQL(
    "SELECT original_country_id FROM users WHERE userid = :userid",
    [":userid" => $userid]
);

$country_changed = (
    empty($currentRow) ||
    (int)$currentRow[0]["original_country_id"] !== $country_id
);

$is_new_profile = (
    empty($currentRow) ||
    empty($currentRow[0]["original_country_id"])
);

// ------------------------------------------------------------
// Validate existing spoken-language rows up front
// (no language may be chosen twice)
// ------------------------------------------------------------

$submittedLanguageIds = [];

foreach ($spoken_languages as $language) {
    $lid = $language["language_id"] ?? "";
    if (is_numeric($lid) && (int)$lid > 0) {
        $submittedLanguageIds[] = (int)$lid;
    }
}

if (count($submittedLanguageIds) !== count(array_unique($submittedLanguageIds))) {
    $_SESSION["error"] = "You selected the same spoken language more than once.";
    header("Location: ../profile.php");
    exit;
}


// ------------------------------------------------------------
// Start transaction
// ------------------------------------------------------------

WFDatabase::startTransaction();

try {

    // ========================================================
    // UPDATE BASIC USER INFORMATION
    // ========================================================

    WFDatabase::executeSQL(
        "
        UPDATE users
        SET
            first_name = :first_name,
            last_name = :last_name,
            bio = :bio,
            original_country_id = :country_id
        WHERE userid = :userid
        ",
        [
            ":first_name" => $first_name,
            ":last_name"  => $last_name,
            ":bio"        => $bio,
            ":country_id" => $country_id,
            ":userid"     => $userid
        ]
    );


    // ========================================================
    // UPDATE EXISTING SPOKEN LANGUAGES
    // Rows are matched by their database id (hidden form field),
    // and always scoped to the logged-in user.
    // ========================================================

    foreach ($spoken_languages as $language) {

        $record_id   = $language["id"] ?? "";
        $language_id = $language["language_id"] ?? "";
        $proficiency = $language["proficency_level"] ?? "";

        if (!is_numeric($record_id) || (int)$record_id <= 0) {
            continue;
        }

        if (!is_numeric($language_id) || (int)$language_id <= 0) {
            continue;
        }

        if (
            !is_numeric($proficiency) ||
            (int)$proficiency < 1 ||
            (int)$proficiency > 5
        ) {
            continue;
        }

        $record_id   = (int)$record_id;
        $language_id = (int)$language_id;
        $proficiency = (int)$proficiency;

        // Language must exist in wf_languages
        $languageCheck = WFDatabase::getDataFromSQL(
            "SELECT LANGUAGE_ID FROM wf_languages WHERE LANGUAGE_ID = :language_id",
            [":language_id" => $language_id]
        );

        if (empty($languageCheck)) {
            continue;
        }

        WFDatabase::executeSQL(
            "
            UPDATE user_spoken_languages
            SET
                language_id = :language_id,
                proficency_level = :proficiency
            WHERE id = :id
              AND userid = :userid
            ",
            [
                ":language_id" => $language_id,
                ":proficiency" => $proficiency,
                ":id"          => $record_id,
                ":userid"      => $userid
            ]
        );
    }


    // ========================================================
    // ADD NEW SPOKEN LANGUAGES (multiple)
    // ========================================================

    foreach ($new_languages as $row) {

        if (!is_array($row)) {
            continue;
        }

        $language_id = $row["language_id"] ?? "";
        $proficiency = $row["proficency_level"] ?? "";

        // Blank rows are ignored
        if ($language_id === "" || $proficiency === "") {
            continue;
        }

        if (!is_numeric($language_id) || !is_numeric($proficiency)) {
            continue;
        }

        $language_id = (int)$language_id;
        $proficiency = (int)$proficiency;

        if ($language_id <= 0 || $proficiency < 1 || $proficiency > 5) {
            continue;
        }

        // Language must exist in wf_languages
        $languageCheck = WFDatabase::getDataFromSQL(
            "SELECT LANGUAGE_ID FROM wf_languages WHERE LANGUAGE_ID = :language_id",
            [":language_id" => $language_id]
        );

        if (empty($languageCheck)) {
            continue;
        }

        // Skip if the user already has this language
        // (also catches duplicates within the same submission)
        $existingLanguage = WFDatabase::getDataFromSQL(
            "
            SELECT id
            FROM user_spoken_languages
            WHERE userid = :userid
              AND language_id = :language_id
            LIMIT 1
            ",
            [
                ":userid"      => $userid,
                ":language_id" => $language_id
            ]
        );

        if (!empty($existingLanguage)) {
            continue;
        }

        WFDatabase::executeSQL(
            "
            INSERT INTO user_spoken_languages
            (
                userid,
                language_id,
                proficency_level
            )
            VALUES
            (
                :userid,
                :language_id,
                :proficiency
            )
            ",
            [
                ":userid"      => $userid,
                ":language_id" => $language_id,
                ":proficiency" => $proficiency
            ]
        );
    }


    // ========================================================
    // ADD NEW COUNTRIES OF INTEREST (multiple)
    // ========================================================

    $countryIds = [];

    foreach ($new_countries as $value) {
        if (is_numeric($value) && (int)$value > 0) {
            $countryIds[] = (int)$value;
        }
    }

    $countryIds = array_unique($countryIds);

    foreach ($countryIds as $interest_id) {

        // Country must exist in wf_countries
        $countryExists = WFDatabase::getDataFromSQL(
            "SELECT COUNTRY_ID FROM wf_countries WHERE COUNTRY_ID = :country_id",
            [":country_id" => $interest_id]
        );

        if (empty($countryExists)) {
            continue;
        }

        // Skip if the user already has this country of interest
        $existingCountry = WFDatabase::getDataFromSQL(
            "
            SELECT id
            FROM user_country_interests
            WHERE userid = :userid
              AND country_id = :country_id
            LIMIT 1
            ",
            [
                ":userid"     => $userid,
                ":country_id" => $interest_id
            ]
        );

        if (!empty($existingCountry)) {
            continue;
        }

        WFDatabase::executeSQL(
            "
            INSERT INTO user_country_interests
            (
                userid,
                country_id
            )
            VALUES
            (
                :userid,
                :country_id
            )
            ",
            [
                ":userid"     => $userid,
                ":country_id" => $interest_id
            ]
        );
    }


    // ========================================================
    // ADD NEW GROUP REQUESTS (multiple)
    // ========================================================

    $groupIds = [];

    foreach ($new_groups as $value) {
        if (is_numeric($value) && (int)$value > 0) {
            $groupIds[] = (int)$value;
        }
    }

    $groupIds = array_unique($groupIds);

    foreach ($groupIds as $group_id) {

        // Group must exist
        $groupExists = WFDatabase::getDataFromSQL(
            "SELECT groupid FROM groups WHERE groupid = :groupid",
            [":groupid" => $group_id]
        );

        if (empty($groupExists)) {
            continue;
        }

        // Skip if the user is already in (or has requested) this group
        $existingGroup = WFDatabase::getDataFromSQL(
            "
            SELECT gm_record_id
            FROM group_members
            WHERE userid = :userid
              AND groupid = :groupid
            LIMIT 1
            ",
            [
                ":userid"  => $userid,
                ":groupid" => $group_id
            ]
        );

        if (!empty($existingGroup)) {
            continue;
        }

        // New group memberships start as pending.
        WFDatabase::executeSQL(
            "
            INSERT INTO group_members
            (
                groupid,
                userid,
                joinedOn,
                approved
            )
            VALUES
            (
                :groupid,
                :userid,
                NOW(),
                '0'
            )
            ",
            [
                ":groupid" => $group_id,
                ":userid"  => $userid
            ]
        );
    }


    // ========================================================
    // COMMIT ALL CHANGES
    // ========================================================

    WFDatabase::commitTransaction();


    // --------------------------------------------------------
    // Refresh session user data
    // --------------------------------------------------------

    $updatedUser = WFDatabase::getDataFromSQL(
        "
        SELECT
            u.userid,
            u.email,
            u.first_name,
            u.last_name,
            u.date_registered,
            u.original_country_id,
            u.role,
            c.COUNTRY_NAME AS original_country_name
        FROM users u
        LEFT JOIN wf_countries c
            ON u.original_country_id = c.COUNTRY_ID
        WHERE u.userid = :userid
        ",
        [":userid" => $userid]
    );

    if (!empty($updatedUser)) {
        $_SESSION["user"] = $updatedUser[0];
    }


    // --------------------------------------------------------
    // Confirmation message (shown by profile.php)
    // --------------------------------------------------------

    // <-- replace the old success-message lines with this block
    $_SESSION["error"] = "";

    if ($is_new_profile) {
        $_SESSION["success"] = "Profile created successfully. Your country of origin is "
            . $country_name . ".";
    } elseif ($country_changed) {
        $_SESSION["success"] = "Profile updated successfully. Your country of origin is now "
            . $country_name . ".";
    } else {
        $_SESSION["success"] = "Profile updated successfully.";
    }

    header("Location: ../profile.php");
    exit;


} catch (Exception $e) {

    // --------------------------------------------------------
    // Something went wrong: undo everything
    // --------------------------------------------------------

    WFDatabase::rollbackTransaction();

    $_SESSION["success"] = "";
    $_SESSION["error"] =
        "Unable to update your profile: " . $e->getMessage();

    header("Location: ../profile.php");
    exit;
}

?>
