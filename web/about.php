<?php

require_once 'classes/Page.php';
require_once 'classes/Footer.php';
require_once 'classes/NavBar.php';
require_once 'classes/PageFactory.php';
require_once 'classes/PageContent.php';

$navLinks = [
    "Home" => ["url" => "/index.php", "icon" => "fas fa-home"],
    "About" => ["url" => "/about.php", "icon" => "fas fa-info-circle"],
    "Login" => ["url" => "/login.php", "icon" => "fas fa-key"]
];

$title = "Translator Coordinator - Software Engineering Class Project 2026";
$content = PageContent::render("about");
$footerText = "&copy; Powered by Etown CS 341";


$page = PageFactory::createPage($title, $content, $footerText, $navLinks,[]);
echo $page->render();