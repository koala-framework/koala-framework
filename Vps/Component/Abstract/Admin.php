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

    /**
     * @return $this
     */
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
        if (!self::getComponentFile($this->_class, 'Controller')) {
            return array();
        }
        if (!Vpc_Abstract::hasSetting($this->_class, 'componentName')
            || !Vpc_Abstract::getSetting($this->_class, 'componentName'))
        {
            //wenn das probleme verursact ignorieren - aber es erspart lange fehlersuche warum eine komp. nicht angezeigt wird :D
            throw new Vps_Exception("Component '$this->_class' does have no componentName but must have one for editing");
        }
        $ret = array(
            'form' => array(
                'xtype' => 'vps.autoform',
                'controllerUrl' => $this->getControllerUrl(),
                'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
                'icon' => $this->_getSetting('componentIcon')->__toString()
            )
        );
        return $ret;
    }

    public function getControllerUrl($class = 'Index')
    {
        $urlOptions = array(
            'class' => $this->_class,
            'componentController' => $class,
            'action' => ''
        );
        $router = Vps_Controller_Front::getInstance()->getRouter();

        if (Zend_Registry::isRegistered('testRootComponentClass')) {
            $urlOptions['root'] = Zend_Registry::get('testRootComponentClass');
            $name = 'vps_test_componentedit';
        } else {
            $name = 'componentedit';
        }
        return $router->assemble($urlOptions, $name, true);
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

    public function addResources(Vps_Acl $acl)
    {
    }

    /**
     * Hilfsfunktion zum hinzufügen von Menüpunkten für alle Komponenten dieser klasse
     */
    protected function _addResourcesBySameClass(Vps_Acl $acl)
    {
        $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        $name = Vpc_Abstract::getSetting($this->_class, 'componentName');
        $icon = Vpc_Abstract::getSetting($this->_class, 'componentIcon');
        if (strpos($name, '.') !== false) $name = substr($name, strrpos($name, '.') + 1);

        if (count($components) > 1) {
            $acl->add(new Vps_Acl_Resource_MenuDropdown('vpc_news',
                        array('text'=>$name, 'icon'=>$icon)), 'vps_component_root');
            foreach ($components as $c) {
                $acl->add(new Vps_Acl_Resource_Component_MenuUrl($c,
                        array('text'=>$c->getTitle(), 'icon'=>$icon),
                        Vpc_Admin::getInstance($c->componentClass)->getControllerUrl().'?componentId='.$c->dbId), 'vpc_news');
            }
        } else if (count($components) == 1) {
            $c = $components[0];
            $acl->add(new Vps_Acl_Resource_Component_MenuUrl($c,
                    array('text'=>$name, 'icon'=>$icon),
                    Vpc_Admin::getInstance($c->componentClass)->getControllerUrl().'?componentId='.$c->dbId), 'vps_component_root');

        }
    }

    protected final function _getSetting($name)
    {
        return Vpc_Abstract::getSetting($this->_class, $name);
    }
}
