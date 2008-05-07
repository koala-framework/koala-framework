<?php
class Vpc_Basic_Text_BlockStylesController extends Vpc_Basic_Text_InlineStylesController
{
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('tag', 'Tag', 40));
    }

    protected function _getWhere()
    {
        $where = Vps_Controller_Action_Auto_Grid::_getWhere();
        $where[] = "tag != 'span'";
        return $where;
    }
}
