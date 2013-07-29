<?php
class Kwf_Cache extends Zend_Cache
{
    private static $_instances = array();

    /**
     * @return Zend_Cache_Core
     */
    public static function factory($frontend, $backend, $frontendOptions = array(), $backendOptions = array(), $customFrontendNaming = false, $customBackendNaming = false, $autoload = false)
    {
        //parameters $customFrontendNaming, $customBackendNaming, $autoload are not used at the moment
        $frontendClass = 'Zend_Cache_' . ($frontend != 'Core' ? 'Frontend_' : '') . $frontend;
        $backendClass = 'Zend_Cache_Backend_' . $backend;
        if ($backend == 'Memcached' || $backend == 'File' || $backend == 'TwoLevels' || $backend == 'Apc') {
            $backendClass = 'Kwf_Cache_Backend_' . $backend;
        }
        $frontendObject = new $frontendClass($frontendOptions);
        $backendObject = new $backendClass($backendOptions);
        $frontendObject->setBackend($backendObject);
        return $frontendObject;
    }

    /**
     * @param string entweder aus config (caches.type)
     * @return Kwf_Cache_Core
     *
     * ähnlich wie Kwf_Model_Abstract::getInstance()
     */
    public static function getInstance($type)
    {
        if (!isset(self::$_instances[$type])) {
            static $config;
            if (!isset($config)) $config = Kwf_Registry::get('config')->caches->toArray();
            if (!isset($config[$type])) {
                throw new Kwf_Exception("unknown type '$type'");
            }
            $cls = $config[$type];
            self::$_instances[$type] = new $cls();
        }
        return self::$_instances[$type];
    }

    public static function clearInstance($type)
    {
        if (isset(self::$_instances[$type])) {
            unset(self::$_instances[$type]);
        }
    }

    /**
     * Hilfsfunktion die einen eindeutigen Prefix zurückgibt
     */
    public static function getUniquePrefix()
    {
        return Kwf_Cache_Simple::getUniquePrefix();
    }
}
