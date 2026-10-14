<?php

class Page {
    private $title;
    private $content;
    private $footer;
    private $navbar;
    private $jsFileArray;
    private $url;

    public function __construct($title, NavBar $navbar, $content, Footer $footer,$jsFileArray) {
        $this->url = Page::getUrl();
        $this->title = $title;
        $this->navbar = $navbar;
        $this->content = $content;
        $this->footer = $footer;
        $this->jsFileArray = $jsFileArray;
    }

    public function render() {
        return "
            <!DOCTYPE html>
            <html lang='en'>
            <head>


                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>{$this->title}</title>
                
                <link rel='stylesheet' href='{$this->url}/style.css'>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css'>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
                <link rel='icon' type='image/x-icon' href='{$this->url}/images/worldIcon.png'>
                {$this->makeJsFileLinks()}
                </head>
                <body>
                    {$this->navbar->render()}
                        <div class='w3-container' style='margin-top:100px'>
                            {$this->content}
                        </div>
                    {$this->footer->render()}
                </body>
            </html>
        ";
    }
    function makeJsFileLinks(){
        $linksString = "";
        //TODO:
        //Loop of $this->jsFileArray
        //making proper html for each file and adding it
        //to the $linksString
        
        return $linksString;

    }
    static function getUrl(){
            $baseFilePath="/translator_coordinator/web";
            if(isset($_SERVER['HTTPS'])){
                $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
            }
            else{
                $protocol = 'http';
            }
            return $protocol . "://" . $_SERVER['HTTP_HOST'] .  $baseFilePath;
    }
}
