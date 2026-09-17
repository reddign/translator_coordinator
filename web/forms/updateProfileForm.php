<?php
/*
------------------------------------------------------------
Update Profile Form
------------------------------------------------------------

This file displays the form used to edit the user's profile.

The form submits to:
    processes/update_profile.php

User information and countries are loaded from the database.
------------------------------------------------------------
*/

session_start();

// Check if the user is logged in
if (!isset($_SESSION["user"])) {
    $_SESSION["error"] = "You must be logged in to update your profile.";
    header("Location: ../processes/login.php");
    exit;
}

// Get the logged-in user's ID from the session
$userid = $_SESSION["user"]["userid"];

// Database connection / database class
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/WFDatabase.php";

// ------------------------------------------------------------
// Get the user's current profile information
// ------------------------------------------------------------

$sql = "
    SELECT
        u.first_name,
        u.last_name,
        u.original_country_id,
        u.bio
    FROM users u
    WHERE u.userid = :userid
";

$userResults = WFDatabase::getDataFromSQL(
    $sql,
    [":userid" => $userid]
);

if (empty($userResults)) {
    die("User not found.");
}

$user = $userResults[0];

// ------------------------------------------------------------
// Get all countries
// ------------------------------------------------------------

$country_sql = "
    SELECT
        COUNTRY_ID,
        COUNTRY_NAME
    FROM wf_countries
    ORDER BY COUNTRY_NAME
";

$countryResults = WFDatabase::getDataFromSQL($country_sql);

// ------------------------------------------------------------
// Get all available languages
// ------------------------------------------------------------

$language_sql = "
    SELECT
        LANGUAGE_ID,
        LANGUAGE_NAME
    FROM wf_languages
    ORDER BY LANGUAGE_NAME
";

$languageResults = WFDatabase::getDataFromSQL($language_sql);

// ------------------------------------------------------------
// Get user's current spoken languages
// ------------------------------------------------------------

$user_language_sql = "
    SELECT
        usl.id,
        usl.language_id,
        usl.proficency_level,
        l.LANGUAGE_NAME
    FROM user_spoken_languages usl
    JOIN wf_languages l
        ON usl.language_id = l.LANGUAGE_ID
    WHERE usl.userid = :userid
    ORDER BY l.LANGUAGE_NAME
";

$userLanguageResults = WFDatabase::getDataFromSQL(
    $user_language_sql,
    [":userid" => $userid]
);

// ------------------------------------------------------------
// Get user's current countries of interest
// ------------------------------------------------------------

$user_country_sql = "
    SELECT
        uci.id,
        uci.country_id,
        c.COUNTRY_NAME
    FROM user_country_interests uci
    JOIN wf_countries c
        ON uci.country_id = c.COUNTRY_ID
    WHERE uci.userid = :userid
    ORDER BY c.COUNTRY_NAME
";

$userCountryResults = WFDatabase::getDataFromSQL(
    $user_country_sql,
    [":userid" => $userid]
);

// ------------------------------------------------------------
// Get all groups
// ------------------------------------------------------------

$group_sql = "
    SELECT
        groupid,
        group_name
    FROM groups
    ORDER BY group_name
";

$groupResults = WFDatabase::getDataFromSQL($group_sql);

// ------------------------------------------------------------
// Get groups the user already belongs to
// ------------------------------------------------------------

$user_group_sql = "
    SELECT
        gm.gm_record_id,
        gm.groupid,
        g.group_name,
        gm.approved,
        gm.joinedOn
    FROM group_members gm
    JOIN groups g
        ON gm.groupid = g.groupid
    WHERE gm.userid = :userid
    ORDER BY g.group_name
";

$userGroupResults = WFDatabase::getDataFromSQL(
    $user_group_sql,
    [":userid" => $userid]
);

?>

<h1>Update Profile</h1>

<form method="POST" action="processes/save_profile.php">

<input
    type="hidden"
    name="userid"
    value="<?= htmlspecialchars($userid) ?>"
>


<!-- First Name -->
<label for="first_name">First Name:</label><br>

<input
    type="text"
    id="first_name"
    name="first_name"
    value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
    required
>

<br><br>


<!-- Last Name -->
<label for="last_name">Last Name:</label><br>

<input
    type="text"
    id="last_name"
    name="last_name"
    value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
    required
>

<br><br>


<!-- Country of Origin -->
<label for="country">Country of Origin:</label><br>

<select id="country" name="country" required>

    <option value="">-- Select a Country --</option>
    
    <?php foreach ($countryResults as $country): ?>

        <option
            value="<?= htmlspecialchars($country['COUNTRY_ID']) ?>"
            <?= ($country['COUNTRY_ID'] == $user['original_country_id']) ? 'selected' : '' ?>
        >
            <?= htmlspecialchars($country['COUNTRY_NAME']) ?>
        </option>

    <?php endforeach; ?>

</select>

<br><br>


<!-- Bio -->
<label for="bio">About Me:</label><br>

