<?php
class Vps_Component_Cache extends Zend_Cache_Core
{
    const CLEANING_MODE_COMPONENT_CLASS = 'componentClass';
    const CLEANING_MODE_ID_PATTERN = 'idPattern';
    const CLEANING_MODE_SELECT = 'select';

    static private $_instance;
    private $_backend;
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

    //für unit testing
    public static function setInstance($instance)
    {
        self::$_instance = $instance;
    }
    /**
     * @return Vps_Component_Cache
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
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
        $this->_preloadedValues = $this->_preloadedValues + $this->_preload($ids);
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
                $values[(string)$val] = null;
            }
        }
        if ($parts) {
            $sql = "SELECT id, content, expire FROM cache_component WHERE " . implode(' OR ', $parts);
            Vps_Benchmark::count('preload cache', $sql);
            $rows = Zend_Registry::get('db')->query($sql)->fetchAll();
            foreach ($rows as $row) {
                if ($row['expire'] == 0 || $row['expire'] > time()) {
                    $values[(string)$row['id']] = $row['content'];
                }
            }
        }
        return $values;
    }

    public function shouldBeLoaded($cacheId)
    {
        $cacheId = (string)$cacheId;
        if (array_key_exists($cacheId, $this->_preloadedValues)) {
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
        $cacheId = (string)$cacheId;
        return isset($this->_preloadedValues[$cacheId]);
    }

    public function getCacheIdFromComponentId($componentId, $masterTemplate = false, $isHasContent = false, $partialNumber = null)
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
        if (!is_null($partialNumber)) { $componentId .= '~' . $partialNumber; }
        return str_replace('~', '___', str_replace('-', '__', $componentId));
    }

    public function getComponentIdFromCacheId($cacheId)
    {
        if (substr($cacheId, -8) == '__master') {
            return null;
        } else {
            return str_replace('___', '~', str_replace('__', '-', $cacheId));
        }
    }
}