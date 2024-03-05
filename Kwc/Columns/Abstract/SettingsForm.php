<?php
class Kwc_Columns_Abstract_SettingsForm extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $types = $this->_getColumnTypes();
        $typeKeys = array_keys($types);

        $this->add(new Kwf_Form_Field_Select('type', trlKwf('Columns type')))
            ->setAllowBlank(false)
            ->setDefaultValue(array_shift($typeKeys))
            ->setValues($types);
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
