<?php
class Kwf_Util_ClearCache
{
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

    protected function _getCacheDirs()
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
        $types = array();
        $simpleCacheBackend = Kwf_Cache_Simple::getBackend();
        if ($simpleCacheBackend == 'memcache' || $simpleCacheBackend == 'elastiCache') {
            $types[] = new Kwf_Util_ClearCache_Types_SimpleCache();
        }
        $hasApc = extension_loaded('apc');
        if (!$hasApc) {
            //apc might be enabled in webserver only, not in cli
            $hasApc = Kwf_Util_Apc::callUtil('is-loaded', array(), array('returnBody'=>true)) == 1;
        }
        if ($hasApc) {
            $types[] = new Kwf_Util_ClearCache_Types_ApcUser();
            $types[] = new Kwf_Util_ClearCache_Types_ApcOptcode();
        }
        foreach ($this->_getCacheDirs() as $d) {
            if ($d != 'config'    //handled in Types_Config
                && $d != 'assets' //handled in Types_Assets
                && $d != 'trl' //handled in Types_Trl
            ) {
                $types[] = new Kwf_Util_ClearCache_Types_Dir($d);
            }
        }
        foreach ($this->_getDbCacheTables() as $t) {
            if ($t == 'cache_component') {
                $types[] = new Kwf_Util_ClearCache_Types_TableComponentView();
            } else if ($t == 'cache_component_includes') {
                //never completely clear that table as it would break clearing fullPage cache
            } else if ($t == 'cache_users') { //handled in Types_Users
                 $types[] = new Kwf_Util_ClearCache_Types_Users();
            } else {
                $types[] = new Kwf_Util_ClearCache_Types_Table($t);
            }
        }
        if (Kwf_Config::getValue('assetsCacheUrl')) {
            $types[] = new Kwf_Util_ClearCache_Types_AssetsServer();
        }
 
        $types[] = new Kwf_Util_ClearCache_Types_Config();
        $types[] = new Kwf_Util_ClearCache_Types_Setup();
        if (Kwf_Component_Data_Root::getComponentClass()) {
            $types[] = new Kwf_Util_ClearCache_Types_ComponentSettings();
        }
        $types[] = new Kwf_Util_ClearCache_Types_Trl();
        $types[] = new Kwf_Util_ClearCache_Types_Assets();
        if (Kwf_Component_Data_Root::getComponentClass()) {
            $types[] = new Kwf_Util_ClearCache_Types_Events();
        }

        try {
            $db = Kwf_Registry::get('db');
        } catch (Exception $e) {
            $db = false;
        }
        if ($db) {
            $tables = Kwf_Registry::get('db')->fetchCol('SHOW TABLES');
            if (in_array('kwf_users', $tables) && in_array('cache_users', $tables)) {
                if (Kwf_Registry::get('config')->cleanupKwfUsersOnClearCache) {
                    $types[] = new Kwf_Util_ClearCache_Types_UsersCleanup();
                }
            }
        }
        if (Kwf_Config::getValueArray('processControl')) {
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
     * @param string types of caches that should be cleared
     * @param bool if output should shown (for cli)
     * @param bool if caches should be refreshed (warmed up)
     * @param array possible options: skipMaintenanceBootstrap, skipOtherServers
     */
    public final function clearCache($typeNames = 'all', $output = false, $refresh = true, $options = array())
    {
        Kwf_Component_ModelObserver::getInstance()->disable();

        ini_set('memory_limit', '512M');
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
/*
        TODO re-enable this somehow
          * required at all?
          * own type? or should the different types each also clear the other servers (apc does that already)

        $skipOtherServers = isset($options['skipOtherServers']) ? $options['skipOtherServers'] : false;
        if (Kwf_Config::getValue('server.aws') && !$skipOtherServers) {
            $otherHostsTypes = $this->_getCacheDirs();
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
                    $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php clear-cache --type=".implode(',', $otherHostsTypes).' --skip-other-servers';
                    $cmd = "ssh -o 'StrictHostKeyChecking no' $domain ".escapeshellarg('cd '.Kwf_Config::getValue('server.dir').'; '.$cmd);
                    passthru($cmd);
                    if ($output) {
                        echo "\n";
                    }
                }
            }
        }
*/
        if (!isset($options['skipMaintenanceBootstrap']) || !$options['skipMaintenanceBootstrap']) {
            Kwf_Util_Maintenance::restoreMaintenanceBootstrap($output);
        }

        Kwf_Component_ModelObserver::getInstance()->enable();
        return $types;
    }
}
