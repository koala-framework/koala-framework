<?php
class Vps_Config_Cache extends Zend_Cache_Frontend_File {
    
    private $_masterFile2_mtime = null;
    
    public function __construct()
    {
        parent::__construct(array(
            'master_file' => 'application/config.ini',
            'automatic_serialization' => true
        ));
        
        $this->_masterFile2_mtime = filemtime(VPS_PATH . '/config.ini');        
        
        $backend = new Zend_Cache_Backend_File(array(
            'cache_dir' => 'application/cache/config',
            'hashed_directory_umask' => 0770
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
}