<?php
class Kwc_Columns_Trl_Controller extends Kwc_Abstract_List_Trl_Controller
{
    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('pos', trlKwf('Column'), 60));
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
    }
}
