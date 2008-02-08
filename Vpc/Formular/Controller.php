<?php
class Vpc_Formular_Controller extends Vpc_Paragraphs_Controller
{
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Auto_Grid_Column('field_label', 'Field Label', 200))
            ->setEditor(new Vps_Auto_Field_TextField());
        $this->_columns->add(new Vps_Auto_Grid_Column_Checkbox('mandatory', 'Mandatory', 50))
            ->setEditor(new Vps_Auto_Field_Checkbox());
        $this->_columns->add(new Vps_Auto_Grid_Column_Checkbox('no_cols', 'Row', 50))
            ->setEditor(new Vps_Auto_Field_Checkbox());
    }
}
