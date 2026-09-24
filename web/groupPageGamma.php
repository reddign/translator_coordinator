<?php

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

require_once dirname(__DIR__,1) . "/includes/config.php";
require_once dirname(__DIR__,1) . "/includes/WFDatabase.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";
?>
<form  method="get">
<label for="groupIDinput">Search by groupID (number only):</label>
  <input type="number" id="groupIDinput" name="groupIDinput" value="">
<button type="submit" >Search</button>
</form>

<script>
    document.querySelector("form").addEventListener("submit", function() {
        var searchString;
        var value = document.getElementById("groupIDinput").value;
        searchString = "../api/groups/" + value;
        document.querySelector("button[type='submit']").setAttribute("formaction", searchString);
    });
</script>

