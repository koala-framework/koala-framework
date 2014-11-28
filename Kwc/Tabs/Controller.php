<?php
class Kwc_Tabs_Controller extends Kwc_Abstract_List_ListEditButtonController
{
    protected $_showChildComponentGridColumns = false;

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->insertBefore('visible', new Kwf_Grid_Column('title', trlKwf('Title'), 200))
            ->setEditor(new Kwf_Form_Field_TextField());
    }
}
