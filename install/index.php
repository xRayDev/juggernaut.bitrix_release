<?php

/* @var $APPLICATION CMain */

class olof_juggernaut extends CModule
{
    var $MODULE_ID = "olof.juggernaut";
    var $MODULE_NAME = "olof.juggernaut";
    var $MODULE_DESCRIPTION = "Альтернатива BitrixFramework";
    
    function __construct() {
		$this->PARTNER_NAME = "Олоф";
    	$this->PARTNER_URI = "http://www.olof.ru";
    }

    function DoInstall() {
        RegisterModule($this->MODULE_ID);
    }

    function DoUninstall() {
        UnRegisterModule($this->MODULE_ID);
    }
}