<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        echo "PHP IS WORKING"; 
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        include 'forms/createGroupForm.php'; 
    ?>
</body>
</html>