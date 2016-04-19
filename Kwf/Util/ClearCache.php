<?php
class Kwf_Util_ClearCache
{
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

    protected function _getCacheDirs()
    {
        $ret = array();
        foreach (new DirectoryIterator('cache') as $d) {
            if ($d->isDir() && substr($d->getFilename(), 0, 1) != '.') {
                if ($d->getFilename() == 'searchindex') continue;
                if ($d->getFilename() == 'fulltext') continue;
                if ($d->getFilename() == 'scss') continue; //never clear scss, too expensive to regenerate
                if ($d->getFilename() == 'uglifyjs') continue; //never clear uglifyjs, too expensive to regenerate
                if ($d->getFilename() == 'media') continue; //never clear media, too expensive to regenerate
                if ($d->getFilename() == 'mediameta') continue; //never clear mediameta, too expensive to regenerate
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

    //we use it internal for copy-data-to-git
    public function getDbCacheTables()
    {
        return $this->_getDbCacheTables();
    }

    protected function _getDbCacheTables()
    {
        $ret = array();
        try {
            if (!Zend_Registry::get('db')) return $ret;
        } catch (Exception $e) {
            return $ret;
        }
        try {
            $tables = Zend_Registry::get('db')->fetchCol('SHOW TABLES');
        } catch (Exception $e) {
            return $ret;
        }
        foreach ($tables as $table) {
            if (substr($table, 0, 6) == 'cache_') {
                $ret[] = $table;
            }
        }
        return $ret;
    }

    public function getTypes()
    {
        $types = array();
        $simpleCacheBackend = Kwf_Cache_Simple::getBackend();
        if ($simpleCacheBackend == 'memcache' || $simpleCacheBackend == 'elastiCache') {
            $types[] = new Kwf_Util_ClearCache_Types_SimpleCache();
        }
        if (Kwf_Util_Apc::isAvailable()) {
            $types[] = new Kwf_Util_ClearCache_Types_ApcUser();
            $types[] = new Kwf_Util_ClearCache_Types_ApcOptcode();
        }
        foreach ($this->_getCacheDirs() as $d) {
            if ($d != 'config'    //handled in Types_Config
                && $d != 'assets' //handled in Types_Assets
                && $d != 'trl' //handled in Types_Trl
                && $d != 'view' //removed indirectly by Types_TableComponentView
            ) {
                $types[] = new Kwf_Util_ClearCache_Types_Dir($d);
            }
        }
        foreach ($this->_getDbCacheTables() as $t) {
            if ($t == 'cache_component') {
                $types[] = new Kwf_Util_ClearCache_Types_TableComponentView();
            } else if ($t == 'cache_component_includes') {
                //never completely clear that table as it would break clearing fullPage cache
            } else if ($t == 'cache_users') {
                //skip, needed during update
            } else {
                $types[] = new Kwf_Util_ClearCache_Types_Table($t);
            }
        }
        if (Kwf_Config::getValue('assetsCacheUrl')) {
            $types[] = new Kwf_Util_ClearCache_Types_AssetsServer();
        }

        $types[] = new Kwf_Util_ClearCache_Types_Config();
        $types[] = new Kwf_Util_ClearCache_Types_Setup();
        $types[] = new Kwf_Util_ClearCache_Types_Assets();

        if (!Kwf_Config::getValue('clearCacheSkipProcessControl') && VENDOR_PATH != '../vendor') {
            $types[] = new Kwf_Util_ClearCache_Types_ProcessControl();
        }

        return $types;
    }

    public function getTypeNames()
    {
        $ret = array();
        foreach ($this->getTypes() as $t) {
            $ret[] = $t->getTypeName();
        }
        return $ret;
    }

    /**
     * @param array possible options: types(=all), output(=false), refresh(=true), excludeTypes, skipMaintenanceBootstrap, skipOtherServers
     */
    public final function clearCache(array $options)
    {
        $typeNames = $options['types'];
        $output = isset($options['output']) ? $options['output'] : false;
        $refresh = isset($options['refresh']) ? $options['refresh'] : false;
        $excludeTypes = isset($options['excludeTypes']) ? $options['excludeTypes'] : array();

        Kwf_Events_ModelObserver::getInstance()->disable();

        Kwf_Util_MemoryLimit::set(512);
        if (!isset($options['skipMaintenanceBootstrap']) || !$options['skipMaintenanceBootstrap']) {
            Kwf_Util_Maintenance::writeMaintenanceBootstrap($output);
        }

        if ($typeNames == 'all') {
            $types = $this->getTypes();
        } else {
            if (!is_array($typeNames)) {
                $typeNames = explode(',', $typeNames);
            }
            $types = array();
            foreach ($this->getTypes() as $t) {
                if (in_array($t->getTypeName(), $typeNames)) {
                    $types[] = $t;
                }
            }
        }

        if (is_string($excludeTypes)) $excludeTypes = explode(',', $excludeTypes);
        foreach ($types as $k=>$i) {
            if (in_array($i->getTypeName(), $excludeTypes)) unset($types[$k]);
        }

        $maxTypeNameLength = 0;
        $countSteps = 0;
        foreach ($types as $type) {
            $type->setVerbosity($output ? Kwf_Util_ClearCache_Types_Abstract::VERBOSE : Kwf_Util_ClearCache_Types_Abstract::SILENT);
            $maxTypeNameLength = max($maxTypeNameLength, strlen($type->getTypeName()));
            if ($type->doesClear()) $countSteps++;
            if ($type->doesRefresh()) $countSteps++;
        }

        $progress = null;
        if (isset($options['progressAdapter'])) {
            $progress = new Zend_ProgressBar($options['progressAdapter'], 0, $countSteps);
        }

        $currentStep = 0;
        foreach ($types as $type) {
            if ($type->doesClear()) {
                $currentStep++;
                if ($progress) $progress->next(1, "clearing ".$type->getTypeName());
                if ($output) {
                    echo "[".str_repeat(' ', 2-strlen($currentStep))."$currentStep/$countSteps] ";
                    echo "clearing ".$type->getTypeName()."...".str_repeat('.', $maxTypeNameLength - strlen($type->getTypeName()))." ";
                }
                $t = microtime(true);
                $type->clearCache($options);
                if ($output) {
                    if ($type->getSuccess()) {
                        echo "\033[00;32mOK\033[00m";
                    } else {
                        echo " [\033[01;31mERROR\033[00m]";
                    }
                    echo " (".round((microtime(true)-$t)*1000)."ms)";
                    echo "\n";
                }
            }
        }
        if ($refresh) {
            foreach ($types as $type) {
                if ($type->doesRefresh()) {
                    $currentStep++;
                    if ($progress) $progress->next(1, "refreshing ".$type->getTypeName());
                    if ($output) {
                        echo "[$currentStep/$countSteps] refreshing ".$type->getTypeName().".".str_repeat('.', $maxTypeNameLength - strlen($type->getTypeName()))." ";
                    }
                    $t = microtime(true);
                    $mem = memory_get_usage();
                    $type->refreshCache($options);
                    if ($output) {
                        if ($type->getSuccess()) {
                            echo "\033[00;32mOK\033[00m";
                        } else {
                            echo " [\033[01;31mERROR\033[00m]";
                        }
                        echo " (".round((microtime(true)-$t)*1000)."ms";
                        if (memory_get_usage()-$mem > 1024*1024) echo ", ".round((memory_get_usage()-$mem)/(1024*1024), 2)."MB";
                        echo ")\n";
                    }
                }
            }
        }

        if (!isset($options['skipMaintenanceBootstrap']) || !$options['skipMaintenanceBootstrap']) {
            Kwf_Util_Maintenance::restoreMaintenanceBootstrap($output);
        }

        Kwf_Events_ModelObserver::getInstance()->enable();
        return $types;
    }
}
