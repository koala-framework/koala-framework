<?php
class Vpc_Basic_Text_InlineStylesController extends Vps_Controller_Action_Auto_Grid
{
    protected $_position = 'pos';
    protected $_tableName = 'Vpc_Basic_Text_StylesModel';

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('name', 'Name', 100));
    }

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where[] = "tag = 'span'";
        return $where;
    }
}
