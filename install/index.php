<?php

/* @var $APPLICATION CMain */

class Juggernaut extends CModule
{
    public $MODULE_ID = "olof.juggernaut";
    public $MODULE_NAME = "olof.juggernaut";
    public $MODULE_DESCRIPTION = "Альтернатива BitrixFramework";
    
    public $PARTNER_NAME = "Олоф";
    public $PARTNER_URI = "http://www.olof.ru";
    
    function DoInstall() {
        RegisterModule($this->MODULE_ID);
    }

    function DoUninstall() {
        UnRegisterModule($this->MODULE_ID);
    }
}