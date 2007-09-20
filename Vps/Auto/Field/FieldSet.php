<?php
class Vps_Auto_Field_FieldSet extends Vps_Auto_Field_Container_Abstract
{
    protected $_xtype = 'fieldset';

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        if (!isset($ret['autoHeight'])) $ret['autoHeight'] = true;
        return $ret;
    }
}
