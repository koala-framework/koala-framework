<?php
chdir(dirname(__FILE__));
define('VPS_PATH', dirname(__FILE__));

$include_path  = get_include_path();
$include_path .= PATH_SEPARATOR . VPS_PATH;
set_include_path($include_path);

require_once 'Vps/Setup.php';
Vps_Setup::setUp();
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/') {
    echo Vps_Registry::get('config')->application->vps->name.' '.Vps_Registry::get('config')->application->vps->version;
    exit;
}
$img = '/vps/vpctest/Vpc_Basic_ImageEnlarge_Root/media/Vpc_Basic_ImageEnlarge_EnlargeTagWithoutSmall_TestComponent/1800-linkTag/default/d28cedbd1a75ccd5bbabb85c0c0ec319/1317301103/foo.png';
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == $img) {
    sleep(5);
}
Vps_Assets_Loader::load();

$front = Vps_Controller_Front::getInstance();
$response = $front->dispatch();
$response->sendResponse();
