<?php
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Core.php';
require_once 'Zend/Cache/Frontend/File.php';
class Vps_Config_Cache extends Zend_Cache_Frontend_File {
    
    private $_masterFile2_mtime = null;
    
    public function __construct()
    {
        parent::__construct(array(
            'master_file' => 'application/config.ini',
            'automatic_serialization' => true
        ));
        
        $this->_masterFile2_mtime = filemtime(VPS_PATH . '/config.ini');
        
        require_once 'Zend/Cache/Backend/File.php';
        $backend = new Zend_Cache_Backend_File(array(
            'cache_dir' => 'application/cache/config',
            'hashed_directory_umask' => 0777,
            'cache_file_umask' => 0666,
        ));
        
        $this->setBackend($backend);
    }
    
    public function test($id)
    {
        $lastModified = parent::test($id);
        if ($lastModified && $lastModified > $this->_masterFile2_mtime) {
            return $lastModified;
        }
        return false;
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