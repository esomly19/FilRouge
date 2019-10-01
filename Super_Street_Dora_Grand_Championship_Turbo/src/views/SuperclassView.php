<?php

namespace Super_Street_Dora_Grand_Championship_Turbo\views;

abstract class SuperclassView{

    protected $title = "";
    protected $css = "";
    protected $body = "";

    public function render(){
        $webPage = <<<END
        <!DOCTYPE html>
        <html lang="fr">
            <head> 
            <title>StreetDora$this->title</title>
            </head>
            <body>
            <div>
                $this->body
            </div>
            </body>

END;
        echo $webPage;
    }
}