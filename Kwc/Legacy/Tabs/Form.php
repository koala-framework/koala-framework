<?php
//Standardmäßig nicht verwendet
class Kwc_Legacy_Tabs_Form extends Kwc_Abstract_List_FormWithEditButton
{
    protected function _getMultiFieldsFieldset()
    {
        $fs = parent::_getMultiFieldsFieldset();
        $fs->setTitle(trlKwf('Tab {0}'));
        $fs->prepend(new Kwf_Form_Field_TextField('title', trlKwf('Title'), 200))
            ->setAllowBlank(false);
        return $fs;
    }
}
