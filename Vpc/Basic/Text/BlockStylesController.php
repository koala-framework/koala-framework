<?php
class Vpc_Basic_Text_BlockStylesController extends Vpc_Basic_Text_InlineStylesController
{
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('tag', trlVps('Tag'), 40));
    }

    protected function _formatSelectTag($select)
    {
        $select->whereNotEquals('tag', 'span');
    }
}
