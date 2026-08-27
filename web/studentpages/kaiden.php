<?php
$pageName = "Kaiden's Webpage";
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
        <section id="Kaiden's Bio">
            <div class="w3-card w3-container">
                <div class="w3-panel w3-blue">
                    <h2>Kaiden</h2>
                </div>
            
                <div id="w3-article">
                    Hello World! Kaiden is a Computer Science student at Elizabethtown College with interests in software engineering, full-stack web development, and user-centered design.
                    He enjoys building applications that solve real problems and creating intuitive user experiences.
                    <BR>
                    <BR>
                    <a href="https://kaidenmiller06.github.io/" target="_blank">Portfolio</a> | <a href="https://linkedin.com/in/kaiden-miller" target="_blank">LinkedIn</a> | <a href="https://github.com/kaidenmiller06" target="_blank">GitHub</a>
                </div>
            </div>
        </section>
    </main>
</body>
<?php
     require_once __DIR__ . "/../includes/footer.php";
?>