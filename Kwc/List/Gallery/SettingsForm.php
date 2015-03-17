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
            ->setWidth(100)
            ->setShowNoSelection(true)
            ->setEmptyText(trlKwf('Show all'))
            ->setValues(array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '6' => '6',
                '8' => '8',
            ));
        $this->fields->add(new Kwf_Form_Field_Static(trlKwf('Choose a number to just show this number of items and hide the others behind a "more"-button')));
    }
}
