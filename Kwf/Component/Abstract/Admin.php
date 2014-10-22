<?php
class Kwf_Component_Abstract_Admin
{
    protected $_class;
    static private $_instances = array();

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
        if (!isset(self::$_instances[$componentClass])) {
            $c = self::getComponentClass($componentClass, 'Admin');
            if (!$c) { return null; }
            self::$_instances[$componentClass] = new $c($componentClass);
        }
        return self::$_instances[$componentClass];
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
        $router = Kwf_Controller_Front::getInstance()->getWebRouter();
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
        } else if ($ext == array('tpl', 'twig') && !$returnClass) {
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
                $exts = $file['ext'];
                if (!is_array($exts)) $exts = array($exts);
                foreach ($exts as $ext) {
                    $f = $path.'/'.$file['filename'].'.'.$ext;
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
                            continue 3;
                        }
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

    /**
     * @deprecated use menuConfig instead
     */
    public function addResources(Kwf_Acl $acl)
    {
    }

    /**
     * Hilfsfunktion zum hinzufügen von Menüpunkten für alle Komponenten dieser klasse
     * @deprecated use Kwf_Component_Abstract_MenuConfig_Trl_SameClass instead
     */
    protected function _addResourcesBySameClass(Kwf_Acl $acl)
    {
        throw new Kwf_Exception('port to menuConfig');
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
            throw new Kwf_Exception("__toString failed for row ".get_class($data->getComponent()->getRow()->getModel()).' you might want to set _toStringField or override componentToString (component \''.$data->componentClass.'\')');
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

        $cacheId = 'componentsWithDependsOnRow';
        if (!$componentsWithDependsOnRow = Kwf_Cache_SimpleStatic::fetch($cacheId)) {
            $componentsWithDependsOnRow = array();
            foreach (Kwc_Abstract::getComponentClasses() as $c) {
                $a = Kwc_Admin::getInstance($c);
                if ($a instanceof Kwf_Component_Abstract_Admin_Interface_DependsOnRow) {
                    $componentsWithDependsOnRow[] = $c;
                }
            }
            Kwf_Cache_SimpleStatic::add($cacheId, $componentsWithDependsOnRow);
        }
        foreach ($componentsWithDependsOnRow as $c) {
            $ret[] = Kwc_Admin::getInstance($c);
        }

        return $ret;
    }

    /**
     * Called when duplication of a number of components finished
     */
    public function afterDuplicate($rootSource, $rootTarget)
    {
    }
}
