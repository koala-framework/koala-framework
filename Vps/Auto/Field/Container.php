<?php
class Vps_Auto_Field_Container extends Vps_Auto_Field_Container_Abstract
{
    public function getMetaData()
    {
        $ret = parent::getMetaData();
        if (!isset($ret['border'])) $ret['border'] = true;
        return $ret;
    }
}
