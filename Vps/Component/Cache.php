<?php
class Vps_Component_Cache extends Zend_Cache_Core {
    
    static private $_instance;
    private $_backend;
    
    public function __construct()
    {
        parent::__construct(array(
            'lifetime' => 30*60
        ));
        
        $this->_backend = new Zend_Cache_Backend_File(array(
            'cache_dir' => $this->_getCacheDir(),
            'hashed_directory_level' => 0,
            'file_name_prefix' => 'vpc',
            'hashed_directory_umask' => 0770
        ));
        
        $this->setBackend($this->_backend);
    }
    
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
        
    public function save($data, $componentClass, $componentId, $tags)
    {
        $this->_setCacheDir($componentClass);
        parent::save($data, $componentId, $tags);
    }
    
    public function remove($component)
    {
        if ($component instanceof Vps_Component_Data) {
            $this->_setCacheDir($component->componentClass);
            parent::remove($this->getCacheIdFromComponentId($component->componentId));
            //p("Cache für Komponente '$component->componentClass' mit Id '$component->componentId' gelöscht.");
        } else {
            $this->rm_recursive($this->_getCacheDir($component));
            //p("Cache für Komponente '$component' gelöscht.");
        }
    }
    
    public function load($componentClass, $id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        $this->_setCacheDir($componentClass);
        if (!$this->test($id)) {
            return false;
        }
        return parent::load($id, $doNotTestCacheValidity, $doNotUnserialize);
    }
    
    public function test($id)
    {
        $lastModified = parent::test($id);
        if (!Zend_Registry::get('config')->debug->componentCache->checkComponentModification) {
            return $lastModified;
        }
        
        $componentId = $this->getComponentIdFromCacheId($id);
        if ($componentId) {
            $data = Vps_Component_Data_Root::getInstance()->getComponentById($componentId);
            if ($data) {
                $file = Vpc_Admin::getComponentFile($data->componentClass, 'Component', 'tpl');
                if ($lastModified > filemtime($file)) { // Wenn Component.tpl nicht geändert wurde, Component.php prüfen
                    // Alle Component.php der Klassenhierarchie prüfen
                    foreach (Vpc_Abstract::getParentClasses($data->componentClass) as $class) {
                        $file = Vpc_Admin::getComponentFile($class, 'Component', 'php');
                        if ($lastModified < filemtime($file)) {
                            return false;
                        }
                        $class = get_parent_class($class);
                    }
                    return true;
                }
            }
        }
        return true;
    }
    
    public function getCacheIdFromComponentId($componentId, $isMaster = false)
    {
        if ($isMaster) { $componentId .= '-master'; }
        return str_replace('-', '__', $componentId);
    }
    
    public function getComponentIdFromCacheId($cacheId)
    {
        if (substr($cacheId, -8) == '__master') {
            return null;
        } else {
            return str_replace('__', '-', $cacheId);
        }
    }
    
    protected function _setCacheDir($componentClass)
    {
        $cacheDir = $this->_getCacheDir($componentClass);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);
        }
        $this->_backend->setCacheDir($cacheDir);
    }
    
    private function _getCacheDir($componentClass = '')
    {
        $cacheDir = 'application/cache/component';
        if ($componentClass != '') $cacheDir .= '/' . $componentClass;
        return $cacheDir;
    }
    
    function rm_recursive($filepath)
    {
        if (is_dir($filepath) && !is_link($filepath)) {
            if ($dh = opendir($filepath)) {
                while (($sf = readdir($dh)) !== false) {
                    if ($sf == '.' || $sf == '..') { continue; }
                    if (!rm_recursive($filepath.'/'.$sf)) {
                        throw new Exception($filepath.'/'.$sf.' could not be deleted.');
                    }
                }
                closedir($dh);
            }
            return rmdir($filepath);
        }
        if (is_dir($filepath)) unlink($filepath);
    }
    
}