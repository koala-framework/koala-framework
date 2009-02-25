<?php
class Vps_Component_Cache extends Zend_Cache_Core
{
    const CLEANING_MODE_DEFAULT = 'default';
    const CLEANING_MODE_COMPONENT_CLASS = 'componentClass';
    const CLEANING_MODE_ID = 'id';

    static private $_instance;
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

    public function saveMeta($meta)
    {
        $this->_backend->saveMeta($meta);
    }

    public function clean($mode = 'all', $tags = array(), $row = null)
    {
        if (in_array($mode, array( self::CLEANING_MODE_DEFAULT,
                                   self::CLEANING_MODE_COMPONENT_CLASS,
                                   self::CLEANING_MODE_ID))
        ) {
            if (!$this->_backend) return null;
            return $this->_backend->clean($mode, $tags, $row);
        } else {
            return parent::clean($mode, $tags);
        }
    }

    public function cleanComponentClass($componentClass)
    {
        $this->clean(self::CLEANING_MODE_COMPONENT_CLASS, $componentClass);
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
        return !empty($this->preloadedValues);
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