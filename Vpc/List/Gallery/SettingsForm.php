<?php
class Vpc_List_Gallery_SettingsForm extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->fields->prepend(new Vps_Form_Field_NumberField('columns'))
            ->setFieldLabel(trlVps('Columns'))
            ->setAllowNegative(false)
            ->setAllowDecimals(false)
            ->setMinValue(1)
            ->setMaxValue(10)
            ->setAllowBlank(false)
            ->setWidth(50);
    }
}
