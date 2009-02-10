<?php
class Vps_Controller_Action_Cli_ClearCacheController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "clears all caches";
    }

    public function indexAction()
    {
        self::clearCache($this->_getParam('type'), true);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private static function _getCacheDirs()
    {
        $ret = array();
        foreach (new DirectoryIterator('application/cache') as $d) {
            if ($d->isDir() && substr($d->getFilename(), 0, 1) != '.') {
                $ret[] = $d->getFilename();
            }
        }
        return $ret;
    }

    public static function getDbCacheTables()
    {
        $ret = array();
        $tables = Zend_Registry::get('db')->fetchCol('SHOW TABLES');
        foreach ($tables as $table) {
            if (substr($table, 0, 6) == 'cache_') {
                $ret[] = $table;
            }
        }
        return $ret;
    }

    public static function getHelpOptions()
    {
        $types = array('all', 'memcache');
        $types = array_merge($types, self::_getCacheDirs());
        $types = array_merge($types, self::getDbCacheTables());
        return array(
            array(
                'param'=> 'type',
                'value'=> $types,
                'valueOptional' => true,
                'help' => 'what to clear'
            )
        );
    }

    public static function clearCache($types = 'all', $output = false)
    {
        if ($types == 'all') {
            $types = array('memcache');
            $types = array_merge($types, self::_getCacheDirs());
            $types = array_merge($types, self::getDbCacheTables());
        } else {
            if (!is_array($types)) {
                $types = explode(',', $types);
            }
        }
        if (in_array('memcache', $types)) {
            $cache = Vps_Cache::factory('Core', 'Memcached', array(
                'lifetime'=>null,
                'automatic_cleaning_factor' => false,
                'automatic_serialization'=>true));
            $cache->clean();
            if ($output) echo "cleared:     memcache...\n";
        }
        foreach (self::getDbCacheTables() as $t) {
            Zend_Registry::get('db')->query("TRUNCATE TABLE $t");
            if ($output) echo "cleared db:  $t...\n";
        }
        foreach (self::_getCacheDirs() as $d) {
            if (in_array($d, $types)) {
                self::_removeDirContents("application/cache/$d");
                if ($output) echo "cleared dir: $d cache...\n";
            }
        }
    }

    private static function _removeDirContents($path)
    {
        $dir = new DirectoryIterator($path);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isFile()) {
                unlink($fileinfo->getPathName());
            } elseif (!$fileinfo->isDot() && $fileinfo->isDir() && $fileinfo->getFilename() != '.svn') {
                self::_removeDirContents($fileinfo->getPathName());
                rmdir($fileinfo->getPathName());
            }
        }
    }
}
