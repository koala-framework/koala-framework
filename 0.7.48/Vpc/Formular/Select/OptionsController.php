<?php
class Vpc_Formular_Select_OptionsController extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_tableName = 'Vpc_Formular_Select_OptionsModel';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column('text', trlVps('Description'), 200))
            ->setEditor(new Vps_Auto_Field_TextField());
        $this->_columns->add(new Vps_Auto_Grid_Column('checked', trlVps('Checked'), 60))
            ->setEditor(new Vps_Auto_Field_Checkbox());
    }
}
