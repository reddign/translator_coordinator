<?php
include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";

// Check if the user is logged in
if (!isset($_SESSION["user"])) {
    $_SESSION["error"] = "";
    header("Location:..\web\processes\login.php");
    exit;
}

?>

<h2>Translator Coordinator - Search Feature</h2>
       <div class="w3-section" id="team-names">
            <span class="name">Once completed you will be able to search for translators by language, country or region.
                You will also be able to search for groups to join that have interest in the language you are learning.
            </span>
        </div>
        <h3>Stay tuned for more updates.</h3>
        </div>

 
        
        <BR><BR><BR><BR>

<?php



include "includes/footer.php";

?>