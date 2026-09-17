<?php
$pageName = "Mo Abouelnagah's Webpage";
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
        <section id="Mo Abouelnagah's Bio">
            <div class="w3-card w3-container">
                <div class="w3-panel w3-blue">
                    <h2>Mo Abouelnagah</h2>
                </div>
            
<<<<<<< HEAD
                <div id="w3-article"> Mo Abouelnagah is a student in this class. He is majoring in Computer Science with no concentration, and intends to graduate in 2028. Doesn't like painting and skiing           <BR><BR>
=======
                <div id="w3-article"> Mo Abouelnagah is a student in this class. He is majoring in Computer Science with no concentration, and intends to graduate in 2028.Likes painting and skiing.        <BR><BR>
>>>>>>> 9dc59abdd7d94645be9500d9b56b406e71e22430
                </div>
            </div>
        </section>
    </main>
</body>
<?php
     require_once __DIR__ . "/../includes/footer.php";
?>