<?php
class Kwc_Abstract_List_Columns_SettingsForm extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->fields->prepend(new Kwf_Form_Field_NumberField('columns'))
            ->setFieldLabel(trlKwf('Columns'))
            ->setAllowNegative(false)
            ->setAllowDecimals(false)
            ->setMinValue(1)
            ->setMaxValue(10)
            ->setAllowBlank(false)
            ->setWidth(50);
    }
}
