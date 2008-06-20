<?php
class Vps_Component_Cache extends Zend_Cache_Core {
    
    static private $_instance;
    
    public function __construct()
    {
        parent::__construct(array(
            'lifetime' => 30*60
        ));
        
        $backend = new Zend_Cache_Backend_File(array(
            'cache_dir' => 'application/cache/component',
            'hashed_directory_level' => 2,
            'file_name_prefix' => 'vpc',
            'hashed_directory_umask' => 0770
        ));
        
        $this->setBackend($backend);
    }
    
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function cleanByTag($tag)
    {
        $this->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($tag));
    }
    
    public function remove($componentId)
    {
        parent::remove($this->getCacheIdFromComponentId($componentId));
    }
    
    public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
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
                    $class = $data->componentClass;
                    while ($class) { // Alle Component.php der Klassenhierarchie prüfen
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
}