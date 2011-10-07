<?php
class Vpc_Tabs_Controller extends Vpc_Abstract_List_ListEditButtonController
{
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->insertBefore('visible', new Vps_Grid_Column('title', trlVps('Title'), 200))
            ->setEditor(new Vps_Form_Field_TextField());
    }
}
