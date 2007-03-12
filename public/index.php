<?php
error_reporting(E_ALL|E_STRICT);
date_default_timezone_set('Europe/Berlin');
 
$include_path  = get_include_path();
$include_path .= PATH_SEPARATOR . '../library';
$include_path .= PATH_SEPARATOR . '../application/models';  
set_include_path($include_path);
 
require_once 'Zend.php';
/*
function __autoload($class)
{
    Zend::loadClass($class);
}
*/
Zend::loadClass('Zend_Controller_Front');
Zend::loadClass('Zend_Config_Ini');
$frontController = Zend_Controller_Front::getInstance();
//$frontController->setRequest('E3_Controller_Request');
//$frontController->setRouter('E3_Controller_Router');
$frontController->getRouter()->addConfig(new Zend_Config_Ini('../application/config.ini', 'routes'), 'routes');
$frontController->setControllerDirectory('../application/controllers');
$frontController->throwExceptions(true);
$frontController->dispatch();

/*
// DB
$params = array ('host'     => '127.0.0.1',
                 'username' => 'root',
                 'password' => '',
                 'dbname'   => 'travelloblog');
$db = Zend_Db::factory('PDO_MYSQL', $params);
Zend_Db_Table::setDefaultAdapter($db);

$route1 = new Zend_Controller_Router_Route(':controller/:action/:id', array('action' => 'index'));
$router = new Zend_Controller_RewriteRouter();
$router->addRoute('myroute', $route1);
Zend::register('router', $router);

$view = new Zend_View();
$view->setScriptPath('../application/views');
Zend::register('view', $view);
$controller = Zend_Controller_Front::getInstance();
//$controller->setRouter($router);
$controller->setControllerDirectory('../application/controllers');
$controller->throwExceptions(true);
try {
    $controller->dispatch();
} catch (Exception $e) {
	echo "<pre>$e</pre>";
}
*/
function p($src, $max_depth = 3) {
    ini_set('xdebug.var_display_max_depth', $max_depth);
    if(function_exists('xdebug_var_dump')) {
        xdebug_var_dump($src);
    } else {
        echo "<pre>";
        print_r($src);
        echo "</pre>";
    }
}
?> 