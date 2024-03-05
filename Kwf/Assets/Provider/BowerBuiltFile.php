<?php
class Kwf_Assets_Provider_BowerBuiltFile extends Kwf_Assets_Provider_Abstract
{
    private $_path;
    public function __construct($path)
    {
        $path = substr($path, strlen(VENDOR_PATH.'/bower_components/'));
        $this->_path = $path;
    }

    public function getPathTypes()
    {
        $ret = array();
        foreach (glob(VENDOR_PATH.'/bower_components/*') as $i) {
            $type = substr($i, strlen(VENDOR_PATH.'/bower_components/'));
            if (substr($type, -3) == '.js') $type = substr($type, 0, -3);
            if (substr($type, -2) == 'js') {
                $ret[$type] = $i;
                $type = substr($type, 0, -2);
            }
            $ret[$type] = $i;
        }
        return $ret;
    }
}
