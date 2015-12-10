<?php
class Kwf_Util_Check_Config
{
    public static function dispatch()
    {
        Kwf_Loader::registerAutoload();
        if (php_sapi_name() == 'cli') {
            $quiet = isset($_SERVER['argv'][2]) && $_SERVER['argv'][2] == 'quiet';
        } else {
            if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER']!='vivid' || $_SERVER['PHP_AUTH_PW']!='planet') {
                header('WWW-Authenticate: Basic realm="Check Config"');
                throw new Kwf_Exception_AccessDenied();
            }
            $quiet = isset($_GET['quiet']);
        }
        Kwf_Util_Check_Config::check($quiet);
    }

    public function check($quiet = false)
    {
        $checks = array();
        $checks['php'] = array(
            'name' => 'Php >= 5.2'
        );
        $checks['imagick'] = array(
            'name' => 'imagick Php extension'
        );
        $checks['exif'] = array(
            'name' => 'read EXIF data'
        );
        /*
        $checks['gd'] = array(
            'name' => 'gd Php extension'
        );
        */
        $checks['fileinfo'] = array(
            'name' => 'fileinfo Php extension'
        );
        $checks['simplexml'] = array(
            'name' => 'simplexml Php extension'
        );
        $checks['tidy'] = array(
            'name' => 'tidy Php extension'
        );
        $checks['pdo_mysql'] = array(
            'name' => 'pdo_mysql Php extension'
        );
        $checks['system'] = array(
            'name' => 'executing system commands'
        );
        $checks['log_write'] = array(
            'name' => 'log write permissions'
        );
        $checks['temp_write'] = array(
            'name' => 'temp write permissions'
        );
        $checks['cache_write'] = array(
            'name' => 'cache write permissions'
        );
        $checks['root_write'] = array(
            'name' => 'root write permissions'
        );
        $checks['imagick_functionality_1'] = array(
            'name' => 'imagick functionality 1'
        );
        $checks['imagick_functionality_2'] = array(
            'name' => 'imagick functionality 2'
        );
        $checks['memory_limit'] = array(
            'name' => 'memory_limit'
        );
        $checks['magic_quotes_gpc'] = array(
            'name' => 'magic_quotes_gpc'
        );

        //ab hier wird die config geladen
        $checks['setup_kwf'] = array(
            'name' => 'loading kwf'
        );
        $checks['db_connection'] = array(
            'name' => 'db connection'
        );
        $checks['git'] = array(
            'name' => 'git >= 1.5'
        );
        $checks['uploads'] = array(
            'name' => 'uploads'
        );
        $checks['setlocale'] = array(
            'name' => 'setlocale'
        );
        $checks['apc'] = array(
            'name' => 'apc'
        );

        if ($quiet) {
            foreach ($checks as $k=>$i) {
                try {
                    call_user_func(array('Kwf_Util_Check_Config', '_'.$k));
                } catch (Exception $e) {
                    echo "\nERROR: " . $e->getMessage();
                }
            }
            if (php_sapi_name()!= 'cli') {
                passthru("php bootstrap.php check-config silent 2>&1", $ret);
                if ($ret) echo "\nFAILED CLI";
            }
        } else {
            if (php_sapi_name()!= 'cli') {
                echo "<h3>Test Webserver...\n</h3>";
            }
            foreach ($checks as $k=>$i) {
                echo "<p style=\"margin:0;\">";
                echo $i['name'].': ';
                try {
                    call_user_func(array('Kwf_Util_Check_Config', '_'.$k));
                    echo "<span style=\"background-color:green\">OK</span>";
                } catch (Exception $e) {
                    echo "<span style=\"background-color:red\">FAILED:</span> ".$e->getMessage();
                }
                echo "</p>";
            }

            if (php_sapi_name()!= 'cli') {
                echo "<h3>Test Cli...\n</h3>";
                passthru("php bootstrap.php check-config 2>&1", $ret);
                if ($ret) {
                    echo "<span style=\"background-color:red\">FAILED CLI: $ret</span>";
                }
                echo  '<br /><br /> all tests finished';
            }
        }

        exit;
    }

    private static function _php()
    {
        if (version_compare(PHP_VERSION, '5.1.6') < 0) {
            throw new Kwf_Exception("Php version '".PHP_VERSION."' is too old");
        }
    }

    private static function _imagick()
    {
        //if (!extension_loaded('imagick')) {
        if (!class_exists('Imagick')) {
            throw new Kwf_Exception("Extension 'imagick' is not loaded");
        }
    }

    private static function _exif()
    {
        if (!function_exists('exif_read_data')) {
            throw new Kwf_Exception("Function exif_read_data is not available");
        }
    }

    private static function _gd()
    {
        if (!extension_loaded('gd')) {
            throw new Kwf_Exception("Extension 'gd' is not loaded");
        }
    }

    private static function _fileinfo()
    {
        if (!function_exists('finfo_file')) {
            throw new Kwf_Exception("Extension 'fileinfo' is not loaded");
        }
    }

    private static function _simplexml()
    {
        if (!class_exists('SimpleXMLElement')) {
            throw new Kwf_Exception("Extension 'simplexml' is not loaded");
        }
    }

    private static function _tidy()
    {
        if (!extension_loaded('tidy')) {
            throw new Kwf_Exception("Extension 'tidy' is not loaded");
        }
    }

    private static function _pdo_mysql()
    {
        if (!extension_loaded('pdo_mysql')) {
            throw new Kwf_Exception("Extension 'pdo_mysql' is not loaded");
        }
    }

    private static function _system()
    {
        $out = shell_exec("ls");
        if (!$out) {
            throw new Kwf_Exception("executing 'ls' returned nothing");
        }
    }

    private static function _setup_kwf()
    {
        Kwf_Registry::get('config');
    }

    private static function _db_connection()
    {
        Kwf_Registry::get('db')->query("SHOW TABLES")->fetchAll();
    }
    private static function _git()
    {
        $gitVersion = exec("git --version", $out, $ret);
        if ($ret) {
            throw new Kwf_Exception("Git command failed");
        }
        if (!preg_match('#^git version ([0-9\\.]+)$#', $gitVersion, $m)) {
            throw new Kwf_Exception("Invalid git --version response");
        }
        $gitVersion = $m[1];
        if (version_compare($gitVersion, "1.5.0") < 0) {
            throw new Kwf_Exception("Invalid git version '$gitVersion', >= 1.5.0 is required");
        }
    }

    private static function _log_write()
    {
        if (file_exists('log/error/test-config-check')) {
            if (file_exists('log/error/test-config-check/test.log')) {
                unlink('log/error/test-config-check/test.log');
            }
            rmdir('log/error/test-config-check');
        }
        mkdir('log/error/test-config-check');
        file_put_contents('log/error/test-config-check/test.log', 'blah');
        if (file_get_contents('log/error/test-config-check/test.log') != 'blah') {
            throw new Kwf_Exception("reading test log failed");
        }
        unlink('log/error/test-config-check/test.log');
        rmdir('log/error/test-config-check');
    }

    private static function _temp_write()
    {
        if (!is_writeable('temp')) {
            throw new Kwf_Exception("temp is not writeable");
        }
        if (file_exists('temp/checkconfig-test')) unlink('temp/checkconfig-test');
        touch('temp/checkconfig-test');
        unlink('temp/checkconfig-test');
    }

    private static function _cache_write()
    {
        $dirs = array('cache');
        foreach (glob('cache/*') as $d) {
            if (is_dir($d)) {
                $dirs[] = $d;
            }
        }
        foreach ($dirs as $d) {
            if (!is_writeable($d)) {
                throw new Kwf_Exception("$d is not writeable");
            }
            if (file_exists($d.'/checkconfig-test')) unlink($d.'/checkconfig-test');
            touch($d.'/checkconfig-test');
            unlink($d.'/checkconfig-test');
        }
    }

    private static function _root_write()
    {
        if (!is_writeable(getcwd())) {
            //needed for moving bootstrap.php when doing clear-cache from webinterface
            throw new Kwf_Exception("root (".getcwd().") is not writeable");
        }
    }

    private static function _imagick_functionality_1()
    {
        if (!class_exists('Imagick', false)) {
            throw new Kwf_Exception("Imagick class doesn't exist");
        }
        $im = new Imagick();
        $im->readImage(dirname(__FILE__).'/Config/testImage.jpg');
        $im->scaleImage(10, 10);
        $im->setImagePage(0, 0, 0, 0);
        $im->setImageColorspace(Imagick::COLORSPACE_RGB);
        $im->getImageBlob();
        $im->destroy();
    }

    private static function _imagick_functionality_2()
    {
        if (!class_exists('Imagick', false)) {
            throw new Kwf_Exception("Imagick class doesn't exist");
        }
        $im = new Imagick();
        $im->readImage(dirname(__FILE__).'/Config/testImage.png');
        $im->scaleImage(10, 10);
        $im->setImagePage(0, 0, 0, 0);
        $im->setImageColorspace(Imagick::COLORSPACE_RGB);
        $im->getImageBlob();
        $im->destroy();
    }

    private static function _memory_limit()
    {
        $m = ini_get('memory_limit');
        if ($m != -1 && (int)$m < 128) throw new Kwf_Exception("need 128M, got $m");
    }

    private static function _uploads()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model');
        $dir = $m->getUploadDir();
        if (!file_exists($dir)) {
             throw new Kwf_Exception("Path for uploads does not eixst");
        }
        if (!is_writable($dir)) {
            throw new Kwf_Exception("Path for uploads is not writeable");
        }
    }

    private static function _setlocale()
    {
        $locale = setlocale(LC_ALL, 0); //backup locale

        $l = Kwf_Trl::getInstance()->trlc('locale', 'C', array(), Kwf_Trl::SOURCE_KWF, 'de');
        if (!setlocale(LC_ALL, explode(', ', $l))) {
            throw new Kwf_Exception("Locale not installed, tried: ".$l);
        }

        if (is_string($locale) && strpos($locale, ';')) {
            $locales = array();
            foreach (explode(';', $locale) as $l) {
                $tmp = explode('=', $l);
                $locales[$tmp[0]] = $tmp[1];
            }
            setlocale(LC_ALL, $locales);
        } else {
            setlocale(LC_ALL, $locale);
        }
    }

    private static function _apc()
    {
        if (!extension_loaded('apc')) {
            throw new Kwf_Exception("apc extension not loaded");
        }

        if (php_sapi_name() == 'cli') {
            if (!ini_get('apc.enable_cli')) {
                throw new Kwf_Exception("apc extension not enabled in cli");
            }
        }

        $info = apc_sma_info(false);
        if ($info['num_seg'] * $info['seg_size'] < 128*1000*1000) {
            throw new Kwf_Exception("apc memory size < 128");
        }
        $value = uniqid();
        if (!apc_store('foobar', $value)) {
            throw new Kwf_Exception("apc_store returned false");
        }
        if (apc_fetch('foobar') != $value) {
            throw new Kwf_Exception("apc_fetch returned something different");
        }
        while (strlen($value) < 1500) {
            $value .= chr(rand(0,255));
        }
        if (!apc_store('foobar', $value)) {
            throw new Kwf_Exception("apc_store returned false");
        }
        if (!apc_delete('foobar')) {
            throw new Kwf_Exception("apc_delete returned false");
        }
    }

    private static function _magic_quotes_gpc()
    {
        if (php_sapi_name()!= 'cli' // nur im web testen, die cli berührt das sowieso nicht
            && get_magic_quotes_gpc()
        ) {
            throw new Kwf_Exception("magic_quotes_gpc is turned on. Please allow disabling it in .htaccess or turn off in php.ini");
        }
    }
}
