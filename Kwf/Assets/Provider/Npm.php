<?php
class Kwf_Assets_Provider_Npm extends Kwf_Assets_Provider_Abstract
{
    private $_path;
    public function __construct($path)
    {
        $path = substr($path, strlen('node_modules/'));
        $this->_path = $path;
    }

    public function getPathTypes()
    {
        $ret = array();
        foreach (glob('node_modules/*') as $i) {
            $type = substr($i, strlen('node_modules/'));
            $ret[$type] = $i;
        }
        return $ret;
    }
}

