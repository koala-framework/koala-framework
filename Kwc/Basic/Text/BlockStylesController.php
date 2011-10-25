<?php
class Kwc_Basic_Text_BlockStylesController extends Kwc_Basic_Text_InlineStylesController
{
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('tag', trlKwf('Tag'), 40));
    }

    protected function _formatSelectTag($select)
    {
        $select->whereNotEquals('tag', 'span');
    }
}
