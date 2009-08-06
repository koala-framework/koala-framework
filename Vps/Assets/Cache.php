<?php
class Vps_Assets_Cache extends Vps_Cache_Core
{
    public function __construct(array $options = array())
    {
        if (!isset($options['lifetime'])) $options['lifetime'] = null;
        if (!isset($options['automatic_serialization'])) $options['automatic_serialization'] = true;
        parent::__construct($options);
        $backend = new Zend_Cache_Backend_File(array(
            'cache_dir' => 'application/cache/assets',
            'cache_file_umask' => 0666,
            'hashed_directory_umask' => 0777,
        ));
        $this->setBackend($backend);
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
