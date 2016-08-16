<?php
abstract class Kwf_Component_Cache
{
    static private $_instance;
    static private $_backend = self::CACHE_BACKEND_MYSQL;
    const CACHE_BACKEND_MYSQL = 'Kwf_Component_Cache_Mysql';
    const CACHE_BACKEND_FNF = 'Kwf_Component_Cache_Fnf';
    const NO_CACHE = '{nocache}';

    /**
     * @return Kwf_Component_Cache_Mysql
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            $backend = self::$_backend;
            self::$_instance = new $backend();
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
    public abstract function deleteViewCache(array $updates);
    public abstract function handlePageParentChanges(array $pageParentChanges);
    public abstract function saveIncludes($componentId, $type, $includedComponents);

    public function writeBuffer()
    {
    }
}
