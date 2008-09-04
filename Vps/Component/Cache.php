<?php
class Vps_Component_Cache extends Zend_Cache_Core
{
    static private $_instance;
    private $_backend;
    private $_process = array(
        'insert' => array(), 
        'update' => array(), 
        'delete' => array()
    );
    private $_processed = false;
    
    public function __construct()
    {
        parent::__construct(array(
            'lifetime' => 30*60
        ));
        
        $this->_backend = new Zend_Cache_Backend_File(array(
            'cache_dir' => $this->_getCacheDir(),
            'hashed_directory_level' => 0,
            'file_name_prefix' => 'vpc',
            'hashed_directory_umask' => 0777,
            'cache_file_umask' => 0777
        ));
        
        $this->setBackend($this->_backend);
    }

    public function __destruct()
    {
        if (!$this->_processed && $this->_process) {
            //exceptions funktionieren im destruktor nicht
            d("There are unprocessed cache-actions, you must call process() somewhere");
        }
    }
    
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function insert($row)
    {
        if ($this->_processed) throw new Vps_Exception("ComponentCache: allready processed");
        $this->_process['insert'][] = $row;
    }
    
    public function update($row)
    {
        if ($this->_processed) throw new Vps_Exception("ComponentCache: allready processed");
        $this->_process['update'][] = $row;
    }
    
    public function delete($row)
    {
        if ($this->_processed) throw new Vps_Exception("ComponentCache: allready processed");
        $this->_process['delete'][] = $row;
    }
    
    public function process()
    {
        $this->_processed = true;
        foreach ($this->_process as $action => $process) {
            foreach ($process as $row) {
                foreach (Vpc_Abstract::getComponentClasses() as $c) {
                    $method = 'onRow' . ucfirst($action);
                    Vpc_Admin::getInstance($c)->$method($row);
                }
            }
        }
    }

    public function save($data, $componentClass, $componentId, $tags = array())
    {
        $this->_setCacheDir($componentClass);
        parent::save($data, $componentId, $tags);
    }

    public function remove($componentClass, $componentId = null)
    {
        if (!Vpc_Abstract::getSetting($componentClass, 'viewCache')) return;
        if ($componentId) {
            $this->_setCacheDir($componentClass);
            $cacheId = $this->getCacheIdFromComponentId($componentId);
            p($cacheId);
            if (parent::remove($cacheId)) {
                Vps_Benchmark::info("Cache für Komponente '$componentClass' mit Id '$componentId' gelöscht.");
            } else {
                Vps_Benchmark::info("Cache NICHT für Komponente '$componentClass' mit Id '$componentId' gelöscht. (keiner vorhanden)");
            }
            $cacheId = $this->getCacheIdFromComponentId($componentId, true);
            p($cacheId);
            parent::remove($cacheId);
            $cacheId = $this->getCacheIdFromComponentId($componentId, false, true);
            p($cacheId);
            parent::remove($cacheId);
        } else {
            if ($this->rm_recursive($this->_getCacheDir($componentClass))) {
                Vps_Benchmark::info("Cache für Komponente '$componentClass' gelöscht.");
            } else {
                Vps_Benchmark::info("Cache NICHT für Komponente '$componentClass' gelöscht. (keiner vorhanden)");
            }
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
        static $checkComponentModification;
        if (is_null($checkComponentModification)) {
            $checkComponentModification = Vps_Registry::get('config')->debug->componentCache->checkComponentModification;
        }
        if (!$checkComponentModification) {
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
    
    public function getCacheIdFromComponentId($componentId, $isMaster = false, $isHasContent = false)
    {
        if ($isMaster) { $componentId .= '-master'; }
        if ($isHasContent) { $componentId .= '-hasContent'; }
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
            mkdir($cacheDir, 0777);
        }
        $this->_backend->setCacheDir($cacheDir);
    }
    
    private function _getCacheDir($componentClass = '')
    {
        $cacheDir = 'application/cache/component';
        if ($componentClass != '') $cacheDir .= '/' . $componentClass;
        return $cacheDir;
    }
    
    function rm_recursive($path)
    {
        $origipath = $path;
        if (!is_dir($path)) { return false; }
        $handler = opendir($path);
        while (true) {
            $item = readdir($handler);
            if ($item == "." or $item == "..") {
                continue;
            } elseif (gettype($item) == "boolean") {
                closedir($handler);
                if (!@rmdir($path)) {
                    return false;
                }
                if ($path == $origipath) {
                    break;
                }
                $path = substr($path, 0, strrpos($path, "/"));
                $handler = opendir($path);
            } elseif (is_dir($path."/".$item)) {
                closedir($handler);
                $path = $path."/".$item;
                $handler = opendir($path);
            } else {
                unlink($path."/".$item);
            }
        }
        return true;
    }
    
}