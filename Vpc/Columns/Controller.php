<?php
class Vpc_Columns_Controller extends Vpc_Abstract_List_ListEditButtonController
{
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->insertBefore('visible', new Vps_Grid_Column('width', trlVps('Width')))
            ->setEditor(new Vps_Form_Field_TextField());
    }
}
