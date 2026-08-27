<?php
$pageName = "Dominic Traina's Webpage";
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
        <section id="Dominic Traina's Bio">
            <div class="w3-card w3-container">
                <div class="w3-panel w3-blue">
                    <h2>Dominic Traina</h2>
                </div>
            
                <div id="w3-article"> Dominic Traina is a current student at Elizabethtown College, 
                    majoring in Computer Science with a concentration in AI & Data Science, 
                    and intends to graduate in 2028. He is planning on pursuing a career in sports data science.
                    <BR><BR>
                </div>
            </div>
        </section>
    </main>
</body>
<?php
     require_once __DIR__ . "/../includes/footer.php";
?>