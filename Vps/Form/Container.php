<?php
class Vps_Form_Container extends Vps_Form_Container_Abstract
{
    public function getMetaData()
    {
        $ret = parent::getMetaData();
        if (!isset($ret['border'])) $ret['border'] = false;
        return $ret;
    }
}
