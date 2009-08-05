<?php
if (file_exists(VPS_PATH.'/include_path')) {
    $zendPath = trim(file_get_contents(VPS_PATH.'/include_path'));
    $zendPath = str_replace(
        '%version%',
        file_get_contents(VPS_PATH.'/include_path_version'),
        $zendPath);
} else {
    die ('zend not found');
}
$includePath  = get_include_path();
$includePath .= PATH_SEPARATOR . $zendPath;
set_include_path($includePath);

require_once 'Zend/Loader.php';

class Vps_Loader extends Zend_Loader
{
    public function classExists($class)
    {
        $filename = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

        foreach (explode(PATH_SEPARATOR, get_include_path()) as $dir) {
            $filespec = rtrim($dir, '\\/') . DIRECTORY_SEPARATOR . $filename;
            if (is_file($filespec)) {
                return class_exists($class);
            }
        }
        return false;
    }

    public static function registerAutoload($class = null, $enabled = true)
    {
        if (!$class) {
            require_once 'Vps/Benchmark.php';
            if (Vps_Benchmark::isEnabled()) {
                $class = 'Vps_Loader_Benchmark';
            } else {
                //für performance
                $class = 'Vps_Loader';
            }
        }
        parent::registerAutoload($class, $enabled);
    }
    public static function autoload($class)
    {
        if ($class == 'TCPDF') {
            require_once 'tcpdf.php';
        } else {
            parent::autoload($class);
        }
    }


}
