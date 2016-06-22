<?php

namespace Jugger;

use Bitrix\Main\Loader;
use Jugger\Psr\Psr4\Autoloader;

include_once __DIR__. '/lib/Psr/Psr4/Autoloader.php';

Loader::includeModule("iblock");

spl_autoload_register('\Jugger\Psr\Psr4\Autoloader::loadClass');

Autoloader::addNamespace("Jugger", __DIR__.'/lib');