<?php

class PageContent{

    static function render($type){
        switch($type){
            case "about":
                $html = PageContent::getClassPhoto();
                break;
            case "map":
                $html = PageContent::getMap();
                break;
            default:
               $html = PageContent::getMap();

        }
        return $html;
    }
    static function getClassPhoto(){
       return '<div class="w3-section" id="team-names">
            <span class="name"><b>Engineers:</b> <a href="studentpages/mo_abouelnagah.php">Mo Abouelnagah</a>, <a href="studentpages/anandb.php">Anand Bum-Erdene</a>, <a href="studentpages/frank_conte.php">Frank Conte<a>, <a href="studentpages/katia_daly.php">Katia Daly</a>, <a href="studentpages/sdomboski.php">Scott Domboski<a>, <a href="studentpages/chase_dornblaser.php">Chase Dornblaser</a>, 
            <a href="studentpages/aaron_frist.php">Aaron Frist</a>, <a href="studentpages/declan_keller.php">Declan Keller</a>, <a href="studentpages/olha_kuptsova.php">Olha Kuptsova</a>, <a href="studentpages/chris_mensah.php">Chris Mensah</a>, <a href="studentpages/kaiden.php">Kaiden Miller</a>, <a href="studentpages/connor_price.php">Connor Price</a>, <a href="studentpages/noah_peiffer.php">Noah Peiffer</a>, <a href="studentpages/kathy_sr.php">Kathy Saldana-Renteria</a>, <a href="studentpages/brandon_short.php">Brandon Short</a>,  
            Dominic Traina, <a href="studentpages/sam_vossen.php">Sam Vossen</a>, <a href="studentpages/isaac_widders.php">Isaac Widders</a>,
                <a href="studentpages/teddy.php">Teddy Bazyar (TA)</a>, and <a href="studentpages/Prof_reddig.php">Prof Reddig</a>
            </span>
        </div>
        <div class="w3-section" id="team-pic">
            <img id="Team2026Pic" src="images/SEteam2026.png" alt="2026 Team to Come picture." width=1000 usemap="#classmap">
            <map id="classmap">
                <area shape="circle" coords="250,97,20" href="studentpages/student_template.php">
                <area shape="circle" coords="480,150,20" href="studentpages/kaiden.php">
                <area shape="circle" coords="203,150,22" href="studentpages/sam_vossen.php">
                <area shape="circle" coords="905, 130, 26" href="studentpages/chase_dornblaser.php">
                <area shape="circle" coords="668,150,28" href="studentpages/declan_keller.php">
                <area shape="circle" coords="125,175,19" href="studentpages/olha_kuptsova.php">
                <area shape="circle" coords="280,140,19" href="studentpages/connor_price.php">
                <area shape="circle" coords="422,164,21" href="studentpages/sdomboski.php">
                <area shape="circle" coords="382,144,21" href="studentpages/isaac_widders.php">
                <area shape="circle" coords="65,190,22" href="studentpages/katia_daly.php">
                <area shape="circle" coords="520,160,24" href="studentpages/noah_peiffer.php">
                <area shape="circle" coords="566,140,24" href="studentpages/dominic_traina.php">
                <area shape="circle" coords="785,140,24" href="studentpages/frank_conte.php">
                <area shape="circle" coords="718,145,24" href="studentpages/mo_abouelnagah.php">
                <area shape="circle" coords="320,160,21" href="studentpages/chris_mensah.php">
                <area shape="circle" coords="245,185,21" href="studentpages/anandb.php">
                <area shape="circle" coords="163,190,19" href="studentpages/kathy_sr.php">
                <area shape="circle" coords="620,150,22" href="studentpages/aaron_frist.php">
            </map>    
        </div><BR><BR><BR><BR>';
    }
    static function getMap(){
       return '<img src="images/WorldMap.png"   usemap="#theworld"/>
        <map name="theworld">
            <!-- North America 21 -->
            <area shape="poly" href = "regions.php?id=21" coords="167, 54, 519, 25, 311,197, 210,170,  90,210 ">
            <!-- Central America 13 -->
            <area shape="poly" href = "regions.php?id=13" coords="215,170,240,215, 310,250, 290,205">
            <!-- South America 5 -->
            <area shape="poly" href = "regions.php?id=5" coords="331,228,440,300, 455,460, 285,460, 300,285">
            <!-- Caribbean 29 -->
            <area shape="poly" href = "regions.php?id=29" coords="293,198,370,210, 330,232, ">
            <!-- West Europe 155 -->
            <area shape="poly" href = "regions.php?id=155" coords="535,135,480,90, 530,85, 575,100, 585,120">
            <!-- North Europe 154 -->
            <area shape="poly" href = "regions.php?id=154" coords="485,70,580,90, 610,70, 600,50">
            <!-- South Europe 39 -->
            <area shape="poly" href = "regions.php?id=39" coords="515,135,550,140, 562,134, 585,125,618,140, 605, 153,540,153,513,157">
            <!-- East Europe 151 -->
            <area shape="poly" href = "regions.php?id=151" coords="577,98,587,118, 634,155, 660,108,675,50, 615, 80">
            <!-- Southeast Asia 35 -->
            <area shape="poly" href = "regions.php?id=35" coords="823,215,845,200, 900,217, 954,285, 954,306, 822,315,">
            <!-- South Asia 34 -->
            <area shape="poly" href = "regions.php?id=34" coords="664,154,735,153, 802,187, 790,269, 750,240, 717,195,">
            <!-- Central Asia 143 -->
            <area shape="poly" href = "regions.php?id=143" coords="660,114,763,115, 745,154, 688,152">
            <!-- East Asia 30 -->
            <area shape="poly" href = "regions.php?id=30" coords="887,204,930,133, 887,137, 767,113, 764,173, 887,214,">
            <!-- Oceania 9 -->
            <area shape="poly" href = "regions.php?id=9" coords="955,285,955,310, 857,340, 865,413, 1008,441,1076,310,995,270 ">
            <!-- Middle East 145 -->
            <area shape="poly" href = "regions.php?id=145" coords="666,236, 696,225, 713,204,702,192,688,191,666,166,662,154,639,157,639,173,653,207">  
            <!-- West Africa 11 -->
            <area shape="poly" href = "regions.php?id=11" coords="575,296, 583,296, 594,267,586,270,582,259,585,252,578,234,582,203,553,216,523,195
            ,466,221, 516,267">
            <!-- Central Africa 17 -->
            <area shape="poly" href = "regions.php?id=17" coords="585,203, 608,215, 603,242,622,263,641,266,641,277,631,282,624,307,607,313,605,301,
            ,588,295, 594,281,597,265,587,266,585,252,587,210">
            <!-- East Africa 14 -->
            <area shape="poly" href = "regions.php?id=14" coords="652,219, 648,232, 640,252,648,264,628,295,643,316,659,315,691,238,665,235,">
            <!-- Southern Africa 18 -->
            <area shape="poly" href = "regions.php?id=18" coords="660,315, 693,320, 676,395,561,397,577,299,605,315,628,323,627,308,637,311,
            644,317">
            <!-- North Africa 15 -->
            <area shape="poly" href = "regions.php?id=15" coords="492,201,519,189,552,211,580,200,613,213,642,201,631,178,570,154,524,160">
        </map>';

    }

}




?>