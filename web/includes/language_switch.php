<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define allowed languages to secure app from malicious file inclusion
$allowed_langs = ['en', 'ru', 'uk', 'es'];
$default_lang  = 'en';

// Check if a language change request was made via the URL (?lang=xx)
if (isset($_GET['lang']) && in_array($_GET['lang'], $allowed_langs, true)) {
    $_SESSION['lang'] = $_GET['lang'];
    
    $params = $_GET;
    unset($params['lang']);

    $path = strtok($_SERVER['REQUEST_URI'], '?');
    $query = http_build_query($params);

    header("Location: " . $path . ($query ? '?' . $query : ''));
    exit();
}

$current_lang = $_SESSION['lang'] ?? $default_lang;

if (!in_array($current_lang, $allowed_langs, true)) {
    $current_lang = $default_lang;
}
$lang = require __DIR__ . "/languages/{$current_lang}.php";

if (!function_exists('t')) {
    function t($key) {
        global $lang;
    return htmlspecialchars((string)($lang[$key] ?? $key), ENT_QUOTES, 'UTF-8');
}
}
