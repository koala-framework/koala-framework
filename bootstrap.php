<?php
chdir(dirname(__FILE__).'/tests');
set_include_path('.'.PATH_SEPARATOR.realpath(getcwd().'/..'));

require_once 'Kwf/Setup.php';
Kwf_Setup::setUp();

if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/') {
    echo Kwf_Registry::get('config')->application->kwf->name.' '.Kwf_Registry::get('config')->application->kwf->version;
    exit;
}

$front = Kwf_Controller_Front::getInstance();
$front->setBaseUrl('');
$response = $front->dispatch();
$response->sendResponse();
