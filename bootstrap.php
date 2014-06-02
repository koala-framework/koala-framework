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
$response = $front->dispatch();
$response->sendResponse();
