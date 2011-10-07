<?php
class Vps_Util_Model_Amazon_Products_Row extends Vps_Model_Row_Item_Abstract
{
    public function __get($name)
    {
        $ret = parent::__get($name);
        if ($name == 'author' && is_array($ret)) {
            $ret = implode(', ', $ret);
        }
        return $ret;
    }
}
