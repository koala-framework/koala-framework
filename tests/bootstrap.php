<?php
error_reporting(E_ALL|E_STRICT);

define('VPS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'..');
$include_path  = get_include_path();
$include_path .= PATH_SEPARATOR . VPS_PATH;
set_include_path($include_path);
require_once 'Vps/Loader.php';
require_once 'Vps/Setup.php';
Vps_Loader::registerAutoload();
date_default_timezone_set('Europe/Berlin');

Zend_Registry::setClassName('Vps_Registry');

require_once 'TestConfiguration.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
