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
        $where = parent::_getWhere();
        unset($where[array_search("tag = 'span'", $where)]);
        $where[] = "tag != 'span'";
        return $where;
    }
}
