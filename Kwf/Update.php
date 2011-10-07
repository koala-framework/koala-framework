<?php
abstract class Vps_Update
{
    protected $_tags = array();

    protected $_actions = array();
    protected $_revision;
    protected $_uniqueName;

    public function __construct($revision, $uniqueName)
    {
        $this->_revision = (int)$revision;
        $this->_uniqueName = $uniqueName;
        $this->_init();
    }

    public function getTags()
    {
        return $this->_tags;
    }

    public function getRevision()
    {
        return $this->_revision;
    }

    public function getUniqueName()
    {
        return $this->_uniqueName;
    }

    protected function _init()
    {
    }

    public function preUpdate()
    {
        $ret = array();
        foreach ($this->_actions as $a) {
            $res = $a->preUpdate();
            if ($res) {
                $ret[] = $res;
            }
        }
        return $ret;
    }

    public function postUpdate()
    {
        $ret = array();
        foreach ($this->_actions as $a) {
            $res = $a->postUpdate();
            if ($res) {
                $ret[] = $res;
            }
        }
        return $ret;
    }

    public function postClearCache()
    {
        $ret = array();
        foreach ($this->_actions as $a) {
            $res = $a->postClearCache();
            if ($res) {
                $ret[] = $res;
            }
        }
        return $ret;
    }
    public function checkSettings()
    {
        $ret = array();
        foreach ($this->_actions as $a) {
            $res = $a->checkSettings();
            if ($res) {
                $ret[] = $res;
            }
        }
        return $ret;
    }

    public function update()
    {
        $ret = array();
        foreach ($this->_actions as $a) {
            $res = $a->update();
            if ($res) {
                $ret[] = $res;
            }
        }
        return $ret;
    }

    public static function getUpdates($from, $to)
    {
        $ret = self::getVpcUpdates($from, $to);

        //web/Vps/ * /Update nach updates durchsuchen
        if (is_dir('./Vps')) {
            foreach (new DirectoryIterator('./Vps') as $d) {
                if ($d->isDir() && substr($d->__toString(), 0, 1) != '.'
                    && is_dir('./Vps/'.$d->__toString().'/Update')
                ) {
                    $u = self::getUpdatesForDir('Vps/'.$d->__toString(), $from, $to);
                    foreach ($u as $i) $i->_tags[] = 'web';
                    $ret = array_merge($ret, $u);
                }
            }
        }

        $u = self::getUpdatesForDir(VPS_PATH.'/Vps', $from, $to);
        $ret = array_merge($ret, $u);
        $u = self::getUpdatesForDir('./update', $from, $to);
        foreach ($u as $i) $i->_tags[] = 'web';
        $ret = array_merge($ret, $u);
        if (defined('DOC_CMS')) { //HACK
            $u = self::getUpdatesForDir(DOC_CMS.'/Vps', $from, $to);
            $ret = array_merge($ret, $u);
        }
        $ret = self::_sortByRevision($ret);
        return $ret;
    }

    public static function getVpcUpdates($from, $to)
    {
        $ret = array();
        $processed = array();
        foreach (Vps_Component_Abstract::getComponentClasses() as $class) {
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
            if (substr($i, 0, 1)!='/') $dirs[$k] = getcwd().'/'.$i;
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
                            if ($file != './update') {
                                $n = str_replace(DIRECTORY_SEPARATOR, '_', $file).'_';
                            }
                            if (substr($n, 0, 8) == 'vps-lib_') continue;
                            if (substr($n, 0, 8) == 'library_') continue;
                            $n .= 'Update_'.$nr;
                            $update = self::createUpdate($n, $i->getPathname());
                            if ($update) $ret[] = $update;
                        }
                    }
                }
                $path = $path . '/Always';
                if (is_dir($path)) {
                    foreach (new DirectoryIterator($path) as $i) {
                        if (!$i->isFile()) continue;
                        $f = $i->__toString();
                        $fileType = substr($f, -4);
                        if ($fileType != '.php') continue;
                        $f = substr($f, 0, -4);
                        $n = str_replace(DIRECTORY_SEPARATOR, '_', $file).'_Update_Always_'.$f;
                        if (is_instance_of($n, 'Vps_Update')) {
                            $ret[] = new $n(null);
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
            } else if (is_file(VPS_PATH . '/' . $file . '.sql')) {
                $filename = VPS_PATH . '/' . $file . '.sql';
                $isSql = true;
            }
        } else {
            $isSql = substr($filename, -4) == '.sql';
        }
        $nr = (int)substr(strrchr($class, '_'), 1);
        $update = null;
        if ($isSql) {
            $update = new Vps_Update_Sql($nr, $class);
            $update->sql = file_get_contents($filename);
            if (preg_match("#\\#\\s*tags:(.*)#", $update->sql, $m)) {
                $update->_tags = explode(' ', trim($m[1]));
            }
            $update->_tags[] = 'db';
        } else {
            if (is_instance_of($class, 'Vps_Update')) {
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
}
