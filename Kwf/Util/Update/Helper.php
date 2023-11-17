<?php
/**
 * Various helpers for update scripts
 */
class Kwf_Util_Update_Helper
{
    static private $_updateTagsCache;

    //for tests
    //this class with only static sucks (untestable)
    public static function clearUpdateTagsCache()
    {
        self::$_updateTagsCache = null;
    }

    /**
     * Returns all udpate tags used by this webs. They are usually set in config.ini
    **/
    public static function getUpdateTags()
    {
        if (!isset(self::$_updateTagsCache)) {
            self::$_updateTagsCache = Kwf_Registry::get('config')->server->updateTags->toArray();
            foreach (Kwf_Component_Abstract::getComponentClasses() as $class) {
                if (Kwc_Abstract::hasSetting($class, 'updateTags')) {
                    self::$_updateTagsCache = array_unique(array_merge(self::$_updateTagsCache, Kwc_Abstract::getSetting($class, 'updateTags')));
                }
            }
        }
        return self::$_updateTagsCache;
    }

    public static function getUpdates()
    {
        $ret = self::getKwcUpdates();

        $u = self::getUpdatesForDir('Kwf_Update');
        $ret = array_merge($ret, $u);

        if (defined('VKWF_PATH')) { //HACK
            $u = self::getUpdatesForDir('Vkwf_Update');
            $ret = array_merge($ret, $u);
        }

        $u = self::getUpdatesForDir('Update');
        $ret = array_merge($ret, $u);

        foreach (Kwf_Model_Abstract::findAllInstances() as $m) {
            $ret = array_merge($ret, $m->getUpdates());
        }

        if ($kernel = Kwf_Util_Symfony::getKernel()) {
            foreach ($kernel->getContainer()->get('kwf.updates_provider_locator')->getUpdateProviders() as $provider) {
                $ret = array_merge($ret, $provider->getUpdates());
            }
        }

        $ret = self::_sortUpdates($ret);
        return $ret;
    }

    public static function getKwcUpdates()
    {
        $ret = array();
        $processed = array();
        foreach (Kwf_Component_Abstract::getComponentClasses() as $cmpClass) {
            foreach (Kwc_Abstract::getSetting($cmpClass, 'parentClasses') as $class) {
                $class = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
                if (!isset($processed[$class])) {
                    $processed[$class] = true;
                    $curClass = $class;
                    if (substr($curClass, -10) == '_Component') {
                        $curClass = substr($curClass, 0, -10);
                    }
                    $curClass .= '_Update';
                    $ret = array_merge($ret, self::getUpdatesForDir($curClass));
                }
            }
        }
        return $ret;
    }

