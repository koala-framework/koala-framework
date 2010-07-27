<?php
chdir(dirname(__FILE__));
define('VPS_PATH', dirname(__FILE__));

$include_path  = get_include_path();
$include_path .= PATH_SEPARATOR . VPS_PATH;
set_include_path($include_path);

require_once 'Vps/Setup.php';
Vps_Setup::setUp();


$d = "<?php
\$dependencies = array(
";
$c = new Zend_Config_Ini('config.ini', 'dependencies');
foreach ($c->toArray() as $k=>$i) {
    p($i);
    $d .= "    '".$k."' => array(\n";
    if (isset($i['files'])) {
    $d .= "        'files' => array(\n";
    foreach ($i['files'] as $f) {
        $d .= "            '$f',\n";
    }
    $d .= "        ),\n";
    }
    if (isset($i['dep'])) {
    $d .= "        'dep' => array(\n";
    foreach ($i['dep'] as $f) {
        $d .= "            '$f',\n";
    }
    $d .= "        ),\n";
    }
    $d .= "    ),\n";
}
$d .= "
);";
file_put_contents('dependencies.php', $d);

Vps_Assets_Loader::load();

$front = Vps_Controller_Front::getInstance();
$response = $front->dispatch();
$response->sendResponse();
