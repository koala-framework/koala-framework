<?php
class Vps_Util_ClearCache
{
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

    protected function _getCacheDirs()
    {
        $ret = array();
        foreach (new DirectoryIterator('application/cache') as $d) {
            if ($d->isDir() && substr($d->getFilename(), 0, 1) != '.') {
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
        if (!Zend_Registry::get('db')) return $ret;
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
        $types = array_merge($types, $this->_getCacheDirs());
        $types = array_merge($types, $this->getDbCacheTables());
        return $types;
    }

    public final function clearCache($types = 'all', $output = false)
    {
        if ($types == 'all') {
            $types = $this->getTypes();
        } else {
            if (!is_array($types)) {
                $types = explode(',', $types);
            }
        }
        $this->_clearCache($types, $output);
    }
    protected function _clearCache(array $types, $output)
    {
        if (in_array('memcache', $types)) {
            $cache = Vps_Cache::factory('Core', 'Memcached', array(
                'lifetime'=>null,
                'automatic_cleaning_factor' => false,
                'automatic_serialization'=>true));
            $cache->clean();
            if ($output) echo "cleared:     memcache...\n";
        }
        foreach ($this->getDbCacheTables() as $t) {
            if (in_array($t, $types)) {
                Zend_Registry::get('db')->query("TRUNCATE TABLE $t");
                if ($output) echo "cleared db:  $t...\n";
            }
        }
        foreach ($this->_getCacheDirs() as $d) {
            if (in_array($d, $types)) {
                if (is_dir("application/cache/$d")) {
                    $this->_removeDirContents("application/cache/$d");
                } else if (is_dir($d)) {
                    $this->_removeDirContents($d);
                }
                if ($output) echo "cleared dir: $d cache...\n";
            }
        }
    }

    private function _removeDirContents($path)
    {
        $dir = new DirectoryIterator($path);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isFile()) {
                unlink($fileinfo->getPathName());
            } elseif (!$fileinfo->isDot() && $fileinfo->isDir() && $fileinfo->getFilename() != '.svn') {
                $this->_removeDirContents($fileinfo->getPathName());
                rmdir($fileinfo->getPathName());
            }
        }
    }
}
