<?php
class Kwf_Util_ClearCache
{
    const MODE_CLEAR = 'clear';
    const MODE_IMPORT = 'import';

    /**
     * @return Kwf_Util_ClearCache
     */
    public static function getInstance()
    {
        static $i;
        if (!isset($i)) {
            $c = Kwf_Registry::get('config')->clearCacheClass;
            if (!$c) $c = 'Kwf_Util_ClearCache';
            $i = new $c();
        }
        return $i;
    }

    public final function getCacheDirs($mode = self::MODE_CLEAR)
    {
        return $this->_getCacheDirs($mode);
    }

    protected function _getCacheDirs($mode = self::MODE_CLEAR)
    {
        $ret = array();
        foreach (new DirectoryIterator('cache') as $d) {
            if ($d->isDir() && substr($d->getFilename(), 0, 1) != '.') {
                if ($d->getFilename() == 'searchindex') continue;
                if ($d->getFilename() == 'fulltext') continue;
                $ret[] = $d->getFilename();
            }
        }
        if (Kwf_Registry::get('config')->server->cacheDirs) {
            foreach (Kwf_Registry::get('config')->server->cacheDirs as $d) {
                if (substr($d, -2)=='/*') {
                    foreach (new DirectoryIterator(substr($d, 0, -1)) as $i) {
                        if ($i->isDir() && substr($i->getFilename(), 0, 1) != '.') {
                            $ret[] = substr($d, 0, -1).$i->getFilename();
                        }
                    }
                } else {
                    $ret[] = $d;
                }
            }
        }
        return $ret;
    }

    public function getDbCacheTables()
    {
        $ret = array();
        try {
            if (!Zend_Registry::get('db')) return $ret;
        } catch (Exception $e) {
            return $ret;
        }
        $tables = Zend_Registry::get('db')->fetchCol('SHOW TABLES');
        foreach ($tables as $table) {
            if (substr($table, 0, 6) == 'cache_') {
                $ret[] = $table;
            }
        }
        return $ret;
    }

    public function getTypes()
    {

        $types = array('all');
        if (class_exists('Memcache') && Kwf_Config::getValue('server.memcache.host')) $types[] = 'memcache';
        if (extension_loaded('apc')) $types[] = 'apc';
        if (extension_loaded('apc') || extension_loaded('Zend OPcache')) {
            $types[] = 'optcode';
        }
        $types[] = 'setup';
        $types = array_merge($types, $this->getCacheDirs());
        $types = array_merge($types, $this->getDbCacheTables());
        if (Kwf_Config::getValue('assetsCacheUrl')) {
            $types[] = 'assetsServer';
        }
        return $types;
    }

    private function _refresh($type, $output)
    {
        ini_set('memory_limit', '256M');
        if ($type == 'setup') {

            file_put_contents('cache/setup'.Kwf_Setup::CACHE_SETUP_VERSION.'.php', Kwf_Util_Setup::generateCode(Kwf_Setup::$configClass));
            Kwf_Util_Apc::callClearCacheByCli(array('files' => getcwd().'/cache/setup'.Kwf_Setup::CACHE_SETUP_VERSION.'.php'));

        } else if ($type == 'config') {

            $configClass = Kwf_Setup::$configClass;
            $config = new $configClass(Kwf_Setup::getConfigSection());
            $cacheId = 'config_'.str_replace('-', '_', Kwf_Setup::getConfigSection());
            Kwf_Config_Cache::getInstance()->save($config, $cacheId);

            Kwf_Config_Web::clearInstances();
            Kwf_Registry::set('config', $config);
            Kwf_Registry::set('configMtime', Kwf_Config_Cache::getInstance()->test($cacheId));

        } else if ($type == 'component') {

            Kwf_Component_Settings::resetSettingsCache();
            Kwf_Component_Settings::_getSettingsCached();

        } else if ($type == 'assets') {

            $loader = new Kwf_Assets_Loader();
            $loader->getDependencies()->getMaxFileMTime(); //this is expensive and gets cached in filesystem

            $webCodeLanguage = Kwf_Registry::get('config')->webCodeLanguage;
            $_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip';
            $assets = Kwf_Registry::get('config')->assets->toArray();
            $assetTypes = array();
            foreach ($assets as $assetsType => $v) {
                if ($assetsType == 'dependencies') continue;
                if ($output) echo $assetsType.' ';
                $urls = $loader->getDependencies()->getAssetUrls($assetsType, 'js', 'web', Kwf_Component_Data_Root::getComponentClass(), $webCodeLanguage);
                $urls = array_merge($urls, $loader->getDependencies()->getAssetUrls($assetsType, 'css', 'web', Kwf_Component_Data_Root::getComponentClass(), $webCodeLanguage));
                foreach ($urls as $url) {
                    $url = preg_replace('#^/assets/#', '', $url);
                    $url = preg_replace('#\\?v=\d+(&t=\d+)?$#', '', $url);
                    $loader->getFileContents($url);
                }
            }

        } else if ($type == 'events') {

            Kwf_Component_Events::getAllListeners();

        } else if ($type == 'cache_users') {

            Kwf_Registry::get('userModel')->getKwfModel()->synchronize(Kwf_Model_MirrorCache::SYNC_ALWAYS);

        } else if ($type == 'users cleanup') {

            // alle zeilen löschen die zuviel sind in kwf_users
            // nötig für lokale tests
            $db = Kwf_Registry::get('db');
            $dbRes = $db->query('SELECT COUNT(*) `cache_users_count` FROM `cache_users`')->fetchAll();
            if ($dbRes[0]['cache_users_count'] >= 1) {
                $dbRes = $db->query('SELECT COUNT(*) `sort_out_count` FROM `kwf_users`
                        WHERE NOT (SELECT cache_users.id
                                    FROM cache_users
                                    WHERE cache_users.id = kwf_users.id
                                    )'
                )->fetchAll();
                $db->query('DELETE FROM `kwf_users`
                        WHERE NOT (SELECT cache_users.id
                                    FROM cache_users
                                    WHERE cache_users.id = kwf_users.id
                                    )'
                );
                return $dbRes[0]['sort_out_count']." rows cleared";
            } else {
                return "skipping: cache_users is empty";
            }

        } else if ($type == 'trl') {

            $webCodeLanguage = Kwf_Registry::get('config')->webCodeLanguage;
            if ($webCodeLanguage != 'en') {
                Kwf_Trl::getInstance()->trl('Login', array(), 'en', $webCodeLanguage);
            }
        }
    }

