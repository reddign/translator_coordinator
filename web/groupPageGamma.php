<?php

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

require_once dirname(__DIR__,1) . "/includes/config.php";
require_once dirname(__DIR__,1) . "/includes/WFDatabase.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";
?>


/*
------------------------------------------------------------
This section is the group search interface.
Where you can connect to the api endpoint for searching groups by their GroupID or retrieve all groups.
- Isaac Widders 9/24/2026
------------------------------------------------------------
*/


<form  method="get">
<label for="groupIDinput">Search by groupID (number only):</label>
  <input type="number" id="groupIDinput" name="groupIDinput" value="">
<button type="submit">Search</button>
</form>

<script>
    document.querySelector("form").addEventListener("submit", function() {
        var searchString;
        var value = document.getElementById("groupIDinput").value;
        searchString = "../api/groups/" + value;
        document.querySelector("button[type='submit']").setAttribute("formaction", searchString);
    });
</script>



<!--Create Group button that sends user to createGroupForm.php to create a group.-->
<a href="/translator_coordinator/web/forms/createGroupForm.php" style="display: inline-block; padding: 10px 20px; 
background-color: #8ac4f6; color: white; text-decoration: none; border-radius: 5px;
font-weight: bold; position: absolute; top: 100px; right: 20px;">Create Group</a> 