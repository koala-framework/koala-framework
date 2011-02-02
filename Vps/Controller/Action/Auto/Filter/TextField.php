<?php
class Vps_Controller_Action_Auto_Filter_TextField extends Vps_Controller_Action_Auto_Filter_Query
{
    protected $_type = 'Text';

    protected function _init()
    {
        parent::_init();
        $this->setSelectType(Vps_Controller_Action_Auto_Filter_Query::SELECT_TYPE_CONTAINS);
    }
}