    public static function getUpdatesForDir($classPrefix)
    {
        static $namespaces;
        if (!isset($namespaces)) {
            $composerNamespaces = include VENDOR_PATH.'/composer/autoload_namespaces.php';
            $psr4Namespaces = include VENDOR_PATH.'/composer/autoload_psr4.php';
            $namespaces = Kwf_Loader::_prepareNamespaces($composerNamespaces, $psr4Namespaces);
        }

        $pos = strpos($classPrefix, '\\') !== false ? strpos($classPrefix, '\\') : strpos($classPrefix, '_');
        $ns1 = substr($classPrefix, 0, $pos+1);

        $pos = strpos($classPrefix, '\\') !== false ? strpos($classPrefix, '\\', $pos+1) : strpos($classPrefix, '_', $pos+1);
        if ($pos !== false) {
            $ns2 = substr($classPrefix, 0, $pos+1);
        } else {
            $ns2 = $classPrefix;
        }
        if (isset($namespaces[$ns2])) {
            $dirs = $namespaces[$ns2];
            $matchingNamespace = $ns2;
        } else if (isset($namespaces[$ns1])) {
            $dirs = $namespaces[$ns1];
            $matchingNamespace = $ns1;
        } else {
            $dirs = array();
            $matchingNamespace = null;
        }


        static $includePaths;
        if (!isset($includePaths)) {
            $includePaths = include VENDOR_PATH.'/composer/include_paths.php';
            $includePaths = array_merge($includePaths, Kwf_Config::getValueArray('includepath'));
        }
        foreach ($includePaths as $i) {
            if (file_exists($i.'/'.str_replace('_', '/', $classPrefix))) {
                $dirs[] = $i;
            }
        }


        if ($matchingNamespace) {
            $classPrefix = substr($classPrefix, strlen($matchingNamespace));
        }

        $ret = array();
        foreach ($dirs as $dir) {
            $path =  $dir . '/' . str_replace(array('_', '\\'), '/', $classPrefix);
            if (is_dir($path)) {
                foreach (new DirectoryIterator($path) as $i) {
                    if (!$i->isFile()) continue;
                    $f = $i->__toString();
                    $fileType = substr($f, -4);
                    if ($fileType != '.php' && $fileType != '.sql') continue;
                    $f = substr($f, 0, -4);
                    if (is_numeric($f)) {
                        throw new Kwf_Exception("Invalid update script name: ".$i->getPathname()." Please use the new syntax.");
                    }
                    $date = substr($f, 0, 8);
                    if ($date !== date("Ymd", strtotime($date))) {
                        throw new Kwf_Exception("Invalid update script name: ".$i->getPathname());
                    }
                    $className = $matchingNamespace.$classPrefix.'_'.$f;
                    if ($className == 'Kwf_Update_Sql') continue;
                    $update = self::createUpdate($className, $i->getPathname());

                    if (!$update) continue;
                    $tags = $update->getTags();
                    if ($tags) {
                        if (array_intersect($tags, self::getUpdateTags()) != $tags) {
                            continue;
                        }
                    }
                    $ret[] = $update;
                }
            }
        }
        $ret = self::_sortUpdates($ret);
        return $ret;
    }

    public static function createUpdate($class, $filename = null)
    {
        $file = str_replace('_', '/', $class);
        $isSql = false;
        if (!$filename) {
            if (is_file($file . '.sql')) {
                $filename = $file . '.sql';
                $isSql = true;
            } else if (is_file(KWF_PATH . '/' . $file . '.sql')) {
                $filename = KWF_PATH . '/' . $file . '.sql';
                $isSql = true;
            }
        } else {
            $isSql = substr($filename, -4) == '.sql';
        }
        $update = null;
        if ($isSql) {
            $update = new Kwf_Update_Sql($class);
            $update->sql = file_get_contents($filename);
            if (preg_match("#\\#\\s*tags:(.*)#", $update->sql, $m)) {
                $update->setTags(explode(' ', trim($m[1])));
            }
        } else {
            $update = new $class($class);
            if (!$update instanceof Kwf_Update) {
                throw new Kwf_Exception("Invalid update class: '$class'");
            }
        }
        return $update;
    }

    private static function _cmpSortUpdates($a, $b)
    {
        $revA = $a->getLegacyRevision();
        $revB = $b->getLegacyRevision();
        $nameA = substr($a->getUniqueName(), strrpos($a->getUniqueName(), '_')+1);
        $nameB = substr($b->getUniqueName(), strrpos($b->getUniqueName(), '_')+1);
        if ($revA && $revB) {
            if ($revA == $revB) return 0;
            return ($revA < $revB) ? -1 : 1;
        } else if ($revA) {
            return -1;
        } else if ($revB) {
            return 1;
        } else {
            return ($nameA < $nameB) ? -1 : 1;
        }
    }

    private static function _sortUpdates($updates)
    {
        usort($updates, array('Kwf_Util_Update_Helper', '_cmpSortUpdates'));
        return $updates;
    }

