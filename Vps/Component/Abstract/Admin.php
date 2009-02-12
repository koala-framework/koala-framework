<?php
class Vps_Component_Abstract_Admin
{
    protected $_class;

    protected function __construct($class)
    {
        $this->_class = $class;
        $this->_init();
    }

    protected function _init()
    {
    }

    public static function getInstance($componentClass)
    {
        static $instances = array();
        if (!isset($instances[$componentClass])) {
            $c = self::getComponentFile($componentClass, 'Admin', 'php', true);
            if (!$c) { return null; }
            $instances[$componentClass] = new $c($componentClass);
        }
        return $instances[$componentClass];
    }

    public static function getAvailableComponents($path = '')
    {
        if ($path == '') {
            $path = VPS_PATH . '/Vpc';
        }
        $return = array();
        foreach (new DirectoryIterator($path) as $item) {
            if ($item->getFilename() != '.' &&
                $item->getFilename() != '..' &&
                $item->getFilename() != '.svn' &&
                $item->isDir()) {

                $pathNew = "$path/$item";
                $return = array_merge(self::getAvailableComponents($pathNew), $return);

            } else if (substr($item->getFilename(), -4) == '.php') {

                $class = str_replace('/', '_', $item->getPath());
                $class = strrchr($class, 'Vpc_');
                $class .= '_' . str_replace('.php', '', $item->getFilename());
                if (Vps_Loader::classExists($class) && is_subclass_of($class, 'Vpc_Abstract')) {
                    $return[] = $class;
                }

            }
        }
        return $return;
    }

    public function getExtConfig()
    {
        $name = Vpc_Abstract::getSetting($this->_class, 'componentName');
        $icon = Vpc_Abstract::getSetting($this->_class, 'componentIcon');
        return array(
            'xtype'         => 'vps.autoform',
            'controllerUrl' => $this->getControllerUrl(),
            'componentName' => $name,
            'componentIcon' => $icon->__toString()
        );
    }

    public function getControllerUrl($class = null)
    {
        if (is_null($class)) $class = $this->_class;
        if (substr($class, -10) == 'Controller') {
            $class = substr($class, 0, -10);
        }
        return '/admin/component/edit/' . $class;
    }
    public function setup()
    {
    }


    public static function getComponentFile($class, $filename = '', $ext = 'php', $returnClass = false)
    {
        if (is_object($class)) $class = get_class($class);
        $ret = null;
        while (!$ret && $class != '') {
            $curClass = $class;
            if ($filename != '') {
                if (substr($curClass, -10) == '_Component') {
                    $curClass = substr($curClass, 0, -10);
                }
                $curClass =  $curClass . '_' . $filename;
            }
            $file = str_replace('_', DIRECTORY_SEPARATOR, $curClass) . '.' . $ext;
            $dirs = explode(PATH_SEPARATOR, get_include_path());
            foreach ($dirs as $dir) {
                if ($dir == '.') $dir = getcwd();
                $path = $dir . '/' . $file;
                if (is_file($path)) {
                    $ret = $returnClass ? $curClass : $path;
                    break;
                }
            }
            $class = get_parent_class($class);
        }
        return $ret;
    }
    public static function getComponentClass($class, $filename)
    {
        return self::getComponentFile($class, $filename, 'php', true);
    }

    public function delete()
    {
    }

    public function onRowUpdate($row)
    {
        $this->_onRowAction($row);
    }

    public function onRowDelete($row)
    {
        $this->_onRowAction($row);
    }

    public function onRowInsert($row)
    {
        $this->_onRowAction($row);
    }

    public function onRowSave($row)
    {
        $this->_onRowAction($row);
    }

    protected function _onRowAction($row)
    {
    }

    public function addResources(Vps_Acl $acl)
    {
    }
}
