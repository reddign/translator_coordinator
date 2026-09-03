<?php
$pageName = "Brandon Short's Webpage";
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
        <section id="Brandon Short's Bio">
            <div class="w3-card w3-container">
                <div class="w3-panel w3-blue">
                    <h2>Brandon Short</h2>
                </div>
            
                <div id="w3-article"> Brandon Short is a student in this class. He is an engineering student, with a concentration in Computer 
                    Engineering and intends to graduate in the spring of 2027.
                    <BR><BR>
                </div>
            </div>
        </section>
    </main>
</body>
<?php
     require_once __DIR__ . "/../includes/footer.php";
?>