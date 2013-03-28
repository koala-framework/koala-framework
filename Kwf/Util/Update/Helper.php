<?php
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
        if (Kwf_Setup::hasDb()) {
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
        foreach ($u as $i) $i->_tags[] = 'web';
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
                $update->_tags = explode(' ', trim($m[1]));
            }
            $update->_tags[] = 'db';
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

    public static function executeUpdates($updates, $doneNames, $debug=false, $skipClearCache=false)
    {
        if (self::_executeUpdate($updates, 'checkSettings', $debug, $skipClearCache)) {

            Kwf_Util_Maintenance::writeMaintenanceBootstrap();

            self::_executeUpdate($updates, 'preUpdate', $debug, $skipClearCache);
            self::_executeUpdate($updates, 'update', $debug, $skipClearCache);
            self::_executeUpdate($updates, 'postUpdate', $debug, $skipClearCache);
            if (!$skipClearCache) {
                echo "\n";
                Kwf_Util_ClearCache::getInstance()->clearCache('all', true);
                echo "\n";
            }
            self::_executeUpdate($updates, 'postClearCache', $debug, $skipClearCache);
            foreach ($updates as $k=>$u) {
                if (!in_array($u->getRevision(), $doneNames)) {
                    $doneNames[] = $u->getUniqueName();
                }
            }
            Kwf_Registry::get('db')->query("UPDATE kwf_update SET data=?", serialize($doneNames));
            if (file_exists('update')) {
                //move away old update file to avoid confusion
                rename('update', 'update.backup');
            }
            echo "\n\033[32mupdate finished\033[0m\n";

            Kwf_Util_Maintenance::restoreMaintenanceBootstrap();

        } else {
            echo "\nupdate stopped\n";
        }
        return $doneNames;
    }

    private static function _executeUpdate($updates, $method, $debug = false, $skipClearCache = false)
    {
        $ret = true;
        foreach ($updates as $update) {
            if ($method != 'checkSettings') {
                if ($method != 'postClearCache' && !$skipClearCache) {
                    Kwf_Util_ClearCache::getInstance()->clearCache('all', false, false);
                }
                Kwf_Model_Abstract::clearInstances(); //wegen eventueller meta-data-caches die sich geändert haben
                Kwf_Component_Generator_Abstract::clearInstances();
                Kwf_Component_Data_Root::reset();
                if ($method == 'update') {
                    echo "\nexecuting $method ".$update->getUniqueName();
                    echo "... ";
                    flush();
                }
            }
            $e = false;
            if (in_array('db', $update->getTags())) {
                $databases = Kwf_Registry::get('config')->server->databases->toArray();
            } else {
                $databases = array('web');
            }
            foreach ($databases as $db) {
                if (!$db) continue;
                if ($method == 'update') {
                    echo $db.' ';
                    flush();
                }
                try {
                    if (Kwf_Registry::get('dao')) {
                        $db = Kwf_Registry::get('dao')->getDb($db);
                    } else {
                        $db = null;
                    }
                } catch (Exception $e) {
                    if ($method == 'update') {
                        echo "skipping, invalid db\n";
                        flush();
                    }
                    continue;
                }
                Kwf_Registry::set('db', $db);
                try {
                    $res = $update->$method();
                } catch (Exception $e) {
                    if ($debug) throw $e;
                    if ($method == 'checkSettings') {
                        echo get_class($update);
                    }
                    echo "\n\033[31mError:\033[0m\n";
                    echo $e->getMessage()."\n\n";
                    flush();
                    $ret = false;
                }
                if (!$e) {
                    if ($res) {
                        print_r($res);
                    }
                }

                //reset to default database
                $db = null;
                try {
                    if (Kwf_Registry::get('dao')) $db = Kwf_Registry::get('dao')->getDb();
                } catch (Exception $e) {}
                Kwf_Registry::set('db', $db);
            }
            if ($method == 'update' && $ret) {
                echo "\033[32 OK \033[0m\n";
            }
            flush();
        }
        return $ret;
    }
}
