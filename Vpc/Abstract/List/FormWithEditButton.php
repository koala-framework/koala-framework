<?php
class Vpc_Abstract_List_FormWithEditButton_NoSaveData extends Vps_Data_Table
{
    public function save(Vps_Model_Row_Interface $row, $data)
    {
    }
}

class Vpc_Abstract_List_FormWithEditButton extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $mf = $this->add(new Vps_Form_Field_MultiFields('Children'));
        $mf->setWidth(400);
        $mf->setPosition(true);
        $mf->setMinEntries(0);

        $mf->fields->add($this->_getMultiFieldsFieldset());
    }

    /**
     * MultiFields content fields.
     *
     * @return array $fields The fields that should be inserted.
     */
    protected function _getMultiFieldsFieldset()
    {
        $fs = new Vps_Form_Container_FieldSet(trlVps('Paragraph {0}'));
        $fs->add(new Vps_Form_Field_Checkbox('visible', trlVps('Visible')));
        $fs->add(new Vps_Form_Field_SimpleAbstract('edit'))
            ->setXtype('vpc.listeditbutton')
            ->setLabelSeparator('')
            ->setData(new Vpc_Abstract_List_FormWithEditButton_NoSaveData('id'));
        return $fs;
    }
}
