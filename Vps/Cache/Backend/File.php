<?php
require_once 'Zend/Cache/Backend/File.php';
class Vps_Cache_Backend_File extends Zend_Cache_Backend_File
{
    public function __construct(array $options = array())
    {
        if (!isset($options['cache_file_umask'])) {
            $options['cache_file_umask'] = 0666;
        }
        if (!isset($options['hashed_directory_umask'])) {
            $options['hashed_directory_umask'] = 0777;
        }
        parent::__construct($options);
    }
}
