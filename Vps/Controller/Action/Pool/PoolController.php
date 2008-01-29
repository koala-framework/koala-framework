<?php
class Vps_Controller_Action_Pool_PoolController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_position = 'pos';
    protected $_tableName = 'Vps_Dao_Pool';

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where['pool = ?'] = $this->_getParam('pool');
        return $where;
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column('value', 'Wert', 300))
            ->setEditor(new Vps_Auto_Field_TextField());
        $this->_columns->add(new Vps_Auto_Grid_Column_Visible());
    }

    protected function _beforeInsert(Zend_Db_Table_Row_Abstract $row, $submitRow)
    {
        parent::_beforeInsert($row, $submitRow);
        $row->pool = $this->_getParam('pool');
    }
}
