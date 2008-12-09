<?php
abstract class Vps_Update
{
    protected $_actions = array();
    protected $_revision;

    public function __construct($revision)
    {
        $this->_revision = (int)$revision;
        $this->_init();
    }

    public function getRevision()
    {
        return $this->_revision;
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
                    $ret = array_merge($ret, $u);
                }
            }
        }

        $u = self::getUpdatesForDir(VPS_PATH.'/Vps', $from, $to);
        $ret = array_merge($ret, $u);
        $ret = self::_sortByRevision($ret);
        return $ret;
    }

    public static function getVpcUpdates($from, $to)
    {
        $ret = array();
        $processed = array();
        foreach (Vps_Component_Abstract::getComponentClasses(false) as $class) {
            while ($class != '') {
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
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $dir) {
            if ($dir == '.') $dir = getcwd();
            if (substr($file, 0, strlen($dir)) == $dir) {
                $file = substr($file, strlen($dir)+1);
            }
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $path =  $path . '/Update';
                if (is_dir($path)) {
                    foreach (new DirectoryIterator($path) as $f) {
                        if (!$f->isFile()) continue;
                        $f = $f->__toString();
                        if (substr($f, -4) != '.php') continue;
                        $f = substr($f, 0, -4);
                        if (!is_numeric($f)) continue;
                        $nr = (int)$f;
                        if ($nr >= $from && $nr < $to) {
                            $n = str_replace(DIRECTORY_SEPARATOR, '_', $file).'_Update_'.$nr;
                            if (is_instance_of($n, 'Vps_Update')) {
                                $ret[] = new $n($nr);
                            }
                        }
                    }
                }
                break;
            }
        }
        $ret = self::_sortByRevision($ret);
        return $ret;
    }

    private static function _sortByRevision($updates)
    {
        $revisions = array();
        foreach ($updates as $k=>$u) {
            $revisions[$k] = $u->getRevision();
        }
        asort($revisions, SORT_NUMERIC);
        $ret = array();
        foreach (array_keys($revisions) as $k) {
            $ret[] = $updates[$k];
        }
        return $ret;
    }
}