    protected function _getRefreshTypes($types)
    {
        $refreshTypes = array();
        $refreshTypes[] = 'config';
        $refreshTypes[] = 'setup';
        if (Kwf_Component_Data_Root::getComponentClass()) {
            $refreshTypes[] = 'component';
        }
        $refreshTypes[] = 'trl';
        $refreshTypes[] = 'assets';
        if (in_array('cache_component', $this->getDbCacheTables())
            && (in_array('component', $types) || in_array('cache_component', $types))
        ) {
            $refreshTypes[] = 'events';
        }
        if (in_array('cache_users', $types)) {
            $refreshTypes[] = 'cache_users';
        }

        try {
            $db = Kwf_Registry::get('db');
        } catch (Exception $e) {
            $db = false;
        }
        if ((in_array('cache_users', $types) || in_array('model', $types)) && $db) {
            $tables = Kwf_Registry::get('db')->fetchCol('SHOW TABLES');
            if (in_array('kwf_users', $tables) && in_array('cache_users', $tables)) {
                if (Kwf_Registry::get('config')->cleanupKwfUsersOnClearCache) {
                    $refreshTypes[] = 'users cleanup';
                }
            }
        }
        return $refreshTypes;
    }

    public final function clearCache($types = 'all', $output = false, $refresh = true, $server = null)
    {
        Kwf_Component_ModelObserver::getInstance()->disable();

        Kwf_Util_Maintenance::writeMaintenanceBootstrap($output);

        $refreshTypes = array();
        if ($types == 'all') {
            $types = $this->getTypes();
            $refreshTypes = $this->_getRefreshTypes($types);
        } else {
            if (!is_array($types)) {
                $types = explode(',', $types);
            }
            $refreshTypes = $types;
        }

        $this->_clearCache($types, $output, $server);

        if ($refresh) {
            if ($output) echo "\n";
            foreach ($refreshTypes as $type) {
                if ($output) echo "Refresh $type".str_repeat('.', 15-strlen($type));
                $t = microtime(true);
                try {
                    $result = $this->_refresh($type, $output);
                    if (!$result) $result= 'OK';
                    $success = true;
                } catch (Exception $e) {
                    if ($output) echo " [\033[01;31mERROR\033[00m] $e\n";
                    continue;
                }
                if ($output) {
                    echo " [\033[00;32m".$result."\033[00m]";
                    echo " ".round((microtime(true)-$t)*1000)."ms";
                    echo "\n";
                }
            }

            $this->_refreshCache($types, $output, $server);
        }

        Kwf_Util_Maintenance::restoreMaintenanceBootstrap($output);

        Kwf_Component_ModelObserver::getInstance()->enable();
    }

