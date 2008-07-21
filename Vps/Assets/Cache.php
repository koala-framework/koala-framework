<?php
class Vps_Assets_Cache extends Zend_Cache_Core
{
    public function __construct()
    {
        parent::__construct(array(
            'lifetime' => null,
            'automatic_serialization' => true
        ));
        $backend = new Zend_Cache_Backend_File(array(
            'cache_dir' => 'application/cache/assets'
        ));
        $this->setBackend($backend);
    }
}
