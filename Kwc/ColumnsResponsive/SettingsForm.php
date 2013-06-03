<?php
class Kwc_ColumnsResponsive_SettingsForm extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_Select('type', trlKwf('Columns type')))
            ->setShowNoSelection(true)
            ->setAllowBlank(false)
            ->setValues($this->_getColumnTypes());
    }

    protected function _getColumnTypes()
    {
        $columnTypes = Kwc_Abstract::getSetting($this->getClass(), 'columns');
        $ret = array();
        foreach ($columnTypes as $key => $value) {
            $ret[$key] = $value['name'];
        }
        return $ret;
    }
}
