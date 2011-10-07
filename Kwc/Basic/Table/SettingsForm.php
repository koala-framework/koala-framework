<?php
class Kwc_Basic_Table_SettingsForm extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->setCreateMissingRow(true);

        $tableStyles = Kwc_Abstract::getSetting($this->getClass(), 'tableStyles');
        if (count($tableStyles)) {
            $this->add(new Kwf_Form_Field_Select('table_style', trlKwf('Table style')))
                ->setShowNoSelection(true)
                ->setValues($tableStyles);
        }
    }
}
