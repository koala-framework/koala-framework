<?php
class Vps_Assets_Cache extends Vps_Cache_Core
{
    public function __construct(array $options = array())
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
