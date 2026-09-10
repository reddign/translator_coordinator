<?php
$pageName = "Isaac Widders's Webpage";
require_once __DIR__ . "/../includes/functions.php";
$url = url();
$url = str_replace("/studentpages"," ", $url);
require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/navbar.php";

?>
   
    <div class="buttons">
        <a href="<?PHP echo $url;?>/about.php"><button class="button button2">Go Back!</button></a> 
    </div>
    <BR><BR>    
    <main>
        <section id="Isaac Widders's Bio">
            <div class="w3-card w3-container">
                <div class="w3-panel w3-blue">
                    <h2>Isaac Widders</h2>
                </div>
            
<<<<<<< HEAD
                <div id="w3-article"> Isaac Widders is a student in this class. He is majoring in Computer Science, with a concentration in Software Development, and intends to graduate in 2029. make an issue
                    I really hope this makes a conflict
=======
                <div id="w3-article"> Isaac Widders is a student in this class. He is majoring in Computer Science, with a concentration in Software Development, and intends to graduate in 2029.
>>>>>>> 35a4c723cc8706efd32dcbff6dd6de148ce86d69
                    <BR><BR>
                </div>
            </div>
        </section>
    </main>
</body>
<?php
     require_once __DIR__ . "/../includes/footer.php";
?>