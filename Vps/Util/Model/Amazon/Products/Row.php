<?php
class Vps_Util_Model_Amazon_Products_Row extends Vps_Model_Row_Item_Abstract
{
    protected function _transformColumnName($name)
    {
        $name = strtoupper(substr($name, 0, 1)).substr($name, 1);
        if ($name == 'Asin') $name = 'ASIN';
        return $name;
    }

    public function __get($name)
    {
        $ret = parent::__get($name);
        if ($name == 'author' && is_array($ret)) {
            $ret = implode(', ', $ret);
        }
        return $ret;
    }
}
