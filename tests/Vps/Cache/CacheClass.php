<?php
class Vps_Cache_CacheClass extends Vps_Cache_Core
{
    private $_cacheDir = null;

    public function __construct(array $options = array())
    {
        $options['checkComponentSettings'] = false;
        $options['lifetime'] = null;
        $options['automatic_serialization'] = true;
        parent::__construct($options);

        $this->_cacheDir = tempnam('/tmp', 'cachetest');
        unlink($this->_cacheDir);
        mkdir($this->_cacheDir);

        $backend = new Zend_Cache_Backend_File(array(
            'cache_dir' => $this->_cacheDir
        ));
        $this->setBackend($backend);
    }

    public function cleanUp()
    {
        if (substr($this->_cacheDir, 0, 4)=='/tmp') {
            system('rm -r '.$this->_cacheDir);
        }
    }
}
