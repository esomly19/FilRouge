<?php
namespace Super_Street_Dora_Grand_Championship_Turbo\controllers;

use Super_Street_Dora_Grand_Championship_Turbo\views\HomeView as HV;

final class ControleurHome
{
    public static function goHome(){
        (new HV())->render();
    }


}
