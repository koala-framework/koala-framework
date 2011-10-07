<?php
class Kwf_Controller_Action_Auto_Filter_TextField extends Kwf_Controller_Action_Auto_Filter_Query
{
    protected $_type = 'Text';

    protected function _init()
    {
        parent::_init();
        $this->setSelectType(Kwf_Controller_Action_Auto_Filter_Query::SELECT_TYPE_CONTAINS);
    }
}
