<?php
class Kwc_List_Gallery_SettingsForm extends Kwc_Abstract_Form
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
        $this->fields->add(new Kwf_Form_Field_Select('show_pics'))
            ->setFieldLabel(trlKwf('Visible Pictures'))
            ->setAllowBlank(true)
            ->setWidth(100)
            ->setValues(array(
                '0' => trl('Show all'),
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '6' => '6',
                '8' => '8',
            ));
        $this->fields->add(new Kwf_Form_Field_Static(trl('Choose a number to just show this number
                of items and hide the others behind a "more"-button')));
    }
}
