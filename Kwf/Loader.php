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
            $namespaces = array();
            $composerNamespaces = include VENDOR_PATH.'/composer/autoload_namespaces.php';
            foreach ($composerNamespaces as $namespace => $dirs) {
                // convert paths to psr4 style
                $namespacePath = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
                $namespacePath = str_replace('_', DIRECTORY_SEPARATOR, $namespacePath);
                if ($namespacePath[strlen($namespacePath)-1] == DIRECTORY_SEPARATOR) {
                    // Path must not end with /
                    $namespacePath = substr($namespacePath, 0, -1);
                }
                $preparedDirs = array();
                foreach ($dirs as $dir) {
                    $preparedDirs[] = $dir.DIRECTORY_SEPARATOR.$namespacePath;
                }
                $namespaces[$namespace] = $preparedDirs;
                if (strpos($namespace, '\\') === false && substr($namespace, -1) != '_') {
                    $namespace = $namespace.DIRECTORY_SEPARATOR;
                    $namespaces[$namespace] = $preparedDirs;
                }
            }
            $psr4Namespaces = include VENDOR_PATH.'/composer/autoload_psr4.php';
            foreach ($psr4Namespaces as $psr4Namespace => $dirs) {
                $namespaces[$psr4Namespace] = $dirs;
            }
        }

        static $classMap;
        if (!isset($classMap)) {
            $classMap = include VENDOR_PATH.'/composer/autoload_classmap.php';
        }

        if (isset($classMap[$class])) {
            $file = $classMap[$class];
        } else {
            $ns3 = null;
            if (($pos = strpos($class, '\\')) !== false) {
                //php 5.3 namespace
                $ns1 = substr($class, 0, $pos+1);

                $pos = strpos($class, '\\', $pos+1);
                if ($pos !== false) {
                    $ns2 = substr($class, 0, $pos+1);
                } else {
                    $ns2 = $class;
                }

                $pos = strpos($class, '\\', $pos+1);
                if ($pos !== false) {
                    $ns3 = substr($class, 0, $pos+1);
                } else {
                    $ns3 = $class;
                }
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            } else {
                $pos = strpos($class, '_');
                $ns1 = substr($class, 0, $pos+1);

                $pos = strpos($class, '_', $pos+1);
                if ($pos !== false) {
                    $ns2 = substr($class, 0, $pos+1);
                } else {
                    $ns2 = $class;
                }

                $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
            }
            $dirs = false;
            $matchingNamespace = '';
            if ($ns3 && isset($namespaces[$ns3])) {
                $dirs = $namespaces[$ns3];
                $matchingNamespace = $ns3;
            } else if (isset($namespaces[$ns2])) {
                $dirs = $namespaces[$ns2];
                $matchingNamespace = $ns2;
            } else if (isset($namespaces[$ns1])) {
                $dirs = $namespaces[$ns1];
                $matchingNamespace = $ns1;
            }
            if ($dirs !== false) {
                $file = substr($file, strlen($matchingNamespace));
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

        static $classmap;
        if (!isset($namespaces)) {
            $namespaces = array();
            $composerNamespaces = include VENDOR_PATH.'/composer/autoload_namespaces.php';
            foreach ($composerNamespaces as $namespace => $path) {
                $namespaces[$namespace] = $path;
                if (strpos($namespace, '\\') === false && substr($namespace, -1) != '_') {
                    $namespace = $namespace.'_';
                    $namespaces[$namespace] = $path;
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

        return class_exists($class);
    }

}
