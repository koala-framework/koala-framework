<?php
class Vpc_Columns_Form extends Vpc_Abstract_List_FormWithEditButton
{
    protected function _getMultiFieldsFieldset()
    {
        $fs = parent::_getMultiFieldsFieldset();
        $fs->setTitle(trlVps('Column {0}'));
        $fs->prepend(new Vps_Form_Field_TextField('width', trlVps('Width')))
            ->setAllowBlank(false);
        return $fs;
    }
}
