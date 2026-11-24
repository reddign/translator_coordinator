<?php
include "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";
?>

<h2><?php echo t('about_title'); ?></h2>

<div class="w3-section" id="about-project">

    <p><?php echo t('about_intro'); ?></p>

    <ul>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <li>
                <?php echo t('about_feature_' . $i); ?>
            </li>
        <?php endfor; ?>
    </ul>

    <p><?php echo t('about_goal'); ?></p>

</div>

<div class="w3-section" id="team-names">

    <h3><?php echo t('the_team'); ?></h3>

    <ul>
        <li>
            <a href="studentpages/anandb.php">
                Anand Bum-Erdene
            </a>
        </li>

        <li>
            <a href="studentpages/olha_kuptsova.php">
                Olha Kuptsova
            </a>
        </li>

        <li>
            <a href="studentpages/mo_abouelnagah.php">
                Mo Abouelnagah
            </a>
        </li>
    </ul>

</div>

<br><br><br><br>

<?php
include "includes/footer.php";
?>