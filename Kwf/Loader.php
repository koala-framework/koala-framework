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
        if (!class_exists('Kwf_Benchmark', false)) require KWF_PATH.'/Kwf/Benchmark.php';
        if (Kwf_Benchmark::isEnabled()) {
            $class = 'Kwf_Loader_Benchmark';
            if (!class_exists($class, false)) require KWF_PATH.'/Kwf/Loader/Benchmark.php';
        } else {
            //für performance
            $class = 'Kwf_Loader';
        }
        spl_autoload_register(array($class, 'loadClass'));
    }

    public static function loadClass($class)
    {

        static $namespaces;
        if (!isset($namespaces)) {
            $namespaces = include VENDOR_PATH.'/composer/autoload_namespaces.php';
        }
        static $classMap;
        if (!isset($classMap)) {
            $classMap = include VENDOR_PATH.'/composer/autoload_classmap.php';
        }
        if (isset($classMap[$class])) {
            $file = $classMap[$class];
        } else {
            $pos = strpos($class, '_');
            $ns1 = substr($class, 0, $pos+1);

            $pos = strpos($class, '_', $pos+1);
            if ($pos !== false) {
                $ns2 = substr($class, 0, $pos+1);
            } else {
                $ns2 = $class;
            }

            $dirs = false;
            if (isset($namespaces[$ns2])) {
                $dirs = $namespaces[$ns2];
            } else if (isset($namespaces[$ns1])) {
                $dirs = $namespaces[$ns1];
            }
            $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
            if ($dirs !== false) {
                if (count($dirs) == 1) {
                    $file = $dirs[0].'/'.$file;
                } else {
                    foreach ($dirs as $dir) {
                        if (file_exists($dir.'/'.$file)) {
                            $file = $dir.'/'.$file;
                        }
                    }
                }
            }
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
