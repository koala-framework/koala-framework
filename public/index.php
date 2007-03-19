<?php
error_reporting(E_ALL|E_STRICT);
date_default_timezone_set('Europe/Berlin');
 
$include_path  = get_include_path();
$include_path .= PATH_SEPARATOR . '../library';
$include_path .= PATH_SEPARATOR . '../application/models';  
set_include_path($include_path);
 
require_once 'Zend.php';
require_once 'Zend/Loader.php';
function __autoload($class)
{
    Zend_Loader::loadClass($class);
}

$frontController = Zend_Controller_Front::getInstance();
//$frontController->setRequest('E3_Controller_Request');
//$frontController->setRouter('E3_Controller_Router');
$frontController->getRouter()->addConfig(new Zend_Config_Ini('../application/config.ini', 'routes'), 'routes');
$frontController->setControllerDirectory('../application/controllers');
$frontController->returnResponse(true);
$response = $frontController->dispatch();
if ($response->isException()) {
	$response->sendHeaders();
    $response->outputBody();
    foreach ($response->getException() as $exception) {
    	throw($exception);
    	//p($exception);
    }
} else {
	$response->sendHeaders();
    $response->outputBody();
}

function p($src, $max_depth = 3) {
    //Zend::dump($src);
    ini_set('xdebug.var_display_max_depth', $max_depth);
    if(function_exists('xdebug_var_dump')) {
        xdebug_var_dump($src);
    } else {
        echo "<pre>";
        print_r($src);
        echo "</pre>";
    }
}