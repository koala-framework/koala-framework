<?php
class Vpc_Formular_Controller extends Vpc_Paragraphs_Controller
{
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('field_label', trlVps('Field Label'), 200))
            ->setEditor(new Vps_Form_Field_TextField());
        $this->_columns->add(new Vps_Grid_Column_Checkbox('mandatory', trlVps('Mandatory'), 50))
            ->setEditor(new Vps_Form_Field_Checkbox());
        $this->_columns->add(new Vps_Grid_Column_Checkbox('no_cols', trlVps('Row'), 50))
            ->setEditor(new Vps_Form_Field_Checkbox());
    }
}
