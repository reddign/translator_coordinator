<?php

class Footer {
    private $text;
    private $socialLinks = '<div style="float:right;">Connect with us:
    <a href="https://www.facebook.com/etowncollege/" class="fa fa-facebook" style="text-decoration: none;"></a>
    <a href="https://www.instagram.com/etowncollege/" class = "fa fa-instagram" style="text-decoration: none;"></a>
    <a href="https://twitter.com/EtownCollege?ref_src=twsrc%5Egoogle%7Ctwcamp%5Eserp%7Ctwgr%5Eauthor" class = "fa fa-twitter" style="text-decoration: none;"></a>
    <a href="https://www.linkedin.com/school/elizabethtown-college/" class = "fa fa-linkedin" style="text-decoration: none;"></a></div>';

    public function __construct($text) {
        $this->text = $text;
    }

    public function render() {
        return "<div class='w3-bottom'>
        <div class='w3-bar w3-white w3-padding w3-card'>
        {$this->text}{$this->socialLinks}
        </div>
        </div>";
    }
}
