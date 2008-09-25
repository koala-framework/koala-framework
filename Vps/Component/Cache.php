<?php
class Vps_Component_Cache extends Zend_Cache_Core
{
    const CLEANING_MODE_COMPONENT_CLASS = 'componentClass';

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
            'lifetime' => null,
            'write_control'=>false,
            'automatic_cleaning_factor'=>0
        ));
        
        $this->_backend = new Vps_Cache_Backend_Db(array(
            'table' => 'cache_component',
            'adapter' => Vps_Registry::get('db')
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
    
    public function process($isLastCall = true)
    {
        $this->_processed = $isLastCall;
        foreach ($this->_process as $action => $process) {
            foreach ($process as $row) {
                foreach (Vpc_Abstract::getComponentClasses() as $c) {
                    $method = 'onRow' . ucfirst($action);
                    Vpc_Admin::getInstance($c)->$method($row);
                }
            }
        }
        Vps_Dao_Index::process();
    }

    /**
     * @param array/Vps_Component_Data/componentId
     **/
    public function remove($componentId)
    {
        if (is_array($componentId)) {
            foreach ($componentId as $c) {
                $this->remove($c);
            }
            return;
        }
        if ($componentId instanceof Vps_Component_Data) {
            $componentClass = $componentId->componentClass;
            $componentId = $componentId->componentId;
        } else {
            $componentClass = '';
        }
        $cacheId = $this->getCacheIdFromComponentId($componentId);
        if (parent::remove($cacheId)) {
            Vps_Benchmark::info("Cache für Komponente $componentClass mit Id '$componentId' gelöscht.");
        } else {
            Vps_Benchmark::info("Cache NICHT für Komponente $componentClass mit Id '$componentId' gelöscht. (keiner vorhanden)");
        }
        $cacheId = $this->getCacheIdFromComponentId($componentId, true);
        parent::remove($cacheId);
        $cacheId = $this->getCacheIdFromComponentId($componentId, false, true);
        parent::remove($cacheId);
    }

    public function cleanComponentClass($componentClass)
    {
        $this->clean(self::CLEANING_MODE_COMPONENT_CLASS, $componentClass);
        Vps_Benchmark::info("Kompletter Cache für Komponente '$componentClass' gelöscht.");
    }

    public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        //TODO: nicht test() aufrufen, macht 2. sql abfrage
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
                if ($lastModified < filemtime($file)) return false;
                // Alle Component.php der Klassenhierarchie prüfen
                foreach (Vpc_Abstract::getParentClasses($data->componentClass) as $class) {
                    $file = Vpc_Admin::getComponentFile($class, 'Component', 'php');
                    if ($lastModified < filemtime($file)) return false;
                }
            }
        }
        return $lastModified;
    }
    
    public function getCacheIdFromComponentId($componentId, $masterTemplate = false, $isHasContent = false)
    {
        if ($masterTemplate) {
            //nicht optimal, wer was besseres weis - bitte
            if ($masterTemplate == 'application/views/master/default.tpl') {
                $componentId .= '-master';
            } else {
                return false;
            }
        }
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
}