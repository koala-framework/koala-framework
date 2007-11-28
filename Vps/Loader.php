<?php
if (file_exists(VPS_PATH.'/include_path')) {
    $zendPath = trim(file_get_contents(VPS_PATH.'/include_path'));
} else if (file_exists('/www/public/library/zend/current')) {
    $zendPath = '/www/public/library/zend/current';
} else {
    die ('zend not found');
}
$include_path  = get_include_path();
$include_path .= PATH_SEPARATOR . $zendPath;
set_include_path($include_path);

require_once 'Zend/Loader.php';

class Vps_Loader extends Zend_Loader
{
}
