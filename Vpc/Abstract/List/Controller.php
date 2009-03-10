<?php
class Vpc_Abstract_List_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_position = 'pos';
    protected $_buttons = array('save', 'add', 'delete');

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column_Visible());
    }

    protected function _beforeInsert($row)
    {
        if (is_null($row->visible)) $row->visible = 0;
    }
}
