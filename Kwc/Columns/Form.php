<?php
//Standardmäßig nicht verwendet
class Kwc_Columns_Form extends Kwc_Abstract_List_FormWithEditButton
{
    protected function _getMultiFieldsFieldset()
    {
        $fs = parent::_getMultiFieldsFieldset();
        $fs->setTitle(trlKwf('Column {0}'));
        $fs->prepend(new Kwf_Form_Field_TextField('width', trlKwf('Width')))
            ->setAllowBlank(false);
        return $fs;
    }
}