<textarea
    id="bio"
    name="bio"
    rows="6"
    cols="50"
><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>

<br><br>


<!-- ========================================================
     SPOKEN LANGUAGES
========================================================= -->

<h2>Spoken Languages</h2>

<?php if (!empty($userLanguageResults)): ?>

    <?php foreach ($userLanguageResults as $index => $userLanguage): ?>

        <div style="margin-bottom: 10px;">

            <label>
                Language:
            </label>

            <select
                name="spoken_languages[<?= $index ?>][language_id]"
            >

                <option value="">-- Select Language --</option>

                <?php foreach ($languageResults as $language): ?>

                    <option
                        value="<?= htmlspecialchars($language['LANGUAGE_ID']) ?>"
                        <?= (
                            $language['LANGUAGE_ID']
                            == $userLanguage['language_id']
                        ) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($language['LANGUAGE_NAME']) ?>
                    </option>

                <?php endforeach; ?>

            </select>


            <label>
                Proficiency:
            </label>

            <select
                name="spoken_languages[<?= $index ?>][proficency_level]"
            >

                <option value="1"
                    <?= ($userLanguage['proficency_level'] == 1) ? 'selected' : '' ?>>
                    1
                </option>

                <option value="2"
                    <?= ($userLanguage['proficency_level'] == 2) ? 'selected' : '' ?>>
                    2
                </option>

                <option value="3"
                    <?= ($userLanguage['proficency_level'] == 3) ? 'selected' : '' ?>>
                    3
                </option>

                <option value="4"
                    <?= ($userLanguage['proficency_level'] == 4) ? 'selected' : '' ?>>
                    4
                </option>

                <option value="5"
                    <?= ($userLanguage['proficency_level'] == 5) ? 'selected' : '' ?>>
                    5
                </option>

            </select>

        </div>

    <?php endforeach; ?>
    //TODO <!-- Also add the ability to add multiple lanuages at once. -->

<?php else: ?>

    <p>No spoken languages have been added yet.</p>

<?php endif; ?>


<!-- Add a new spoken language -->

<h3>Add Spoken Language</h3>

<select name="new_spoken_language">

    <option value="">-- Select Language --</option>

    <?php foreach ($languageResults as $language): ?>

        <option
            value="<?= htmlspecialchars($language['LANGUAGE_ID']) ?>"
        >
            <?= htmlspecialchars($language['LANGUAGE_NAME']) ?>
        </option>

    <?php endforeach; ?>

</select>


<label for="new_language_proficiency">
    Proficiency:
</label>

<select
    id="new_language_proficiency"
    name="new_language_proficiency"
>

    <option value="">-- Select Level --</option>
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>

</select>

<br><br>


<!-- ========================================================
     COUNTRIES OF INTEREST
========================================================= -->

<h2>Countries of Interest</h2>

<?php if (!empty($userCountryResults)): ?>

    <?php foreach ($userCountryResults as $userCountry): ?>

        <div style="margin-bottom: 5px;">

            <?= htmlspecialchars($userCountry['COUNTRY_NAME']) ?>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <p>No countries of interest have been added yet.</p>

<?php endif; ?>


<!-- Add country -->

<h3>Add Country of Interest</h3>

<select name="new_country_interest">

    <option value="">-- Select a Country --</option>

    <?php foreach ($countryResults as $country): ?>

        <option
            value="<?= htmlspecialchars($country['COUNTRY_ID']) ?>"
        >
            <?= htmlspecialchars($country['COUNTRY_NAME']) ?>
        </option>

    <?php endforeach; ?>

</select>
    //TODO <!-- Also add the ability to add multiple countries at once. -->

<br><br>


<!-- ========================================================
     GROUPS
========================================================= -->

<h2>Groups</h2>

<?php if (!empty($userGroupResults)): ?>

    <h3>Your Current Groups</h3>

    <?php foreach ($userGroupResults as $userGroup): ?>

        <div style="margin-bottom: 5px;">

            <?= htmlspecialchars($userGroup['group_name']) ?>

            <?php if ($userGroup['approved'] == '0'): ?>

                <span> (Pending Approval)</span>

            <?php else: ?>

                <span> (Approved)</span>

            <?php endif; ?>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <p>You are not currently a member of any groups.</p>

<?php endif; ?>


<!-- Add group -->

<h3>Join a Group</h3>

<select name="new_group">

    <option value="">-- Select a Group --</option>

    <?php foreach ($groupResults as $group): ?>

        <option
            value="<?= htmlspecialchars($group['groupid']) ?>"
        >
            <?= htmlspecialchars($group['group_name']) ?>
        </option>

    <?php endforeach; ?>

</select>

<br><br>

    //TODO <!-- Also add the ability to add multiple groups at once. -->


<!-- ========================================================
     SUBMIT
========================================================= -->

<button type="submit">Save Profile</button>

</form>

<br>

<a href="../profile.php">Cancel</a>
