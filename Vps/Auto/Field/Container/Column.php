<?php
class Vps_Auto_Field_Column extends Vps_Auto_Field_Container_Abstract
{
    public function getMetaData()
    {
        $ret = parent::getMetaData();
        if (!isset($ret['layout'])) $ret['layout'] = 'form';
        if (!isset($ret['border'])) $ret['border'] = false;
        return $ret;
    }
}
