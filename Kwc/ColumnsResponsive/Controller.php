<?php
class Kwc_ColumnsResponsive_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_buttons = array();
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_model->setData($this->_getParam('class'), $this->_getParam('componentId'));

        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Name'), 200));
    }
}
