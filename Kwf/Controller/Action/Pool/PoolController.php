<?php
class Kwf_Controller_Action_Pool_PoolController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_position = 'pos';
    protected $_tableName = 'Kwf_Dao_Pool';

    protected function _getPool()
    {
        return $this->_getParam('pool');
    }

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where['pool = ?'] = $this->_getPool();
        return $where;
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('value', trlKwf('Value'), 300))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column_Visible());
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeInsert($row, $submitRow);
        $row->pool = $this->_getPool();
    }
}
