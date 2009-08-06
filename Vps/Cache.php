<?php
class Vps_Cache extends Zend_Cache
{
    public static function factory($frontend, $backend, $frontendOptions = array(), $backendOptions = array())
    {
        $frontendClass = 'Zend_Cache_' . ($frontend != 'Core' ? 'Frontend_' : '') . $frontend;
        $backendClass = 'Zend_Cache_Backend_' . $backend;
        if ($backend == 'Memcached') {
            $backendOptions['servers'] = array(array(
                'host' => Vps_Registry::get('config')->server->memcache->host,
                'port' => Vps_Registry::get('config')->server->memcache->port
            ));
	    $backendOptions['compatibility'] = true;
            $backendClass = 'Vps_Cache_Backend_' . $backend;
        }
        if ($backend == 'File') {
            $backendOptions['cache_file_umask'] = 0666;
            $backendOptions['hashed_directory_umask'] = 0777;
        }
        $frontendObject = new $frontendClass($frontendOptions);
        $backendObject = new $backendClass($backendOptions);
        $frontendObject->setBackend($backendObject);
        return $frontendObject;
    }
}
