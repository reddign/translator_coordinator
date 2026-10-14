<?php
require_once "Page.php";

class NavBar {
    private $links = [];
    private $url;

    public function __construct($links = []) {
        $this->url = Page::getUrl();
        $this->links = $links;
    }

    public function render() {
       //Logo Link
        $html = "<div class='w3-top'>";
        $html .= "<div class='w3-bar w3-white w3-padding w3-card' style='letter-spacing:4px;'>";
        $html .= "<a href='{$this->url}/index.php' class='w3-bar-item w3-button'>";
        $html .= "<img src='{$this->url}/images/logo.png' height='50px'></a>";
        $html .= "<div class='w3-right w3-hide-small'>";
            
        foreach ($this->links as $text => $info) {
            $url = $info['url'];
            $html .= "<a href='{$this->url}/{$url}' class='w3-bar-item w3-button'>{$text}</a>";
        
        }
        $html .= "</div></div></div>";
        return $html;
    }
}
