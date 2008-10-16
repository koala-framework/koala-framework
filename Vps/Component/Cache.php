<?php
class Vps_Component_Cache extends Zend_Cache_Core
{
    const CLEANING_MODE_COMPONENT_CLASS = 'componentClass';
    const CLEANING_MODE_ID_PATTERN = 'idPattern';
    const CLEANING_MODE_SELECT = 'select';
    
    static private $_instance;
    private $_backend;
    private $_process = array(
        'insert' => array(), 
        'update' => array(), 
        'delete' => array()
    );
    private $_processed = false;
    private $_preloadedValues = array();
    
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
        if (!$this->_processed && $this->_process != array(
            'insert' => array(), 
            'update' => array(), 
            'delete' => array()
        )) {
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
    
    public function removeByIdPattern($idPattern, $componentClass = null)
    {
        $this->_backend->clean(self::CLEANING_MODE_ID_PATTERN, 
            array('idPattern' => $idPattern, 'componentClass' => $componentClass)
        );
        if ($componentClass) {
            Vps_Benchmark::info("Cache für Pattern '$idPattern' mit Klasse '$componentClass' gelöscht.");
        } else {
            Vps_Benchmark::info("Cache für Pattern '$idPattern' gelöscht.");
        }
    }

    public function removeBySelect(Zend_Db_Table_Select $select)
    {
        $this->_backend->clean(self::CLEANING_MODE_SELECT, $select);
        Vps_Benchmark::info("Cache mit Select gelöscht.");
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
        $this->_backend->clean(self::CLEANING_MODE_COMPONENT_CLASS, $componentClass);
        Vps_Benchmark::info("Kompletter Cache für Komponente '$componentClass' gelöscht.");
    }
    
    public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        if ($this->isLoaded($id)) {
            return $this->_preloadedValues[$id];
        }
        
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
    
    public function emptyPreload()
    {
        $this->_preloadedValues = array();
    }
    
    public function preload($ids)
    {
        $this->_preloadedValues = array_merge(
            $this->_preloadedValues, $this->_preload($ids)
        );
    }
    
    protected function _preload($ids)
    {
        $parts = array();
        $values = array();
        foreach ($ids as $key => $val) {
            if ($key) {
                $parts[] = "(id LIKE '{$key}%' AND page_id='$val')";
                $values[$key] = null;
            } else {
                $parts[] = "page_id='$val'";
                $values[$val] = null;
            }
        }
        if ($parts) {
            $sql = "SELECT id, content FROM cache_component WHERE " . implode(' OR ', $parts);
            Vps_Benchmark::count('preload cache', $sql);
            $rows = Zend_Registry::get('db')->query($sql)->fetchAll();
            foreach ($rows as $row) {
                $values[$row['id']] = $row['content'];
            }
        }
        return $values;
    }
    
    public function shouldBeLoaded($cacheId)
    {
        $cacheId = (string)$cacheId;
        if (isset($this->_preloadedValues[$cacheId])) {
            return true;
        }
        $cutId = $cacheId;
        while ($cutId) {
            $pos = strrpos($cutId, '-');
            $cutId = $pos ? substr($cutId, 0, $pos) : '';
            if (in_array($cutId, array_keys($this->_preloadedValues)))  {
                return true;
            }
        }
        return false;
    }
    
    public function isLoaded($cacheId)
    {
        return 
            isset($this->_preloadedValues[$cacheId]) &&
            !is_null($this->_preloadedValues[$cacheId]);
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