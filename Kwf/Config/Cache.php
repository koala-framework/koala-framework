<?php
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Frontend/File.php';
class Kwf_Config_Cache extends Zend_Cache_Frontend_File
{
    public function __construct()
    {
        $files = array(
            KWF_PATH . '/config.ini'
        );
        if (file_exists('config.ini')) $files[] = 'config.ini';
        if (file_exists('kwf_branch')) $files[] = 'kwf_branch';
        if (file_exists('config.local.ini')) $files[] = 'config.local.ini';
        parent::__construct(array(
            'master_files' => $files,
            'automatic_serialization' => true
        ));
        require_once 'Kwf/Cache/Backend/File.php';
        $this->setBackend(new Kwf_Cache_Backend_File(array(
            'cache_dir' => 'cache/config'
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
