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
            $start = substr($file, 0, 4);
            if ($start == 'Kwf/' || $start == 'Kwc/') {
                //use absolute path for optimal performance
                $absolutePathUsed = true;
                $file = KWF_PATH.'/'.$file;
            }
            try {
                include $file;
            } catch (Exception $e) {
                if ($fp = @fopen($file, 'r', true)) {
                    //if file exists re-throw exception
                    //(file_exists accepts unfortunately no use_include_path parameter)
                    fclose($fp);
                    throw $e;
                }

                if (!isset($absolutePathUsed)) return;

                //not found, try again without absolute KWF_PATH
                $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
                try {
                    include $file;
                } catch (Exception $e) {
                    if ($fp = @fopen($file, 'r', true)) {
                        //if file exists re-throw exception
                        //(file_exists accepts unfortunately no use_include_path parameter)
                        fclose($fp);
                        throw $e;
                    }
                    //if file does not exist don't throw exception
                    //required for class_exists to return false
                }
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
