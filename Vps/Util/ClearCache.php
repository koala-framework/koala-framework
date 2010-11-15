<?php
class Vps_Util_ClearCache
{
    const MODE_CLEAR = 'clear';
    const MODE_IMPORT = 'import';

    public function getInstance()
    {
        static $i;
        if (!isset($i)) {
            $c = Vps_Registry::get('config')->clearCacheClass;
            if (!$c) $c = 'Vps_Util_ClearCache';
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
        foreach (new DirectoryIterator('application/cache') as $d) {
            if ($d->isDir() && substr($d->getFilename(), 0, 1) != '.') {
                if ($d->getFilename() == 'searchindex') continue;
                if ($d->getFilename() == 'fulltext') continue;
                $ret[] = $d->getFilename();
            }
        }
        if (Vps_Registry::get('config')->server->cacheDirs) {
            foreach (Vps_Registry::get('config')->server->cacheDirs as $d) {
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
        if (class_exists('Memcache')) $types[] = 'memcache';
        $types = array_merge($types, $this->getCacheDirs());
        $types = array_merge($types, $this->getDbCacheTables());
        return $types;
    }

    public final function clearCache($types = 'all', $output = false, $refresh = true, $server = null)
    {
        if ($types == 'all') {
            $types = $this->getTypes();
        } else {
            if (!is_array($types)) {
                $types = explode(',', $types);
            }
        }
        $this->_clearCache($types, $output, $server);

        if ($refresh) {
            if ($output) echo "\n";
            if ($output) echo "Refresh settings.......";
            Vps_Config_Web::clearInstances();
            $config = Vps_Config_Web::getInstance(Vps_Setup::getConfigSection());
            Vps_Registry::set('config', $config);
            Vps_Registry::set('configMtime', Vps_Config_Web::getInstanceMtime(Vps_Setup::getConfigSection()));
            if ($output) echo " [\033[00;32mOK\033[00m]\n";

            if (Vps_Component_Data_Root::getComponentClass()) {
                if ($output) echo "Refresh component......";
                Vpc_Abstract::getSettingMtime();
                if ($output) echo " [\033[00;32mOK\033[00m]\n";
            }

            if (in_array('cache_component_meta', $this->getDbCacheTables())
                && (in_array('component', $types) || in_array('cache_component_meta', $types))
            ) {
                if ($output) echo "Refresh static cache...";
                try {
                    Vps_Component_Cache::refreshStaticCache();
                    if ($output) echo " [\033[00;32mOK\033[00m]\n";
                } catch (Exception $e) {
                    if ($output) echo " [\033[01;31mERROR\033[00m] $e\n";
                }
            }
            try {
                $db = Vps_Registry::get('db');
            } catch (Exception $e) {
                $db = false;
            }
            if ((in_array('cache_users', $types) || in_array('model', $types)) && $db) {
                $tables = Vps_Registry::get('db')->fetchCol('SHOW TABLES');
                if (in_array('vps_users', $tables) && in_array('cache_users', $tables)) {
                    if ($output) echo "Synchronize users......";
                    try {
                        Vps_Registry::get('userModel')->synchronize(Vps_Model_MirrorCache::SYNC_ALWAYS);
                        if ($output) echo " [\033[00;32mOK\033[00m]\n";
                    } catch (Exception $e) {
                        if ($output) echo " [\033[01;31mERROR\033[00m] $e\n";
                    }

                    // alle zeilen löschen die zuviel sind in vps_users
                    // nötig für lokale tests
                    if (Vps_Registry::get('config')->cleanupVpsUsersOnClearCache) {
                        if ($output) echo "vps_users cleanup......";

                        $dbRes = $db->query('SELECT COUNT(*) `cache_users_count` FROM `cache_users`')->fetchAll();
                        if ($dbRes[0]['cache_users_count'] >= 1) {
                            $dbRes = $db->query('SELECT COUNT(*) `sort_out_count` FROM `vps_users`
                                    WHERE NOT (SELECT cache_users.id
                                                FROM cache_users
                                                WHERE cache_users.id = vps_users.id
                                               )'
                            )->fetchAll();
                            $db->query('DELETE FROM `vps_users`
                                    WHERE NOT (SELECT cache_users.id
                                                FROM cache_users
                                                WHERE cache_users.id = vps_users.id
                                               )'
                            );
                            if ($output) echo " [\033[00;32mOK: ".$dbRes[0]['sort_out_count']." rows cleared\033[00m]\n";
                        } else {
                            if ($output) echo " [\033[01;33mskipping: cache_users is empty\033[00m]\n";
                        }
                    } else {
                        if ($output) echo "vps_users cleanup...... [\033[00;32mskipped by config\033[00m]\n";
                    }
                }
            }
        }
    }

    protected function _clearCache(array $types, $output, $server)
    {
        if (in_array('memcache', $types)) {
            if ($server) {
                if ($output) echo "ignored:     memcache\n";
            } else {
                $cache = Vps_Cache::factory('Core', 'Memcached', array(
                    'lifetime'=>null,
                    'automatic_cleaning_factor' => false,
                    'automatic_serialization'=>true));
                $cache->clean();
                if ($output) echo "cleared:     memcache\n";
            }
        }
        foreach ($this->getDbCacheTables() as $t) {
            if ($server) {
                if ($output) echo "ignored db:  $t\n";
            } else {
                if (in_array($t, $types) ||
                    (in_array('component', $types) && substr($t, 0, 15) == 'cache_component')
                ) {
                    Zend_Registry::get('db')->query("TRUNCATE TABLE $t");
                    if ($output) echo "cleared db:  $t\n";
                }
            }
        }
        foreach ($this->getCacheDirs() as $d) {
            if (in_array($d, $types)) {
                if (is_dir("application/cache/$d")) {
                    $this->_removeDirContents("application/cache/$d", $server);
                } else if (is_dir($d)) {
                    $this->_removeDirContents($d, $server);
                }
                if ($output) echo "cleared dir: $d cache\n";
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
                throw new Vps_ClientException("Clearing remote cache '$path' failed");
            }
        } else {
            $dir = new DirectoryIterator($path);
            foreach ($dir as $fileinfo) {
                if ($fileinfo->isFile() && $fileinfo->getFilename() != '.gitignore') {
                    unlink($fileinfo->getPathName());
                } elseif (!$fileinfo->isDot() && $fileinfo->isDir() && $fileinfo->getFilename() != '.svn') {
                    $this->_removeDirContents($fileinfo->getPathName(), $server);
                    @rmdir($fileinfo->getPathName());
                }
            }
        }
    }
}
