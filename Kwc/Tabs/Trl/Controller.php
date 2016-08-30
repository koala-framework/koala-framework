<?php
class Kwc_Tabs_Trl_Controller extends Kwc_Abstract_List_Trl_Controller
{
    protected $_model = 'Kwc_Tabs_Trl_AdminModel';

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('pos', trlKwf('Tab'), 50));
        $this->_columns->add(new Kwf_Grid_Column_Visible());
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 100))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
    }
}
