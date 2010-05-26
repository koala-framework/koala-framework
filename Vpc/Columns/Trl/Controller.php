<?php
class Vpc_Columns_Trl_Controller extends Vpc_Abstract_List_Trl_Controller
{
    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('pos', trlVps('Column'), 60));
        $this->_columns->add(new Vps_Grid_Column_Button('edit'));
        $this->_columns->add(new Vps_Grid_Column_Visible());
    }
}
