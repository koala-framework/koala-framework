<?php
class Kwf_Assets_Cache extends Kwf_Cache_Core
{
    public function __construct(array $options = array())
    {
        if (!isset($options['lifetime'])) $options['lifetime'] = null;
        if (!isset($options['automatic_serialization'])) $options['automatic_serialization'] = true;
        if (!isset($options['automatic_cleaning_factor'])) $options['automatic_cleaning_factor'] = 0;
        if (!isset($options['write_control'])) $options['write_control'] = false;
        parent::__construct($options);
        $backend = new Zend_Cache_Backend_File(array(
            'cache_dir' => 'cache/assets',
            'cache_file_umask' => 0666,
            'hashed_directory_umask' => 0777,
            'hashed_directory_level' => 2,
        ));
        if (extension_loaded('apc')) {
            $backend = new Kwf_Cache_Backend_TwoLevels(array(
                'slow_backend' => $backend,
                'fast_backend' => new Kwf_Cache_Backend_Apc()
            ));
        }
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
