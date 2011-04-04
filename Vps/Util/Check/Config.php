<?php
class Vps_Util_Check_Config
{
    public function check()
    {
        $checks = array();
        $checks['php'] = array(
            'name' => 'Php >= 5.2'
        );
        $checks['memcache'] = array(
            'name' => 'memcache Php extension'
        );
        $checks['imagick'] = array(
            'name' => 'imagick Php extension'
        );
        $checks['gd'] = array(
            'name' => 'gd Php extension'
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
        $checks['log_write'] = array(
            'name' => 'log_write permissions'
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

        //ab hier wird die config geladen
        $checks['setup_vps'] = array(
            'name' => 'loading vps'
        );
        $checks['memcache_connection'] = array(
            'name' => 'memcache connection'
        );
        $checks['memcache_connection2'] = array(
            'name' => 'memcache connection2'
        );
        $checks['db_connection'] = array(
            'name' => 'db connection'
        );
        $checks['svn'] = array(
            'name' => 'svn'
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
        $checks['fileinfo_functionality'] = array(
            'name' => 'fileinfo functionality'
        );
        $checks['apc'] = array(
            'name' => 'apc'
        );


        $res = '<h3>';
        if (php_sapi_name()!= 'cli') {
            $res .= "Test Webserver...\n";
        } else {
            $res .= "Test Cli...\n";
        }
        $res .= '</h3>';
        foreach ($checks as $k=>$i) {
            $res .= "<p style=\"margin:0;\">";
            $res .= $i['name'].': ';
            try {
                call_user_func(array('Vps_Util_Check_Config', '_'.$k));
                $res .= "<span style=\"background-color:green\">OK</span>";
            } catch (Exception $e) {
                $res .= "<span style=\"background-color:red\">FAILED:</span> ".$e->getMessage();
            }
            $res .= "</p>";
        }

        echo $res;
        if (php_sapi_name()!= 'cli') {
            passthru("php bootstrap.php check-config", $ret);
            if ($ret) {
                echo "<span style=\"background-color:red\">FAILED:</span>";
            }
            echo  '<br /><br /> all tests finished';
        }
        exit;
    }

    private static function _php()
    {
        if (version_compare(PHP_VERSION, '5.1.6') < 0) {
            throw new Vps_Exception("Php version '".PHP_VERSION."' is too old");
        }
    }

    private static function _memcache()
    {
        if (!extension_loaded('memcache')) {
            throw new Vps_Exception("Extension 'memcache' is not loaded");
        }
    }

    private static function _imagick()
    {
        //if (!extension_loaded('imagick')) {
        if (!class_exists('Imagick')) {
            throw new Vps_Exception("Extension 'imagick' is not loaded");
        }
    }

    private static function _gd()
    {
        if (!extension_loaded('gd')) {
            throw new Vps_Exception("Extension 'gd' is not loaded");
        }
    }

    private static function _fileinfo()
    {
        if (!function_exists('finfo_file')) {
            throw new Vps_Exception("Extension 'fileinfo' is not loaded");
        }
    }

    private static function _simplexml()
    {
        if (!class_exists('SimpleXMLElement')) {
            throw new Vps_Exception("Extension 'simplexml' is not loaded");
        }
    }

    private static function _tidy()
    {
        if (!extension_loaded('tidy')) {
            throw new Vps_Exception("Extension 'tidy' is not loaded");
        }
    }

    private static function _pdo_mysql()
    {
        if (!extension_loaded('pdo_mysql')) {
            throw new Vps_Exception("Extension 'pdo_mysql' is not loaded");
        }
    }

    private static function _system()
    {
        $out = shell_exec("ls");
        if (!$out) {
            throw new Vps_Exception("executing 'ls' returned nothing");
        }
    }

    private static function _setup_vps()
    {
        Vps_Setup::setUpVps();
    }

    private static function _memcache_connection()
    {
        $cache = Vps_Cache::factory('Core', 'Memcached');
        $cache->save('foo', 'bar');
        if ($cache->load('bar') != 'foo') {
            throw new Vps_Exception("Memcache doesn't return the saved value correctly");
        }
    }

    private static function _memcache_connection2()
    {
        $memcache = new Memcache;
        $memcacheSettings = Vps_Registry::get('config')->server->memcache;
        if (!$memcache->addServer($memcacheSettings->host, $memcacheSettings->port, true, 1, 1, 1)) {
            throw new Vps_Exception("addServer returned false");
        }
        $value = uniqid();
        if (!$memcache->set('check-config-test', $value)) {
            throw new Vps_Exception("set returned false");
        }
        if ($memcache->get('check-config-test') != $value) {
            throw new Vps_Exception("get returned a different value");
        }
    }

    private static function _db_connection()
    {
        Vps_Registry::get('db')->query("SHOW TABLES")->fetchAll();
    }
    private static function _svn()
    {
        exec("svn --version", $out, $ret);
        if ($ret) {
            throw new Vps_Exception("Svn command failed");
        }
    }
    private static function _git()
    {
        $gitVersion = exec("git --version", $out, $ret);
        if ($ret) {
            throw new Vps_Exception("Git command failed");
        }
        if (!preg_match('#^git version ([0-9\\.]+)$#', $gitVersion, $m)) {
            throw new Vps_Exception("Invalid git --version response");
        }
        $gitVersion = $m[1];
        if (version_compare($gitVersion, "1.5.0") < 0) {
            throw new Vps_Exception("Invalid git version '$gitVersion', >= 1.5.0 is required");
        }
    }

    private static function _log_write()
    {
        if (file_exists('application/log/error/test-config-check')) {
            if (file_exists('application/log/error/test-config-check/test.log')) {
                unlink('application/log/error/test-config-check/test.log');
            }
            rmdir('application/log/error/test-config-check');
        }
        mkdir('application/log/error/test-config-check');
        file_put_contents('application/log/error/test-config-check/test.log', 'blah');
        if (file_get_contents('application/log/error/test-config-check/test.log') != 'blah') {
            throw new Vps_Exception("reading test log failed");
        }
        unlink('application/log/error/test-config-check/test.log');
        rmdir('application/log/error/test-config-check');
    }

    private static function _imagick_functionality_1()
    {
        if (!class_exists('Imagick', false)) {
            throw new Vps_Exception("Imagick class doesn't exist");
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
            throw new Vps_Exception("Imagick class doesn't exist");
        }
        $im = new Imagick();
        $im->readImage(VPS_PATH.'/images/information.png');
        $im->scaleImage(10, 10);
        $im->setImagePage(0, 0, 0, 0);
        $im->setImageColorspace(Imagick::COLORSPACE_RGB);
        $im->getImageBlob();
        $im->destroy();
    }

    private static function _memory_limit()
    {
        $m = ini_get('memory_limit');
        if ((int)$m < 128) throw new Vps_Exception("need 128M, got $m");
    }

    private static function _uploads()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Uploads_Model');
        $dir = $m->getUploadDir();
        if (!file_exists($dir)) {
             throw new Vps_Exception("Path for uploads does not eixst");
        }
        if (!is_writable($dir)) {
            throw new Vps_Exception("Path for uploads is not writeable");
        }
    }

    private static function _setlocale()
    {
        $locale = setlocale(LC_ALL, 0); //backup locale

        $l = Vps_Trl::getInstance()->trlc('locale', 'C', array(), Vps_Trl::SOURCE_VPS, 'de');
        if (!setlocale(LC_ALL, explode(', ', $l))) {
            throw new Vps_Exception("Locale not installed, tried: ".$l);
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

    private static function _fileinfo_functionality()
    {
        $mime = Vps_Uploads_Row::detectMimeType(false, file_get_contents(VPS_PATH.'/images/information.png'));
        if ($mime != 'image/png') {
            throw new Vps_Exception("fileinfo returned wrong information");
        }
    }

    private static function _apc()
    {
        if (php_sapi_name() == 'cli') return; //apc gibts im cli nicht

        if (!extension_loaded('apc')) {
            throw new Vps_Exception("apc extension not loaded");
        }
        $info = apc_sma_info(false);
        if ($info['num_seg'] * $info['seg_size'] < 128*1000*1000) {
            throw new Vps_Exception("apc memory size < 128");
        }
        $value = uniqid();
        if (!apc_store('foobar', $value)) {
            throw new Vps_Exception("apc_store returned false");
        }
        if (apc_fetch('foobar') != $value) {
            throw new Vps_Exception("apc_fetch returned something different");
        }
        while (strlen($value) < 1500) {
            $value .= chr(rand(0,255));
        }
        if (!apc_store('foobar', $value)) {
            throw new Vps_Exception("apc_store returned false");
        }
        if (!apc_delete('foobar')) {
            throw new Vps_Exception("apc_delete returned false");
        }
    }
}
