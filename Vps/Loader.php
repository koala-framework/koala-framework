<?php
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

    public static function registerAutoload()
    {
        require_once 'Vps/Benchmark.php';
        if (Vps_Benchmark::isEnabled()) {
            $class = 'Vps_Loader_Benchmark';
            require_once 'Vps/Loader/Benchmark.php';
        } else {
            //für performance
            $class = 'Vps_Loader';
        }
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setDefaultAutoloader(array($class, 'loadClass'));
        $autoloader->setFallbackAutoloader(true);
    }

    public static function loadClass($class)
    {
        if ($class == 'TCPDF') {
            require_once 'tcpdf.php';
        } else {
            $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
            try {
                include_once $file;
            } catch (Exception $e) {
                if ($fp = @fopen($file, 'r', true)) {
                    //wenns die datei gibt, fehler weiterschmeissen
                    //(file_exists akzeptiert leider keinen use_include_path parameter)
                    fclose($fp);
                    throw $e;
                }
                //wenns die datei nicht gibt, keinen fehler schmeissen
                //ist notwendig für class_exists das false zurück gibt
            }
        }
    }
}
