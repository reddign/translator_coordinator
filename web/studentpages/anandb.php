<?php
$pageName = "Anand Bum-Erdene's Webpage";
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
        <section id="Anand Bum-Erdene's Bio">
            <div class="w3-card w3-container">
                <div class="w3-panel w3-blue">
                    <h2>Anand Bum-Erdene</h2>
                </div>
            
                <div id="w3-article"> Anand Bum-Erdene is a student in this class majoring in Computer Science with a concentration of Cyber Security. Intends to graduate spring 2027 after graduation wants to find a job in Cyber Scurity.
                    <BR><BR>
                </div>
            </div>
        </section>
    </main>
</body>
<?php
     require_once __DIR__ . "/../includes/footer.php";
?>