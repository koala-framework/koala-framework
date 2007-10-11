<?php
class Vps_Auto_Container_FieldSet extends Vps_Auto_Container_Abstract
{
    protected $_xtype = 'fieldset';

    public function __construct($title = null)
    {
        parent::__construct();
        $this->setTitle($title);
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        if (!isset($ret['autoHeight'])) $ret['autoHeight'] = true;
        return $ret;
    }
}
