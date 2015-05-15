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

    public static function getUpdates($from, $to)
    {
        $ret = self::getKwcUpdates($from, $to);

        $u = self::getUpdatesForDir('Kwf_Update', $from, $to);
        $ret = array_merge($ret, $u);

        if (defined('VKWF_PATH')) { //HACK
            $u = self::getUpdatesForDir('Vkwf_Update', $from, $to);
            $ret = array_merge($ret, $u);
        }

        $u = self::getUpdatesForDir('Update', $from, $to);
        $ret = array_merge($ret, $u);

        $ret = self::_sortByRevision($ret);
        return $ret;
    }

    public static function getKwcUpdates($from, $to)
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
                    $ret = array_merge($ret, self::getUpdatesForDir($curClass, $from, $to));
                }
            }
        }
        return $ret;
    }

    public static function getUpdatesForDir($classPrefix, $from, $to)
    {
        static $namespaces;
        if (!isset($namespaces)) {
            $namespaces = include VENDOR_PATH.'/composer/autoload_namespaces.php';
        }
        $pos = strpos($classPrefix, '_');
        $ns1 = substr($classPrefix, 0, $pos+1);

        $pos = strpos($classPrefix, '_', $pos+1);
        if ($pos !== false) {
            $ns2 = substr($classPrefix, 0, $pos+1);
        } else {
            $ns2 = $classPrefix;
        }

        if (isset($namespaces[$ns2])) {
            $dirs = $namespaces[$ns2];
        } else if (isset($namespaces[$ns1])) {
            $dirs = $namespaces[$ns1];
        } else {
            $dirs = array();
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

        $ret = array();
        foreach ($dirs as $dir) {
            $path =  $dir . '/' . str_replace('_', '/', $classPrefix);
            if (is_dir($path)) {
                foreach (new DirectoryIterator($path) as $i) {
                    if (!$i->isFile()) continue;
                    $f = $i->__toString();
                    $fileType = substr($f, -4);
                    if ($fileType != '.php' && $fileType != '.sql') continue;
                    $f = substr($f, 0, -4);
                    if (!is_numeric($f)) continue;
                    $nr = (int)$f;
                    if ($nr >= $from && $nr < $to) {
                        $n = $classPrefix.'_'.$nr;
                        $update = self::createUpdate($n, $i->getPathname());

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
        }
        $ret = self::_sortByRevision($ret);
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
        $nr = (int)substr(strrchr($class, '_'), 1);
        $update = null;
        if ($isSql) {
            $update = new Kwf_Update_Sql($nr, $class);
            $update->sql = file_get_contents($filename);
            if (preg_match("#\\#\\s*tags:(.*)#", $update->sql, $m)) {
                $update->setTags(explode(' ', trim($m[1])));
            }
        } else {
            $update = new $class($nr, $class);
            if (!$update instanceof Kwf_Update) {
                throw new Kwf_Exception("Invalid update class: '$class'");
            }
        }
        return $update;
    }

    private static function _sortByRevision($updates)
    {
        $revisions = array();
        foreach ($updates as $k=>$u) {
            $revisions[$k] = $u->getRevision();
            if (is_null($revisions[$k])) {
                $revisions[$k] = 99999999;
            }
        }
        asort($revisions, SORT_NUMERIC);
        $ret = array();
        foreach (array_keys($revisions) as $k) {
            $ret[] = $updates[$k];
        }
        return $ret;
    }

    public static function getExecutedUpdatesNames()
    {
        $db = Kwf_Registry::get('db');
        try {
            $q = $db->query("SELECT data FROM kwf_update");
        } catch (Exception $e) {
        }
        $doneNames = false;
        if (isset($q)) {
            $doneNames = $q->fetchColumn();
        }
        if (!$doneNames) {
            //fallback for older versions, update used to be a file
            if (!file_exists('update')) {
                $doneNames = array();
                foreach (Kwf_Util_Update_Helper::getUpdates(0, 9999999) as $u) {
                    $doneNames[] = $u->getUniqueName();
                }
                $db->query("UPDATE kwf_update SET data=?", serialize($doneNames));
                echo "No update revision found, assuming up-to-date\n";
                exit;
            }
            $doneNames = file_get_contents('update');
        }

        if (is_numeric(trim($doneNames))) {
            //UPDATE applicaton/update format
            $r = trim($doneNames);
            $doneNames = array();
            foreach (Kwf_Util_Update_Helper::getUpdates(0, $r) as $u) {
                $doneNames[] = $u->getUniqueName();
            }
        } else {
            $doneNames = unserialize($doneNames);
            if (isset($doneNames['start'])) {
                //UPDATE applicaton/update format
                if (!isset($doneNames['done'])) {
                    $doneNames['done'] = array();
                    foreach (Kwf_Util_Update_Helper::getUpdates(0, $doneNames['start']) as $u) {
                        $doneNames['done'][] = $u->getRevision();
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
                        foreach (Kwf_Util_Update_Helper::getUpdates(0, 9999999) as $u) {
                            if (!isset($allUpdates[$u->getRevision()])) $allUpdates[$u->getRevision()] = array();
                            $allUpdates[$u->getRevision()][] = $u;
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
