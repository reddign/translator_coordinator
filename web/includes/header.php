<?php
$url = "/native_tweet/web";
require_once __DIR__ . "/language_switch.php";
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_lang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?PHP echo $url;?>/style.css">
    <link rel="icon" type="image/x-icon" href="<?PHP echo $url;?>/images/worldIcon.png">
    <title>Native Tweet</title>
</head>
<body>