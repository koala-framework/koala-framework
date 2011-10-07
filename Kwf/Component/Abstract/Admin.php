<?php
class Kwf_Component_Abstract_Admin
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
            $c = self::getComponentClass($componentClass, 'Admin');
            if (!$c) { return null; }
            $instances[$componentClass] = new $c($componentClass);
        }
        return $instances[$componentClass];
    }

    public static function getAvailableComponents($path = '')
    {
        if ($path == '') {
            $path = KWF_PATH . '/Kwc';
        }
        $return = array();
        foreach (new DirectoryIterator($path) as $item) {
            if ($item->getFilename() != '.' &&
                $item->getFilename() != '..' &&
                $item->getFilename() != '.svn' &&
                $item->getFilename() != '.git' &&
                $item->isDir()) {

                $pathNew = "$path/$item";
                $return = array_merge(self::getAvailableComponents($pathNew), $return);

            } else if (substr($item->getFilename(), -4) == '.php') {

                $class = str_replace('/', '_', $item->getPath());
                $class = strrchr($class, 'Kwc_');
                $class .= '_' . str_replace('.php', '', $item->getFilename());
                if (class_exists($class) && is_subclass_of($class, 'Kwc_Abstract')) {
                    $return[] = $class;
                }

            }
        }
        return $return;
    }

    public final function getExtConfig($type = Kwf_Component_Abstract_ExtConfig_Abstract::TYPE_DEFAULT)
    {
        return Kwf_Component_Abstract_ExtConfig_Abstract::getInstance($this->_class)->getConfig($type);
    }

    //TODO: in ExtConfig/Abstract verschieben
    public function getControllerUrl($class = 'Index')
    {
        $urlOptions = array(
            'class' => $this->_class,
            'componentController' => $class,
            'action' => ''
        );
        $router = Kwf_Controller_Front::getInstance()->getRouter();

        if (Zend_Registry::isRegistered('testRootComponentClass')) {
            $urlOptions['root'] = Zend_Registry::get('testRootComponentClass');
            $name = 'kwf_test_componentedit';
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
        if (is_object($class)) {
            if ($class instanceof Kwf_Component_Abstract) $class = get_class($class);
            else if ($class instanceof Kwf_Component_Data) $class = $class->componentClass;
            else throw new Kwf_Exception("invalid class");
        }
        $files = Kwc_Abstract::getSetting($class, 'componentFiles');
        $key = false;

        //precomputed aus Kwf/Component/Abstract.php
        if ($ext == 'php' && $returnClass) {
            $key = $filename;
        } else if ($ext == 'tpl' && !$returnClass) {
            $key = $filename.'.tpl';
        }
        if ($key) {
            Kwf_Benchmark::count('getComponentFile precomputed');
            if (isset($files[$key])) return $files[$key];
        }
        Kwf_Benchmark::count('getComponentFile slow');
        $ret = self::getComponentFiles($class, array(array('filename'=>$filename, 'ext'=>$ext, 'returnClass'=>$returnClass)));
        return $ret[0];
    }

    public static function getComponentFiles($class, $files)
    {
        $ret = array();
        $paths = Kwc_Abstract::getSetting($class, 'parentFilePaths'); //teuer, nur einmal aufrufen
        foreach ($files as $kFile => $file) {
            if (isset($file['multiple']) && $file['multiple']) {
                $ret[$kFile] = array();
            } else {
                $ret[$kFile] = false;
            }
            foreach ($paths as $path => $c) {
                $f = $path.'/'.$file['filename'].'.'.$file['ext'];
                if (file_exists($f)) {
                    if ($file['returnClass']) {
                        $i = $c.'_'.str_replace('/', '_', $file['filename']);
                    } else {
                        $i = $f;
                    }
                    if (isset($file['multiple']) && $file['multiple']) {
                        $ret[$kFile][] = $i;
                    } else {
                        $ret[$kFile] = $i;
                        continue 2;
                    }
                }
            }
        }
        return $ret;
    }

    public static function getComponentClass($class, $filename)
    {
        return self::getComponentFile($class, str_replace('_', '/', $filename), 'php', true);
    }

    public function delete()
    {
    }

    public function addResources(Kwf_Acl $acl)
    {
    }

    /**
     * Hilfsfunktion zum hinzufügen von Menüpunkten für alle Komponenten dieser klasse
     */
    protected function _addResourcesBySameClass(Kwf_Acl $acl)
    {
        $dropdownName = 'kwc_'.$this->_class;

        //BEGIN hack
        //TODO im 1.11 gscheite lösung
        $isTrl = is_instance_of($this->_class, 'Kwc_Chained_Trl_Component');
        $hasTrl = false;
        foreach (Kwc_Abstract::getComponentClasses() as $cls) {
            if (is_instance_of($cls, 'Kwc_Chained_Trl_Component')) {
                if (Kwc_Abstract::getSetting($cls, 'masterComponentClass') == $this->_class) {
                    $dropdownName = 'kwc_'.$cls;
                    $hasTrl = true;
                    break;
                }
            }
        }
        /*
            class Kwf_Component_MenuConfig_Abstract {
                public function __construct(...)
                public static function getInstance($componentclass);
                public function getPriority(); //trls haben niedrige priorität
                abstract public function addResources(Kwf_Acl $acl);
            }
            $ret['menuConfig'] = 'Kwf_Component_MenuConfig_SameClass'; //fürs deutsche
            $ret['menuConfig'] = 'Kwf_Component_MenuConfig_Trl_SameClass'; //fürs trls, erstellt ein dropdown und verschiebt auch das deutsche da hinein

            $ret['menuConfigDropdownName'] = '';
            $ret['menuConfig'] = 'Kwf_Component_MenuConfig_Dropdown_SameClass'; //fürs deutsche
        */
        //END hack

        $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        $name = Kwc_Abstract::getSetting($this->_class, 'componentName');
        if (strpos($name, '.') !== false) $name = substr($name, strrpos($name, '.') + 1);
        $icon = Kwc_Abstract::getSetting($this->_class, 'componentIcon');
        if ($hasTrl || $isTrl || count($components) > 1) {
            if (!$acl->has($dropdownName)) {
                $acl->add(
                    new Kwf_Acl_Resource_MenuDropdown(
                        $dropdownName, array('text'=>$name, 'icon'=>$icon)
                    ), 'kwf_component_root'
                );
            }
            foreach ($components as $c) {
                $t = $c->getTitle();
                if (!$t) $t = $name;
                if ($hasTrl || $isTrl) {
                    $t .= ' ('.$c->getLanguageData()->name.')';
                }
                $acl->add(
                    new Kwf_Acl_Resource_Component_MenuUrl(
                        $c, array('text'=>$t, 'icon'=>$icon)
                    ), $dropdownName
                );
            }
        } else if (count($components) == 1) {
            $c = $components[0];
            $name = $this->_addResourcesBySameClassResourceName($c);
            $acl->add(
                new Kwf_Acl_Resource_Component_MenuUrl(
                    $c, array('text'=>$name, 'icon'=>$icon)
                ), 'kwf_component_root'
            );
        }
    }

    //TODO nicht recht flexibel... NUR in Kwc_News_Directory_Trl_Admin verwenden
    //falls es wo anders gebraucht wird bitte flexibler machen
    protected function _addResourcesBySameClassResourceName($c)
    {
        $ret = Kwc_Abstract::getSetting($this->_class, 'componentName');
        if (strpos($ret, '.') !== false) $ret = substr(strrchr($ret, '.'), 1);
        return $ret;
    }

    protected final function _getSetting($name)
    {
        return Kwc_Abstract::getSetting($this->_class, $name);
    }


    public function componentToString(Kwf_Component_Data $data)
    {
        if (!$data->getComponent()->getRow()) {
            throw new Kwf_Exception('Please implement Admin::componentToString for '.$data->componentClass);
        }
        try {
            return $data->getComponent()->getRow()->__toString();
        } catch (Kwf_Exception $e) {
            throw new Kwf_Exception("__toString failed for row ".get_class($data->getComponent()->getRow()->getModel()).' you might want to set _toStringField or override componentToString');
        }
    }

    public function gridColumns()
    {
        $ret = array();
        $c = new Kwf_Grid_Column('string', trlKwf('Name'));
        $c->setData(new Kwf_Component_Abstract_ToStringData($this->_class));
        $ret['string'] = $c;
        return $ret;
    }

    public final static function getDependsOnRowInstances()
    {
        $ret = array();

        $cache = Kwf_Cache::factory('Core', 'Apc', array('automatic_serialization'=>true, 'lifetime'=>null));
        if (!$componentsWithDependsOnRow = $cache->load('componentsWithDependsOnRow')) {
            foreach (Kwc_Abstract::getComponentClasses() as $c) {
                $a = Kwc_Admin::getInstance($c);
                if ($a instanceof Kwf_Component_Abstract_Admin_Interface_DependsOnRow) {
                    $componentsWithDependsOnRow[] = $c;
                }
            }
            $cache->save($componentsWithDependsOnRow, 'componentsWithDependsOnRow');
        }
        foreach ($componentsWithDependsOnRow as $c) {
            $ret[] = Kwc_Admin::getInstance($c);
        }

        return $ret;
    }
}
