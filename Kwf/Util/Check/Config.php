<?php
class Kwf_Util_Check_Config
{
    const RESULT_OK = 'ok';
    const RESULT_FAILED = 'failed';
    const RESULT_WARNING = 'warning';

    public static function dispatch()
    {
        Kwf_Loader::registerAutoload();
        if (php_sapi_name() == 'cli') {
            $quiet = isset($_SERVER['argv'][2]) && trim($_SERVER['argv'][2]) == 'quiet';
        } else {
            $role = false;
            try {
                $role = Kwf_Registry::get('userModel')->getAuthedUserRole();
            } catch (Exception $e) {}
            if ($role != 'admin') {
                if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER']!='vivid' || $_SERVER['PHP_AUTH_PW']!='planet') {
                    header('WWW-Authenticate: Basic realm="Check Config"');
                    throw new Kwf_Exception_AccessDenied();
                }
            }
            $quiet = isset($_GET['quiet']);
        }
        self::_check($quiet);
    }

    public static function getCheckResults()
    {
        $ret = array();
        $checks = self::_getChecks();
        foreach ($checks as $k=>$i) {
            try {
                $result = call_user_func(array('Kwf_Util_Check_Config', '_'.$k));
            } catch (Exception $e) {
                $result = array(
                    'status' => self::RESULT_FAILED,
                    'message' => $e->getMessage(),
                );
            }
            $ret[] = array(
                'checkText' => str_replace('apache2handler', 'apache2', php_sapi_name()).' '.$i['name'],
                'status' => $result['status'],
                'message' => isset($result['message']) ? $result['message'] : '',
            );
        }
//         if (php_sapi_name()!= 'cli') {
//             passthru(Kwf_Config::getValue('server.phpCli')." bootstrap.php check-config quiet 2>&1", $ret);
//             if ($ret) echo "\nFAILED CLI";
//         }
        return $ret;
    }

    private static function _getChecks()
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
        $checks['write_perm'] = array(
            'name' => 'write permissions'
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
        return $checks;
    }

    private static function _check($quiet = false)
    {
        $results = self::getCheckResults();
        if ($quiet) {
            foreach ($results as $i) {
                if ($i['status'] == self::RESULT_FAILED) {
                    echo "\nFAILED: ".$i['checkText'].' '.$i['message'];
                } else if ($i['status'] == self::RESULT_WARNING) {
                    echo "\nWARNING: ".$i['checkText'].' '.$i['message'];
                }
            }
            if (php_sapi_name()!= 'cli') {
                passthru(Kwf_Config::getValue('server.phpCli')." bootstrap.php check-config quiet 2>&1", $ret);
                if ($ret) echo "\nFAILED CLI";
            }
        } else {
            foreach ($results as $i) {
                echo "<p style=\"margin:0;\">";
                echo $i['checkText'].': ';
                if ($i['status'] == self::RESULT_OK) {
                    echo "<span style=\"background-color:green\">OK</span>";
                } else if ($i['status'] == self::RESULT_WARNING) {
                    echo "<span style=\"background-color:yellow\">WARNING</span>";
                } else if ($i['status'] == self::RESULT_FAILED) {
                    echo "<span style=\"background-color:red\">FAILED</span>";
                } else {
                    throw new Kwf_Exception("Unknown result");
                }
                if ($i['message']) {
                    echo ': '.$i['message'];
                }
                echo "</p>";
            }

            if (php_sapi_name()!= 'cli') {
                passthru(Kwf_Config::getValue('server.phpCli')." bootstrap.php check-config 2>&1", $ret);
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
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _imagick()
    {
        //if (!extension_loaded('imagick')) {
        if (!class_exists('Imagick')) {
            if (!extension_loaded('gd')) {
                return array(
                    'status' => self::RESULT_FAILED,
                    'message' => "Extension 'imagick' is not loaded. Fallback extension 'gd' is also not loaded."
                );
            }
            return array(
                'status' => self::RESULT_WARNING,
                'message' => "Extension 'imagick' is not loaded. 'gd' is used as fallback."
            );
        }

        $im = new Imagick();
        $im->readImage(dirname(__FILE__).'/Config/testImage.jpg');
        $im->scaleImage(10, 10);
        $im->setImagePage(0, 0, 0, 0);
        $im->setImageColorspace(Imagick::COLORSPACE_RGB);
        $im->getImageBlob();
        $im->destroy();

        $im = new Imagick();
        $im->readImage(dirname(__FILE__).'/Config/testImage.png');
        $im->scaleImage(10, 10);
        $im->setImagePage(0, 0, 0, 0);
        $im->setImageColorspace(Imagick::COLORSPACE_RGB);
        $im->getImageBlob();
        $im->destroy();

        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _exif()
    {
        if (!function_exists('exif_read_data')) {
            return array(
                'status' => self::RESULT_WARNING,
                'message' => "Function exif_read_data is not available, rotating images according to exif data won't be possible"
            );
        }
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _fileinfo()
    {
        if (!function_exists('finfo_file')) {
            return array(
                'status' => self::RESULT_WARNING,
                'message' => "Extension 'fileinfo' is not loaded"
            );
        }

        $mime = Kwf_Uploads_Row::detectMimeType(false, file_get_contents(KWF_PATH.'/images/information.png'));
        if ($mime != 'image/png') {
            return array(
                'status' => self::RESULT_WARNING,
                'message' => "fileinfo returned wrong information: $mime"
            );
        }

        $mime = Kwf_Uploads_Row::detectMimeType(false, file_get_contents(KWF_PATH.'/tests/Kwf/Uploads/DetectMimeType/sample.docx'));
        if (!($mime == 'application/msword' || $mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')) {
            return array(
                'status' => self::RESULT_WARNING,
                'message' => "fileinfo returned wrong information: $mime"
            );
        }

        $mime = Kwf_Uploads_Row::detectMimeType(false, file_get_contents(KWF_PATH.'/tests/Kwf/Uploads/DetectMimeType/sample.odt'));
        if ($mime != 'application/vnd.oasis.opendocument.text') {
            return array(
                'status' => self::RESULT_WARNING,
                'message' => "fileinfo returned wrong information: $mime"
            );
        }
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _simplexml()
    {
        if (!class_exists('SimpleXMLElement')) {
            throw new Kwf_Exception("Extension 'simplexml' is not loaded");
        }
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _tidy()
    {
        if (!extension_loaded('tidy')) {
            return array(
                'status' => self::RESULT_WARNING,
                'message' => "Extension 'tidy' is not loaded."
            );
        }
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _pdo_mysql()
    {
        if (!extension_loaded('pdo_mysql')) {
            throw new Kwf_Exception("Extension 'pdo_mysql' is not loaded");
        }
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _system()
    {
        $out = shell_exec("ls");
        if (!$out) {
            throw new Kwf_Exception("executing 'ls' returned nothing");
        }
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _setup_kwf()
    {
        Kwf_Registry::get('config');
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _db_connection()
    {
        if (Kwf_Registry::get('db')) {
            Kwf_Registry::get('db')->query("SHOW TABLES")->fetchAll();
        }
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _git()
    {
        $gitVersion = exec("git --version", $out, $ret);
        if ($ret) {
            return array(
                'status' => self::RESULT_WARNING,
                'message' => "Git command is not available."
            );
        }
        if (!preg_match('#^git version ([0-9\\.]+)$#', $gitVersion, $m)) {
            throw new Kwf_Exception("Invalid git --version response");
        }
        $gitVersion = $m[1];
        if (version_compare($gitVersion, "1.5.0") < 0) {
            throw new Kwf_Exception("Invalid git version '$gitVersion', >= 1.5.0 is required");
        }
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _write_perm()
    {
        //log folders
        if (file_exists('log/error/test-config-check')) {
            if (file_exists('log/error/test-config-check/test.log')) {
                unlink('log/error/test-config-check/test.log');
            }
            rmdir('log/error/test-config-check');
        }
        mkdir('log/error/test-config-check');
        file_put_contents('log/error/test-config-check/test.log', 'blah');
        if (file_get_contents('log/error/test-config-check/test.log') != 'blah') {
            return array(
                'status' => self::RESULT_FAILED,
                'message' => "reading test log failed"
            );
        }
        unlink('log/error/test-config-check/test.log');
        rmdir('log/error/test-config-check');

        //temp folder
        if (!is_writeable('temp')) {
            return array(
                'status' => self::RESULT_FAILED,
                'message' => "temp is not writeable"
            );
        }
        if (file_exists('temp/checkconfig-test')) unlink('temp/checkconfig-test');
        touch('temp/checkconfig-test');
        unlink('temp/checkconfig-test');

        //cache folders
        $dirs = array('cache');
        foreach (glob('cache/*') as $d) {
            if (is_dir($d)) {
                $dirs[] = $d;
            }
        }
        foreach ($dirs as $d) {
            if (!is_writeable($d)) {
                return array(
                    'status' => self::RESULT_FAILED,
                    'message' => "$d is not writeable"
                );
            }
            if (file_exists($d.'/checkconfig-test')) unlink($d.'/checkconfig-test');
            touch($d.'/checkconfig-test');
            unlink($d.'/checkconfig-test');
        }
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _memory_limit()
    {
        $m = ini_get('memory_limit');
        if ($m != -1 && (int)$m < 128) throw new Kwf_Exception("need 128M, got $m");
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _uploads()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model');
        $dir = $m->getUploadDir();
        if (!file_exists($dir)) {
            return array(
                'status' => self::RESULT_FAILED,
                'message' => "Uploads path '$dir' does not exist"
            );
        }
        if (!is_writable($dir)) {
            return array(
                'status' => self::RESULT_FAILED,
                'message' => "Uploads path '$dir' is not writeable"
            );
        }
        return array(
            'status' => self::RESULT_OK,
        );
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
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _apc()
    {
        if (!extension_loaded('apc')) {
            return array(
                'status' => self::RESULT_WARNING,
                'message' => "Extension 'apc' not loaded"
            );
        }

        if (php_sapi_name() == 'cli') {
            if (!ini_get('apc.enable_cli')) {
                throw new Kwf_Exception("apc extension not enabled in cli");
            }
        }

        $info = apc_sma_info(false);
        if ($info['num_seg'] * $info['seg_size'] < 128*1000*1000) {
            return array(
                'status' => self::RESULT_WARNING,
                'message' => "apc memory size is < 128MB"
            );
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
        return array(
            'status' => self::RESULT_OK,
        );
    }

    private static function _magic_quotes_gpc()
    {
        if (php_sapi_name()!= 'cli' // nur im web testen, die cli berührt das sowieso nicht
            && get_magic_quotes_gpc()
        ) {
            return array(
                'status' => self::RESULT_WARNING,
                'message' => "magic_quotes_gpc is turned on. Please allow disabling it in .htaccess or turn off in php.ini"
            );
        }
        return array(
            'status' => self::RESULT_OK,
        );
    }
}
