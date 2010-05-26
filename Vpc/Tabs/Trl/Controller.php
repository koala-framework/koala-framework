<?php
class Vpc_Tabs_Trl_Controller extends Vpc_Abstract_List_Trl_Controller
{
    protected $_model = 'Vpc_Tabs_Trl_AdminModel';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('pos', trlVps('Tab'), 50));
        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 100))
            ->setEditor(new Vps_Form_Field_TextField());
        $this->_columns->add(new Vps_Grid_Column_Button('edit'));
        $this->_columns->add(new Vps_Grid_Column_Visible());
    }
}
