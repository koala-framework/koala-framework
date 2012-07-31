<?php
class Kwf_Controller_Action_Pool_PoolviewController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_position = false;
    protected $_sortable = false;
    protected $_defaultOrder = 'pos';
    protected $_model = 'Kwf_Util_Model_Pool';

    protected function _getPool()
    {
        return $this->_getParam('pool');
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('pool', $this->_getPool());
        $ret->whereEquals('visible', 1);
        return $ret;
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('pos'));
        $this->_columns->add(new Kwf_Grid_Column('value', 'Wert', 300));
    }
}
