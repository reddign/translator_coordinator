<?php
$pageName = "Olha Kuptsova's Webpage";
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
        <section id="Olha Kuptsova's Bio">
            <div class="w3-card w3-container">
                <div class="w3-panel w3-blue">
                    <h2>Olha Kuptsova</h2>
                </div>
            
                <div id="w3-article"> Olha Kuptsova is a student in this class. She is majoring in Computer Science, 
                    with a concentration in Cybersecurity, without a minor, and is a part of the class of 2026, but intends to graduate in 2028.
                    <BR><BR>
                </div>
            </div>
        </section>
    </main>
</body>
<?php
     require_once __DIR__ . "/../includes/footer.php";
?>