<?php
class Vps_Cache extends Zend_Cache
{
    /**
     * @return Zend_Cache_Core
     */
    public static function factory($frontend, $backend, $frontendOptions = array(), $backendOptions = array())
    {
        $frontendClass = 'Zend_Cache_' . ($frontend != 'Core' ? 'Frontend_' : '') . $frontend;
        $backendClass = 'Zend_Cache_Backend_' . $backend;
        if ($backend == 'Memcached' || $backend == 'File' || $backend == 'TwoLevels') {
            $backendClass = 'Vps_Cache_Backend_' . $backend;
        }
        $frontendObject = new $frontendClass($frontendOptions);
        $backendObject = new $backendClass($backendOptions);
        $frontendObject->setBackend($backendObject);
        return $frontendObject;
    }
}
