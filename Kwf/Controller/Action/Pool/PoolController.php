<?php
class Kwf_Controller_Action_Pool_PoolController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_position = 'pos';
    protected $_model = 'Kwf_Util_Model_Pool';

    protected function _getPool()
    {
        return $this->_getParam('pool');
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('pool', $this->_getPool());
        return $ret;
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Visible());
        $this->_columns->add(new Kwf_Grid_Column('value', trlKwf('Value'), 300))
            ->setEditor(new Kwf_Form_Field_TextField());
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeInsert($row, $submitRow);
        $row->pool = $this->_getPool();
    }
}
