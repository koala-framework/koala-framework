<?php
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Frontend/File.php';
class Vps_Config_Cache extends Zend_Cache_Frontend_File
{
    public function __construct()
    {
        $files = array(
            'application/config.ini',
            VPS_PATH . '/config.ini'
        );
        if (file_exists('application/vps_branch')) $files[] = 'application/vps_branch';
        if (file_exists('application/config.local.ini')) $files[] = 'application/config.local.ini';
        parent::__construct(array(
            'master_files' => $files,
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
