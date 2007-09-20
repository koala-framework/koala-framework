<?php
class Vps_Auto_Field_Tab extends Vps_Auto_Field_Container_Abstract
{
    public function getMetaData()
    {
        $ret = parent::getMetaData();
        if (!isset($ret['layout'])) $ret['layout'] = 'form';
        return $ret;
    }
}
