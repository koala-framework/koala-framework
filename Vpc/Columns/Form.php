<?php
class Vpc_Columns_Form_NoSaveData extends Vps_Data_Table
{
    public function save(Vps_Model_Row_Interface $row, $data)
    {
    }
}
class Vpc_Columns_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $mf = $this->add(new Vps_Form_Field_MultiFields('Columns'));
        $mf->setPosition(true);
        $fs = $mf->fields->add(new Vps_Form_Container_FieldSet(trlVps('Column {0}')));
            $fs->add(new Vps_Form_Field_TextField('width', trlVps('Width')))
                ->setAllowBlank(false);
            $fs->add(new Vps_Form_Field_SimpleAbstract('edit'))
                ->setXtype('vpc.columns.editbutton')
                ->setLabelSeparator('')
                ->setData(new Vpc_Columns_Form_NoSaveData('id'));
    }
}
