<?php
abstract class Kwf_Component_Cache
{
    static private $_instance;
    const NO_CACHE = '{nocache}';

    /**
     * @return Kwf_Component_Cache_Mysql
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            if (Kwf_Cache_Simple::getBackend() == 'redis') {
                self::$_instance = new Kwf_Component_Cache_Redis;
            } else {
                self::$_instance = new Kwf_Component_Cache_Mysql;
            }
        }
        return self::$_instance;
    }

    public static function setInstance($backend)
    {
        self::clearInstance();
        self::$_backend = $backend;
    }

    public static function clearInstance()
    {
        self::$_instance = null;
    }

    public abstract function save(Kwf_Component_Data $component, $content, $renderer, $type, $value, $tag, $lifetime);
    public abstract function loadWithMetadata($componentId, $renderer='component', $type = 'component', $value = '');
    public abstract function load($componentId, $renderer='component', $type = 'component', $value = '');
    public abstract function countViewCacheEntries($updates);
    public abstract function deleteViewCache(array $updates);
    public abstract function handlePageParentChanges(array $pageParentChanges);
    public abstract function saveIncludes($componentId, $type, $includedComponents);

    public function collectGarbage($debug)
    {
    }

    public function writeBuffer()
    {
    }
}
