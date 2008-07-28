<?php
if (file_exists(VPS_PATH.'/include_path')) {
    $zendPath = trim(file_get_contents(VPS_PATH.'/include_path'));
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
                $class = 'Vps_Loader';
            } else {
                //für performance
                $class = 'Zend_Loader';
            }
        }
        parent::registerAutoload($class, $enabled);
    }

    public static function autoload($class)
    {
        $ret = parent::autoload($class);
        if (substr($class, 0, 4) == 'Vpc_') {
            if (is_subclass_of($class, 'Vpc_Abstract')) {
                Vps_Benchmark::count('component classes included');
            } else if (is_subclass_of($class, 'Vps_Component_Generator_Abstract')) {
                Vps_Benchmark::count('generator classes included');
            }
        }
        Vps_Benchmark::count('classes included', $class);
        return $ret;
    }

}
