<?php
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Frontend/File.php';
class Vps_Config_Cache extends Zend_Cache_Frontend_File
{
    public function __construct()
    {
        parent::__construct(array(
            'master_files' => array(
                'application/config.ini',
                VPS_PATH . '/config.ini'
            ),
            'automatic_serialization' => true
        ));
        require_once 'Vps/Cache/Backend/File.php';
        $this->setBackend(new Vps_Cache_Backend_File(array(
            'cache_dir' => 'application/cache/config'
        )));
    }


    public static function getInstance()
    {
        static $cache;
        if (!isset($cache)) {
            $cache = new self();
        }
        return $cache;
    }
}