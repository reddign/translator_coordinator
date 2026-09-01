<?php
require_once __DIR__ . "/../includes/config.php";

include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";

$page = $_GET["page"]??"login";

if($page=="login"){
    require_once "includes/loginForm.php";
}else{
    require_once "includes/registrationForm.php";
}


include "includes/footer.php";

?>