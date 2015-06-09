<?php
class Kwf_Config_Cache extends Zend_Cache_Core
{
    public function __construct()
    {
        parent::__construct(array(
            'automatic_serialization' => true,
            'lifetime' => null,
            'automatic_cleaning_factor' => 0,
        ));
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
