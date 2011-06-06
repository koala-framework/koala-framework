<?php
class Vps_Cache extends Zend_Cache
{
    private static $_instances = array();

    /**
     * @return Zend_Cache_Core
     */
    public static function factory($frontend, $backend, $frontendOptions = array(), $backendOptions = array())
    {
        $frontendClass = 'Zend_Cache_' . ($frontend != 'Core' ? 'Frontend_' : '') . $frontend;
        $backendClass = 'Zend_Cache_Backend_' . $backend;
        if ($backend == 'Memcached' || $backend == 'File' || $backend == 'TwoLevels' || $backend == 'Apc') {
            $backendClass = 'Vps_Cache_Backend_' . $backend;
        }
        $frontendObject = new $frontendClass($frontendOptions);
        $backendObject = new $backendClass($backendOptions);
        $frontendObject->setBackend($backendObject);
        return $frontendObject;
    }

    /**
     * @param string entweder aus config (caches.type)
     * @return Vps_Cache_Core
     *
     * ähnlich wie Vps_Model_Abstract::getInstance()
     */
    public static function getInstance($type)
    {
        if (!isset(self::$_instances[$type])) {
            static $config;
            if (!isset($config)) $config = Vps_Registry::get('config')->caches->toArray();
            if (!isset($config[$type])) {
                throw new Vps_Exception("unknown type '$type'");
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
        static $ret;
        if (!isset($ret)) {
            $ret = Zend_Registry::get('config')->application->id.'-'.Vps_Setup::getConfigSection().'-';
        }
        return $ret;
    }
}
