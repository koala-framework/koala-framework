<?php
class Kwf_Loader
{
    public static function registerAutoload()
    {
        require_once 'Kwf/Benchmark.php';
        if (Kwf_Benchmark::isEnabled()) {
            $class = 'Kwf_Loader_Benchmark';
            require_once 'Kwf/Loader/Benchmark.php';
        } else {
            //für performance
            $class = 'Kwf_Loader';
        }
        spl_autoload_register(array($class, 'loadClass'));
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