    protected function _refreshCache($types, $output, $server)
    {
    }

    private function _callApcUtil($type, $outputType, $output)
    {
        $result = Kwf_Util_Apc::callClearCacheByCli(array('type' => $type));
        if ($output) {
            if ($result['result']) {
                echo "cleared:     $outputType (".$result['time']."ms) " . $result['message'] . "\n";
            } else {
                $url = '';
                if (isset($result['url'])) {
                    $url = $result['url'];
                    if ($result['url2']) $url .= ' / ' . $result['url2'];
                    $url = " ($url)";
                }
                echo "error:       $outputType$url\n" . $result['message'] . "\n\n";
            }
        }
    }

    protected function _clearCache(array $types, $output, $server)
    {
        if (in_array('memcache', $types)) {
            if ($server) {
                if ($output) echo "ignored:     memcache\n";
            } else {
                $cache = Kwf_Cache::factory('Core', 'Memcached', array(
                    'lifetime'=>null,
                    'automatic_cleaning_factor' => false,
                    'automatic_serialization'=>true));
                $cache->clean();
                if ($output) echo "cleared:     memcache\n";
            }
        }
        if (in_array('apc', $types)) {
            if ($server) {
                if ($output) echo "ignored:     apc\n";
            } else {
                $this->_callApcUtil('user', 'apc', $output);
            }
        }
        if (in_array('optcode', $types)) {
            if ($server) {
                if ($output) echo "ignored:     optcode\n";
            } else {
                $this->_callApcUtil('file', 'optcode', $output);
            }
        }
        if (in_array('setup', $types)) {
            if (file_exists('cache/setup'.Kwf_Setup::CACHE_SETUP_VERSION.'.php')) {
                if ($output) echo "cleared:     cache/setup".Kwf_Setup::CACHE_SETUP_VERSION.".php\n";
                unlink('cache/setup'.Kwf_Setup::CACHE_SETUP_VERSION.'.php');
            }
        }
        foreach ($this->getDbCacheTables() as $t) {
            if ($server) {
                if ($output) echo "ignored db:  $t\n";
            } else {
                if (in_array($t, $types)) {
                    if ($t == 'cache_component') {
                        try {
                            $cnt = Zend_Registry::get('db')->query("SELECT COUNT(*) FROM $t WHERE deleted=0")->fetchColumn();
                            if ($cnt > 5000) {
                                if ($output) echo "skipped:     $t (won't delete $cnt entries, use clear-view-cache to clear)\n";
                                continue;
                            }
                        } catch (Exception $e) {}
                    }
                    Zend_Registry::get('db')->query("TRUNCATE TABLE $t");
                    if ($output) echo "cleared db:  $t\n";
                }
            }
        }
        foreach ($this->getCacheDirs() as $d) {
            if (in_array($d, $types)) {
                if (is_dir("cache/$d")) {
                    $this->_removeDirContents("cache/$d", $server);
                } else if (is_dir($d)) {
                    $this->_removeDirContents($d, $server);
                }
                if ($output) echo "cleared dir: $d cache\n";
            }
        }
        if (in_array('assetsServer', $types)) {
            $url = Kwf_Config::getValue('assetsCacheUrl').'?web='.Kwf_Config::getValue('application.id').'&section='.Kwf_Setup::getConfigSection().'&clear';
            try {
                $out = file_get_contents($url);
                if ($output) echo "cleared:     assetsServer [".$out."]\n";
            } catch (Exception $e) {
                if ($output) echo "cleared:     assetsServer [ERROR] ".$e->getMessage()."\n";
            }
        }
    }

    private function _removeDirContents($path, $server)
    {
        if ($server) {
            $cmd = "clear-cache-dir --path=$path";
            $cmd = "sshvps $server->user@$server->host $server->dir $cmd";
            $cmd = "sudo -u vps $cmd";
            passthru($cmd, $ret);
            if ($ret != 0) {
                throw new Kwf_ClientException("Clearing remote cache '$path' failed");
            }
        } else {
            $dir = new DirectoryIterator($path);
            foreach ($dir as $fileinfo) {
                if ($fileinfo->isFile() && $fileinfo->getFilename() != '.gitignore' && substr($fileinfo->getFilename(), 0, 4) != '.nfs') {
                    unlink($fileinfo->getPathName());
                } elseif (!$fileinfo->isDot() && $fileinfo->isDir() && $fileinfo->getFilename() != '.svn') {
                    $this->_removeDirContents($fileinfo->getPathName(), $server);
                    @rmdir($fileinfo->getPathName());
                }
            }
        }
    }
}
