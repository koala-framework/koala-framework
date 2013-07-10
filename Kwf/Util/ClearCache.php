<?php
class Kwf_Util_ClearCache
{
    const MODE_CLEAR = 'clear';
    const MODE_IMPORT = 'import';

    /**
     * @return Kwf_Util_ClearCache
     */
    public function getInstance()
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
        if (Kwf_Config::getValue('aws.simpleCacheCluster')) {
            $types[] = 'elastiCache';
        }
        if (class_exists('Memcache') && Kwf_Config::getValue('server.memcache.host')) $types[] = 'memcache';
        if (extension_loaded('apc')) $types[] = 'apc';
        if (extension_loaded('apc')) {
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
            Kwf_Util_Apc::callClearCacheByCli(array('files' => getcwd().'/cache/setup'.Kwf_Setup::CACHE_SETUP_VERSION.'.php'), Kwf_Util_Apc::SILENT);

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

    public final function clearCache($types = 'all', $output = false, $refresh = true, $options = array())
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

        $this->_clearCache($types, $output, $options);

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

            $this->_refreshCache($types, $output);
        }

        $skipOtherServers = isset($options['skipOtherServers']) ? $options['skipOtherServers'] : false;
        if (Kwf_Config::getValue('server.aws') && !$skipOtherServers) {
            $otherHostsTypes = $this->getCacheDirs();
            //add other types
            $otherHostsTypes[] = 'config';
            $otherHostsTypes[] = 'setup';
            $otherHostsTypes[] = 'component';
            $otherHostsTypes[] = 'events';
            $otherHostsTypes[] = 'trl';
            $otherHostsTypes = array_unique($otherHostsTypes);
            if (in_array('all', $types)) {
                //use all of $otherHostsTypes
            } else {
                $otherHostsTypes = array_intersect($otherHostsTypes, $types);
            }
            if ($otherHostsTypes) {
                $domains = Kwf_Util_Aws_Ec2_InstanceDnsNames::getOther();
                foreach ($domains as $domain) {
                    if ($output) {
                        echo "executing clear-cache on $domain:\n";
                    }
                    $cmd = "php bootstrap.php clear-cache --type=".implode(',', $otherHostsTypes).' --skip-other-servers';
                    $cmd = "ssh -o 'StrictHostKeyChecking no' $domain ".escapeshellarg('cd '.Kwf_Config::getValue('server.dir').'; '.$cmd);
                    passthru($cmd);
                    if ($output) {
                        echo "\n";
                    }
                }
            }
        }

        Kwf_Util_Maintenance::restoreMaintenanceBootstrap($output);

        Kwf_Component_ModelObserver::getInstance()->enable();
    }

    protected function _refreshCache($types, $output)
    {
    }

    private function _callApcUtil($type, $output, $options)
    {
        Kwf_Util_Apc::callClearCacheByCli(array('type' => $type), $output ? Kwf_Util_Apc::VERBOSE : Kwf_Util_Apc::SILENT, $options);
    }

    protected function _clearCache(array $types, $output, $options)
    {
        $skipOtherServers = isset($options['skipOtherServers']) ? $options['skipOtherServers'] : false;
        if (in_array('elastiCache', $types) && !$skipOtherServers) {
            //namespace used in Kwf_Cache_Simple
            $cache = Kwf_Cache_Simple::getZendCache();
            $mc = $cache->getBackend()->getMemcache();
            if ($mc->get('cache_namespace')) {
                $mc->increment('cache_namespace');
            }
        }
        if (in_array('memcache', $types)) {
            $cache = Kwf_Cache::factory('Core', 'Memcached', array(
                'lifetime'=>null,
                'automatic_cleaning_factor' => false,
                'automatic_serialization'=>true));
            $cache->clean();
            if ($output) echo "cleared:     memcache\n";
        }
        if (in_array('apc', $types)) {
            $this->_callApcUtil('user', $output, $options);
        }
        if (in_array('optcode', $types)) {
            $this->_callApcUtil('file', $output, $options);
        }
        if (in_array('setup', $types)) {
            if (file_exists('cache/setup'.Kwf_Setup::CACHE_SETUP_VERSION.'.php')) {
                if ($output) echo "cleared:     cache/setup".Kwf_Setup::CACHE_SETUP_VERSION.".php\n";
                unlink('cache/setup'.Kwf_Setup::CACHE_SETUP_VERSION.'.php');
            }
        }
        foreach ($this->getDbCacheTables() as $t) {
            if (in_array($t, $types)) {
                if ($t == 'cache_component' && !Kwf_Config::getValue('debug.componentCache.clearOnClearCache')) {
                    if ($output) echo "skipped:     $t (won't delete, use clear-view-cache to clear)\n";
                    continue;
                }
                Zend_Registry::get('db')->query("TRUNCATE TABLE $t");
                if ($output) echo "cleared db:  $t\n";
            }
        }
        foreach ($this->getCacheDirs() as $d) {
            if (in_array($d, $types)) {
                if (is_dir("cache/$d")) {
                    $this->_removeDirContents("cache/$d");
                } else if (is_dir($d)) {
                    $this->_removeDirContents($d);
                }
                if ($output) echo "cleared dir: $d cache\n";
            }
        }
        if (in_array('assets', $types)) {
            Kwf_Assets_Cache::getInstance()->clean();
                if ($output) echo "cleared:     assets cache\n";
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

    private function _removeDirContents($path)
    {
        $dir = new DirectoryIterator($path);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isFile() && $fileinfo->getFilename() != '.gitignore' && substr($fileinfo->getFilename(), 0, 4) != '.nfs') {
                unlink($fileinfo->getPathName());
            } elseif (!$fileinfo->isDot() && $fileinfo->isDir() && $fileinfo->getFilename() != '.svn') {
                $this->_removeDirContents($fileinfo->getPathName());
                @rmdir($fileinfo->getPathName());
            }
        }
    }
}
