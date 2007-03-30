<?php

// $test = str_repeat(str_repeat("x", 1000), 1000);
//error_reporting(E_ALL|E_STRICT);
error_reporting(E_ALL); // wg. HTML_QuickForm (PEAR)
date_default_timezone_set('Europe/Berlin');

$include_path  = get_include_path();
$include_path .= PATH_SEPARATOR . '../library';
$include_path .= PATH_SEPARATOR . '../application/models';
set_include_path($include_path);

require_once 'Zend/Loader.php';
function __autoload($class)
{
    Zend_Loader::loadClass($class);
}

$dao = new E3_Dao(new Zend_Config_Ini('../application/config.db.ini', 'database'));
Zend_Registry::set('dao', $dao);


$front = Zend_Controller_Front::getInstance();
$front->setDispatcher(new E3_Controller_Dispatcher());
//$front->setRequest('E3_Controller_Request');
//$front->setRouter('E3_Controller_Router');
$router = $front->getRouter();
$router->addConfig(new Zend_Config_Ini('../application/config.ini', 'routes'), 'routes');



$front->registerPlugin(new E3_Controller_Plugin_Admin());
$front->setControllerDirectory('../application/controllers');
$front->returnResponse(true);

$response = $front->dispatch();
if ($response->isException()) {
    $response->sendHeaders();
    $response->outputBody();
    foreach ($response->getException() as $exception) {
      p($exception);
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