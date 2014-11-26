<?php
chdir(dirname(__FILE__).'/tests');
set_include_path('.'.PATH_SEPARATOR.realpath(getcwd().'/..'));
define('VENDOR_PATH', '../vendor');

require 'Kwf/Setup.php';
Kwf_Setup::setUp();

if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/') {
    echo Kwf_Registry::get('config')->application->kwf->name.' '.Kwf_Registry::get('config')->application->kwf->version;
    exit;
}

$front = Kwf_Controller_Front::getInstance();
$front->addControllerDirectory(KWF_PATH.'/tests', 'kwf_test');
$front->addControllerDirectory(KWF_PATH.'/tests/controller', 'tests_controller');

$router = $front->getRouter();

if ($router instanceof Kwf_Controller_Router) {

    //für selenium-tests von sachen die im kwf liegen
    $router->AddRoute('kwf_test', new Zend_Controller_Router_Route(
                '/kwf/test/:controller/:action',
                array('module'     => 'kwf_test',
                    'action'     =>'index')));
    $router->AddRoute('kwf_kwctest', new Zend_Controller_Router_Route_Regex(
                'kwf/kwctest/([^/]+)/(.*)',
                array('module'     => 'tests_controller',
                      'controller' => 'render-component',
                      'action'     => 'index',
                      'url'        => ''),
                array('root'=>1, 'url'=>2)));
    $router->AddRoute('kwf_test_componentedit', new Zend_Controller_Router_Route(
                '/kwf/componentedittest/:root/:class/:componentController/:action',
                array('module' => 'component_test',
                    'controller' => 'component_test',
                    'action' => 'index')));
}

$front->setBaseUrl('');
$response = $front->dispatch();
$response->sendResponse();
