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
    header("Location: ../forms/updateProfileForm.php");
    exit;
}


// ------------------------------------------------------------
// Get form values
// ------------------------------------------------------------

$first_name = trim($_POST["first_name"] ?? "");
$last_name = trim($_POST["last_name"] ?? "");
$bio = trim($_POST["bio"] ?? "");
$country_id = $_POST["country"] ?? "";

$spoken_languages = $_POST["spoken_languages"] ?? [];

$new_spoken_language = $_POST["new_spoken_language"] ?? "";
$new_language_proficiency = $_POST["new_language_proficiency"] ?? "";

$new_country_interest = $_POST["new_country_interest"] ?? "";

$new_group = $_POST["new_group"] ?? "";


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


// ------------------------------------------------------------
// Start transaction
// ------------------------------------------------------------

WFDatabase::startTransaction();

try {

    // ========================================================
    // UPDATE BASIC USER INFORMATION
    // ========================================================

    $sql = "
        UPDATE users
        SET
            first_name = :first_name,
            last_name = :last_name,
            bio = :bio,
            original_country_id = :country_id
        WHERE userid = :userid
    ";

    WFDatabase::executeSQL(
        $sql,
        [
            ":first_name" => $first_name,
            ":last_name" => $last_name,
            ":bio" => $bio,
            ":country_id" => $country_id,
            ":userid" => $userid
        ]
    );


    // ========================================================
    // UPDATE EXISTING SPOKEN LANGUAGES
    // ========================================================

    if (!empty($spoken_languages)) {

        // Get the user's existing language records.
        $existingLanguageSql = "
            SELECT id
            FROM user_spoken_languages
            WHERE userid = :userid
            ORDER BY id
        ";

        $existingLanguages = WFDatabase::getDataFromSQL(
            $existingLanguageSql,
            [
                ":userid" => $userid
            ]
        );


        foreach ($spoken_languages as $index => $language) {

            // Make sure there is a corresponding database row.
            if (!isset($existingLanguages[$index])) {
                continue;
            }

            $language_id = $language["language_id"] ?? "";
            $proficiency = $language["proficency_level"] ?? "";

            // Validate language.
            if (
                !is_numeric($language_id) ||
                (int)$language_id <= 0
            ) {
                continue;
            }

            // Validate proficiency.
            if (
                !is_numeric($proficiency) ||
                (int)$proficiency < 1 ||
                (int)$proficiency > 5
            ) {
                continue;
            }

            $language_id = (int)$language_id;
            $proficiency = (int)$proficiency;

            $language_record_id =
                (int)$existingLanguages[$index]["id"];


            // Update the specific user's language record.
            $updateLanguageSql = "
                UPDATE user_spoken_languages
                SET
                    language_id = :language_id,
                    proficency_level = :proficiency
                WHERE id = :id
                  AND userid = :userid
            ";

            WFDatabase::executeSQL(
                $updateLanguageSql,
                [
                    ":language_id" => $language_id,
                    ":proficiency" => $proficiency,
                    ":id" => $language_record_id,
                    ":userid" => $userid
                ]
            );
        }
    }


    // ========================================================
    // ADD NEW SPOKEN LANGUAGE
    // ========================================================

    if (
        $new_spoken_language !== "" &&
        $new_language_proficiency !== ""
    ) {

        if (
            is_numeric($new_spoken_language) &&
            is_numeric($new_language_proficiency)
        ) {

            $new_spoken_language = (int)$new_spoken_language;
            $new_language_proficiency =
                (int)$new_language_proficiency;


            if (
                $new_spoken_language > 0 &&
                $new_language_proficiency >= 1 &&
                $new_language_proficiency <= 5
            ) {

                // Check if the user already has this language.
                $checkLanguageSql = "
                    SELECT id
                    FROM user_spoken_languages
                    WHERE userid = :userid
                      AND language_id = :language_id
                    LIMIT 1
                ";

                $existingLanguage = WFDatabase::getDataFromSQL(
                    $checkLanguageSql,
                    [
                        ":userid" => $userid,
                        ":language_id" => $new_spoken_language
                    ]
                );


                // Only insert if it doesn't already exist.
                if (empty($existingLanguage)) {

                    $insertLanguageSql = "
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
                    ";

                    WFDatabase::executeSQL(
                        $insertLanguageSql,
                        [
                            ":userid" => $userid,
                            ":language_id" => $new_spoken_language,
                            ":proficiency" =>
                                $new_language_proficiency
                        ]
                    );
                }
            }
        }
    }


    // ========================================================
    // ADD COUNTRY OF INTEREST
    // ========================================================

    if (
        $new_country_interest !== "" &&
        is_numeric($new_country_interest)
    ) {

        $new_country_interest = (int)$new_country_interest;

        if ($new_country_interest > 0) {

            // Check for duplicate country.
            $checkCountrySql = "
                SELECT id
                FROM user_country_interests
                WHERE userid = :userid
                  AND country_id = :country_id
                LIMIT 1
            ";

            $existingCountry = WFDatabase::getDataFromSQL(
                $checkCountrySql,
                [
                    ":userid" => $userid,
                    ":country_id" => $new_country_interest
                ]
            );


            if (empty($existingCountry)) {

                $insertCountrySql = "
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
                ";

                WFDatabase::executeSQL(
                    $insertCountrySql,
                    [
                        ":userid" => $userid,
                        ":country_id" => $new_country_interest
                    ]
                );
            }
        }
    }


    // ========================================================
    // ADD GROUP
    // ========================================================

    if (
        $new_group !== "" &&
        is_numeric($new_group)
    ) {

        $new_group = (int)$new_group;

        if ($new_group > 0) {

            // Check if the user is already in this group.
            $checkGroupSql = "
                SELECT gm_record_id
                FROM group_members
                WHERE userid = :userid
                  AND groupid = :groupid
                LIMIT 1
            ";

            $existingGroup = WFDatabase::getDataFromSQL(
                $checkGroupSql,
                [
                    ":userid" => $userid,
                    ":groupid" => $new_group
                ]
            );


            if (empty($existingGroup)) {

                // New group memberships start as pending.
                $insertGroupSql = "
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
                ";

                WFDatabase::executeSQL(
                    $insertGroupSql,
                    [
                        ":groupid" => $new_group,
                        ":userid" => $userid
                    ]
                );
            }
        }
    }


    // ========================================================
    // COMMIT ALL CHANGES
    // ========================================================

    WFDatabase::commitTransaction();


    // --------------------------------------------------------
    // Refresh session user data
    // --------------------------------------------------------

    $updatedUserSql = "
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
    ";

    $updatedUser = WFDatabase::getDataFromSQL(
        $updatedUserSql,
        [
            ":userid" => $userid
        ]
    );


    if (!empty($updatedUser)) {
        $_SESSION["user"] = $updatedUser[0];
    }


    $_SESSION["error"] = "";

    // --------------------------------------------------------
    // Return to profile
    // --------------------------------------------------------

    header("Location: ../profile.php");
    exit;


} catch (Exception $e) {

    // --------------------------------------------------------
    // Something went wrong
    // --------------------------------------------------------

    WFDatabase::rollbackTransaction();

    $_SESSION["error"] =
        "Unable to update your profile: " . $e->getMessage();

    header("Location: ../profile.php");
    exit;
}

?>
