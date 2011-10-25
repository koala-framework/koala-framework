<?php
class Kwc_Columns_Controller extends Kwc_Abstract_List_ListEditButtonController
{
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->insertBefore('visible', new Kwf_Grid_Column('width', trlKwf('Width')))
            ->setEditor(new Kwf_Form_Field_TextField());
    }
}
