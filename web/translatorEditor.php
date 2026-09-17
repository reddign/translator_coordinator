/**
This file will hold the translator editor page
This page will allow the user to edit their translation status and submit to update the database with their changes

*/

<?php
//add includes
include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";
include "includes/footer.php";

//Access the API to get data
$userDataUrl = $mainURL."/api/users/";
$userResponse = getJSONFromURL($userDataUrl);

//Framework
/**
 * USERNAME/ TITLE
<h1>Translator Editor</h1>
 * Add Languages
 * List
 * of
 * languages
 * (X on the end to remove)
 * Proficiency dropdown for each language
 * 
 * Add stars (1-5)
 */

?>