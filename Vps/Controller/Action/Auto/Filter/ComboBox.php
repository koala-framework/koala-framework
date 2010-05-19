<?php
class Vps_Controller_Action_Auto_Filter_ComboBox extends Vps_Controller_Action_Auto_Filter_Query
{
    protected function _init()
    {
        parent::_init();
        $this->_mandatoryProperties['data'] = null;
    }
}
