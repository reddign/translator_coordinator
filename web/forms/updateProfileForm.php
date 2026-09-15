<?php
/*
------------------------------------------------------------
Update Profile Form
------------------------------------------------------------

This file displays the form used to edit the user's profile.

The form submits to:
    processes/update_profile.php

Database validation and updating will be handled there.
------------------------------------------------------------
*/

?>

<h1>Update Profile</h1>

<form method="POST" action="processes/update_profile.php">

    <!-- First Name -->
    <label for="first_name">First Name:</label><br>
    <input
        type="text"
        id="first_name"
        name="first_name"
        value=""
        required
    >
    <br><br>

    <!-- Last Name -->
    <label for="last_name">Last Name:</label><br>
    <input
        type="text"
        id="last_name"
        name="last_name"
        value=""
        required
    >
    <br><br>

    <!-- Country of Origin -->
    <label for="country">Country of Origin:</label><br>
    <select id="country" name="country" required>
        <option value="">-- Select a Country --</option>

        <!--
        TODO:
        Populate this list from the database.
        Example:

        <option value="1">United States</option>
        <option value="2">Canada</option>
        <option value="3">Mexico</option>
        -->
    </select>
    <br><br>

    <!-- Bio -->
    <label for="bio">About Me:</label><br>
    <textarea
        id="bio"
        name="bio"
        rows="6"
        cols="50"
    ></textarea>
    <br><br>

    <!-- Submit -->
    <button type="submit">Save Profile</button>

</form>

<br>

<a href="profile.php">Cancel</a>
