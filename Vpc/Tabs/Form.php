<?php
//Standardmäßig nicht verwendet
class Vpc_Tabs_Form extends Vpc_Abstract_List_FormWithEditButton
{
    protected function _getMultiFieldsFieldset()
    {
        $fs = parent::_getMultiFieldsFieldset();
        $fs->setTitle(trlVps('Tab {0}'));
        $fs->prepend(new Vps_Form_Field_TextField('title', trlVps('Title'), 200))
            ->setAllowBlank(false);
        return $fs;
    }
}
