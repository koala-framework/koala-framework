<?php
chdir(dirname(__FILE__).'/tests');
set_include_path('.'.PATH_SEPARATOR.realpath(getcwd().'/..'));

require_once 'Kwf/Setup.php';
Kwf_Setup::setUp();

if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/') {
    echo Kwf_Registry::get('config')->application->kwf->name.' '.Kwf_Registry::get('config')->application->kwf->version;
    exit;
}
$img = '/kwf/kwctest/Kwc_Basic_ImageEnlarge_Root/media/Kwc_Basic_ImageEnlarge_EnlargeTagWithoutSmall_TestComponent/1800-linkTag/default/d28cedbd1a75ccd5bbabb85c0c0ec319/1317301103/foo.png';
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == $img) {
    sleep(5);
}

$front = Kwf_Controller_Front::getInstance();
$response = $front->dispatch();
$response->sendResponse();
