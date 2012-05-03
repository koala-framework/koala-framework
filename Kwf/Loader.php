<?php
class Kwf_Loader
{
    private static $_includePath;
    /**
     * Set include path used for Kwf_Loader::isValidClass
     *
     * called exactly once in setup
     *
     * get_include_path is not used, because some external library might have changed that.
     *
     * @internal
     */
    public static function setIncludePath($ip)
    {
        if (self::$_includePath) throw new Kwf_Exception("include path is already set");
        self::$_includePath = $ip;
        set_include_path($ip);
    }

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
            require_once Kwf_Config::getValue('externLibraryPath.tcpdf').'/tcpdf.php';
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

    /**
     * Use this method to validate user input that should represent a valid class
     *
     * validate before triggering autoloader for security reasons
     */
    public static function isValidClass($class)
    {
        if (preg_match('#[^a-z0-9_]#i', $class)) return false;

        //don't look into other libs
        if (preg_match('#^[a-z]+-lib#i', $class)) return false;
        if (preg_match('#^lib#i', $class)) return false;

        $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        foreach (explode(PATH_SEPARATOR, self::$_includePath) as $ip) {
            if (file_exists($ip.DIRECTORY_SEPARATOR.$file)) {
                return class_exists($class);
            }
        }
        return false;
    }

}
