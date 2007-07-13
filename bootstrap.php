<?php
if (file_exists('application/include_path')) {
    define('VPS_PATH', file_get_contents('application/include_path'));
} else  {
    define('VPS_PATH', '/www/public/vps/library/');
}
set_include_path(get_include_path() . PATH_SEPARATOR . VPS_PATH);
try {
    require_once 'Vps/Assets/Loader.php';
    Vps_Assets_Loader::load();
    require_once VPS_PATH . '/Vps/Controller/Front.php';
    Vps_Controller_Front::setUp();
    Vps_Controller_Front::setUpDb();
    $front = Vps_Controller_Front::getInstance(false);

    $router = $front->getRouter();
    $front->setDispatcher(new Vps_Controller_Dispatcher());
    $router->AddRoute('componentshow', new Zend_Controller_Router_Route('component/:action/:id', array('module' => 'admin', 'controller' => 'components', 'action' => 'show')));
    $router->AddRoute('componentedit', new Zend_Controller_Router_Route('component/edit/:id/:action', array('module' => 'component', 'controller' => 'index', 'action' => 'index')));

    $acl = new Vps_Acl();
    $acl->add(new Zend_Acl_Resource('component'));
    $acl->add(new Zend_Acl_Resource('components'));
    $acl->allow('guest', 'component');
    $acl->allow('guest', 'components');
    Zend_Registry::set('acl', $acl);
    
    $response = $front->dispatch();
    $response->setHeader('Content-Type', 'text/html; charset=utf-8');
    $response->sendHeaders();
    $response->outputBody();
} catch (Exception $e){
    echo '<pre>';
    echo ($e->__toString());
    echo '</pre>';
}
