<?php
class Vps_Component_Abstract_Admin
{
    protected $_class;
    const EXT_CONFIG_DEFAULT = 'default';
    const EXT_CONFIG_SHARED = 'shared';

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
            $path = VPS_PATH . '/Vpc';
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
                $class = strrchr($class, 'Vpc_');
                $class .= '_' . str_replace('.php', '', $item->getFilename());
                if (Vps_Loader::classExists($class) && is_subclass_of($class, 'Vpc_Abstract')) {
                    $return[] = $class;
                }

            }
        }
        return $return;
    }

    public function getExtConfig($type = self::EXT_CONFIG_DEFAULT)
    {
        if ($type == self::EXT_CONFIG_DEFAULT && Vpc_Abstract::getFlag($this->_class, 'sharedDataClass')) return array();
        if ($type == self::EXT_CONFIG_SHARED && !Vpc_Abstract::getFlag($this->_class, 'sharedDataClass')) return array();

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
        $files = Vpc_Abstract::getSetting($class, 'componentFiles');
        $key = false;

        //precomputed aus Vps/Component/Abstract.php
        if ($ext == 'php' && $returnClass) {
            $key = $filename;
        } else if ($ext == 'tpl' && !$returnClass) {
            $key = $filename.'.tpl';
        }
        if ($key) {
            Vps_Benchmark::count('getComponentFile precomputed');
            if (isset($files[$key])) return $files[$key];
        }
        Vps_Benchmark::count('getComponentFile slow');
        $ret = self::getComponentFiles($class, array(array('filename'=>$filename, 'ext'=>$ext, 'returnClass'=>$returnClass)));
        return $ret[0];
    }

    public static function getComponentFiles($class, $files)
    {
        $ret = array();
        $paths = Vpc_Abstract::getSetting($class, 'parentFilePaths'); //teuer, nur einmal aufrufen
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
        if (strpos($name, '.') !== false) $name = substr($name, strrpos($name, '.') + 1);
        $icon = Vpc_Abstract::getSetting($this->_class, 'componentIcon');
        if (count($components) > 1) {
            if (!$acl->has('vpc_news')) {
                $acl->add(
                    new Vps_Acl_Resource_MenuDropdown(
                        'vpc_news', array('text'=>$name, 'icon'=>$icon)
                    ), 'vps_component_root'
                );
            }
            foreach ($components as $c) {
                $t = $c->getTitle();
                if (!$t) $t = $c->componentId;
                $acl->add(
                    new Vps_Acl_Resource_Component_MenuUrl(
                        $c, array('text'=>$t, 'icon'=>$icon)
                    ), 'vpc_news'
                );
            }
        } else if (count($components) == 1) {
            $c = $components[0];
            $name = $this->_addResourcesBySameClassResourceName($c);
            $acl->add(
                new Vps_Acl_Resource_Component_MenuUrl(
                    $c, array('text'=>$name, 'icon'=>$icon)
                ), 'vps_component_root'
            );
        }
    }

    //TODO nicht recht flexibel... NUR in Vpc_News_Directory_Trl_Admin verwenden
    //falls es wo anders gebraucht wird bitte flexibler machen
    protected function _addResourcesBySameClassResourceName($c)
    {
        $ret = Vpc_Abstract::getSetting($this->_class, 'componentName');
        if (strpos($ret, '.') !== false) $ret = substr(strrchr($ret, '.'), 1);
        return $ret;
    }

    protected final function _getSetting($name)
    {
        return Vpc_Abstract::getSetting($this->_class, $name);
    }


    public function componentToString(Vps_Component_Data $data)
    {
        if (!$data->getComponent()->getRow()) {
            throw new Vps_Exception('Please implement Admin::componentToString for '.$data->componentClass);
        }
        try {
            return $data->getComponent()->getRow()->__toString();
        } catch (Zend_Db_Table_Row_Exception $e) {
            throw new Vps_Exception("__toString failed for component ".$data->componentClass.' you might want to set _toStringField or override componentToString');
        } catch (Vps_Exception $e) {
            throw new Vps_Exception("__toString failed for component ".$data->componentClass.' you might want to set _toStringField or override componentToString');
        }
    }

    public function gridColumns()
    {
        $ret = array();
        $c = new Vps_Grid_Column('string', trlVps('Name'));
        $c->setData(new Vps_Component_Abstract_ToStringData($this->_class));
        $ret['string'] = $c;
        return $ret;
    }

    public final static function getDependsOnRowInstances()
    {
        $ret = array();

        $cache = Vps_Cache::factory('Core', 'Memcached', array('automatic_serialization'=>true, 'lifetime'=>null));
        if (!$componentsWithDependsOnRow = $cache->load('componentsWithDependsOnRow')) {
            foreach (Vpc_Abstract::getComponentClasses() as $c) {
                $a = Vpc_Admin::getInstance($c);
                if ($a instanceof Vps_Component_Abstract_Admin_Interface_DependsOnRow) {
                    $componentsWithDependsOnRow[] = $c;
                }
            }
            $cache->save($componentsWithDependsOnRow, 'componentsWithDependsOnRow');
        }
        foreach ($componentsWithDependsOnRow as $c) {
            $ret[] = Vpc_Admin::getInstance($c);
        }

        return $ret;
    }
}
