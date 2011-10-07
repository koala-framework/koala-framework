<?php
class Kwc_Abstract_List_FormWithEditButton_NoSaveData extends Kwf_Data_Table
{
    public function save(Kwf_Model_Row_Interface $row, $data)
    {
    }
}

class Kwc_Abstract_List_FormWithEditButton extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $mf = $this->add(new Kwf_Form_Field_MultiFields('Children'));
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
        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Paragraph {0}'));
        $fs->add(new Kwf_Form_Field_Checkbox('visible', trlKwf('Visible')));
        $fs->add(new Kwf_Form_Field_SimpleAbstract('edit'))
            ->setXtype('kwc.listeditbutton')
            ->setLabelSeparator('')
            ->setData(new Kwc_Abstract_List_FormWithEditButton_NoSaveData('id'));
        return $fs;
    }
}
