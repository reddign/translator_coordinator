<?php
$pageName = "Connor Price's Webpage";
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
        <section id="Connor Price's Bio">
            <div class="w3-card w3-container">
                <div class="w3-panel w3-blue">
                    <h2>Connor T. Price</h2>
                </div>
            
                <div id="w3-article"> <a href="https://github.com/conpri">Connor Price</a> is a sophomore student in Software Engineering. He is majoring in Computer Science, with no concentration, and intends to graduate in 2028.
                    <BR><BR>
                    His hobbies include watching sports and playing video games.
                    <BR><BR>
                
                </div>
            </div>
        </section>
    </main>
</body>
<?php
     require_once __DIR__ . "/../includes/footer.php";
?>
