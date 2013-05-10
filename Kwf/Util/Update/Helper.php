<?php
/**
 * Various helpers for update scripts
 */
class Kwf_Util_Update_Helper
{
    /**
     * Returns all udpate tags used by this webs. They are usually set in config.ini
    **/
    public static function getUpdateTags()
    {
        $ret = Kwf_Registry::get('config')->server->updateTags->toArray();
        foreach (Kwf_Component_Abstract::getComponentClasses() as $class) {
            if (Kwc_Abstract::hasSetting($class, 'updateTags')) {
                $ret = array_unique(array_merge($ret, Kwc_Abstract::getSetting($class, 'updateTags')));
            }
        }
        if (Kwf_Registry::get('db')) {
            $ret[] = 'db';
        }
        return $ret;
    }

    public static function getUpdates($from, $to)
    {
        $ret = self::getKwcUpdates($from, $to);

        $u = self::getUpdatesForDir(KWF_PATH.'/Kwf', $from, $to);
        $ret = array_merge($ret, $u);

        if (defined('VKWF_PATH')) { //HACK
            $u = self::getUpdatesForDir(VKWF_PATH.'/Vkwf', $from, $to);
            $ret = array_merge($ret, $u);
        }

        $u = self::getUpdatesForDir(getcwd() . '/app', $from, $to);
        foreach ($u as $i) $i->appendTag('web');
        $ret = array_merge($ret, $u);

        $ret = self::_sortByRevision($ret);
        return $ret;
    }

    public static function getKwcUpdates($from, $to)
    {
        $ret = array();
        $processed = array();
        foreach (Kwf_Component_Abstract::getComponentClasses() as $class) {
            while ($class != '') {
                $class = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
                if (!in_array($class, $processed)) {
                    $processed[] = $class;
                    $curClass = $class;
                    if (substr($curClass, -10) == '_Component') {
                        $curClass = substr($curClass, 0, -10);
                    }
                    $file = str_replace('_', DIRECTORY_SEPARATOR, $curClass);
                    $ret = array_merge($ret, self::getUpdatesForDir($file, $from, $to));
                }
                $class = get_parent_class($class);
            }
        }
        return $ret;
    }

    public static function getUpdatesForDir($file, $from, $to)
    {
        $ret = array();
        $paths = array();
        $dirs = explode(PATH_SEPARATOR, get_include_path());
        foreach ($dirs as $k=>$i) {
            if ($i=='.') {
                $dirs[$k] = getcwd();
                continue;
            }
            if (!preg_match('#^(/|\w\:\\\\)#i', $i)) {
                 //relative path, make it absolute
                 $dirs[$k] = getcwd().'/'.$i;
            }
        }
        $dirs = array_unique($dirs);
        $dirs = array_reverse($dirs);
        foreach ($dirs as $dir) {
            if (substr($file, 0, strlen($dir)) == $dir) {
                $file = substr($file, strlen($dir)+1);
            }
            $path = $dir . '/' . $file;
            if (substr($path, 0, 1)=='.') {
                $path = getcwd().substr($path, 1);
            }
            if (in_array($path, $paths)) continue;
            $paths[] = $path;
            if (is_dir($path)) {
                $path =  $path . '/Update';
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
                            $n = '';
                            if ($file) {
                                $n = str_replace(DIRECTORY_SEPARATOR, '_', $file) . '_';
                            }
                            if (preg_match('#^[a-z]+-lib_#', $n)) continue; //kwf-lib, vkwf-lib
                            if (substr($n, 0, 8) == 'library_') continue;
                            $n .= 'Update_'.$nr;
                            $update = self::createUpdate($n, $i->getPathname());
                            if (!$update) continue;
                            if ($update->getTags() && !in_array('web', $update->getTags())) {
                                $tags = $update->getTags();
                                foreach ($tags as $tag) {
                                    if (!in_array($tag, self::getUpdateTags())) {
                                        continue 2;
                                    }
                                }
                            }
                            $ret[] = $update;
                        }
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
            $update->appendTag('db');
        } else {
            if (is_instance_of($class, 'Kwf_Update')) {
                $update = new $class($nr, $class);
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
}