    public static function getExecutedUpdatesNames()
    {
        $db = Kwf_Registry::get('db');
        $tables = $db->listTables();
        $doneNames = false;
        if (in_array('kwf_updates', $tables)) {
            $q = $db->query("SELECT name FROM kwf_updates");
            $doneNames = array();
            foreach($q->fetchAll() as $row) {
                $doneNames[] = $row['name'];
            }
        } else if (in_array('kwf_update', $tables)) {
            //update pre 4.2
            $q = $db->query("SELECT data FROM kwf_update");
            $doneNames = $q->fetchColumn();
        }

        if (!$doneNames) {
            //fallback for older versions, update used to be a file
            if (file_exists('update')) {
                $doneNames = file_get_contents('update');
            } else {
                throw new Kwf_Exception("Can't read kwf_update");
            }
        }

        if (is_string($doneNames) && is_numeric(trim($doneNames))) {
            //UPDATE applicaton/update format
            $r = trim($doneNames);
            $doneNames = array();
            foreach (Kwf_Util_Update_Helper::getUpdates() as $u) {
                if ($u->getLegacyRevision() && $u->getLegacyRevision() <= $r) {
                    $doneNames[] = $u->getUniqueName();
                }
            }
        } else if (is_string($doneNames)) {
            $doneNames = unserialize($doneNames);
            if (isset($doneNames['start'])) {
                //UPDATE applicaton/update format
                if (!isset($doneNames['done'])) {
                    $doneNames['done'] = array();
                    foreach (Kwf_Util_Update_Helper::getUpdates() as $u) {
                        if ($u->getLegacyRevision() && $u->getLegacyRevision() <= $doneNames['start']) {
                            $doneNames['done'][] = $u->getLegacyRevision();
                        }
                    }
                }
                $doneNames = $doneNames['done'];
            }
            $doneNamesCpy = $doneNames;
            $doneNames = array();
            foreach ($doneNamesCpy as $i) {
                if (is_numeric($i)) {
                    //UPDATE applicaton/update format
                    static $allUpdates;
                    if (!isset($allUpdates)) {
                        $allUpdates = array();
                        foreach (Kwf_Util_Update_Helper::getUpdates() as $u) {
                            if ($u->getLegacyRevision()) {
                                if (!isset($allUpdates[$u->getLegacyRevision()])) $allUpdates[$u->getLegacyRevision()] = array();
                                $allUpdates[$u->getLegacyRevision()][] = $u;
                            }
                        }
                    }
                    if (isset($allUpdates[$i])) {
                        foreach ($allUpdates[$i] as $u) {
                            $doneNames[] = $u->getUniqueName();
                        }
                    }
                } else {
                    $doneNames[] = $i;
                }
            }
        }

        //convert old updates from pre 3.0 times (where kwf was called vps)
        foreach ($doneNames as &$i) {
            if (substr($i, 0, 4) == 'Vpc_' || substr($i, 0, 4) == 'Vps_') {
                $updateWithoutWebname = substr($i, strpos($i, '_', 4)+1);
                if (class_exists($updateWithoutWebname)) {
                    $i = $updateWithoutWebname;
                    continue;
                }
                $updateSqlFile = str_replace('_', '/', $updateWithoutWebname).'.sql';
                foreach (explode(PATH_SEPARATOR, get_include_path()) as $ip) {
                    if (file_exists($ip.'/'.$updateSqlFile)) {
                        $i = $updateWithoutWebname;
                        continue;
                    }
                }

                $i = str_replace('Vps_Update_', 'Vkwf_Update_', $i);

                $i = str_replace('Vps_', 'Kwf_', $i);
                $i = str_replace('Vpc_', 'Kwc_', $i);
            }
        }

        if (!$doneNames) {
            //it's ok to have no updates throw new Kwf_ClientException("Invalid update revision");
        }
        return $doneNames;
    }

    public static function countPendingUpdates()
    {
        $updates = self::getUpdates(0, 9999999);
        $doneNames = self::getExecutedUpdatesNames();
        foreach ($updates as $k=>$u) {
            if ($u->getRevision() && in_array($u->getUniqueName(), $doneNames)) {
                unset($updates[$k]);
            }
        }
        return count($updates);
    }
}
