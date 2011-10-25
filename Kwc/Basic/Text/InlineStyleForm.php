<?php
class Kwc_Basic_Text_InlineStyleForm extends Kwf_Form
{
    protected function _init()
    {
        parent::_init();

        $this->add(new Kwf_Form_Field_NumberField('pos', trlKwf('Position')))
            ->setAllowDecimals(false)
            ->setAllowNegative(false)
            ->setWidth(50);

        $this->add(new Kwf_Form_Field_TextField('name', trlKwf('Name')))
            ->setAllowBlank(false);

        $this->add(new Kwf_Form_Field_ColorField('color', trlKwf('Color')))
            ->setDefaultValue('000000');
        $this->add(new Kwf_Form_Field_Select('font_weight', trlKwf('Font Weight')))
            ->setShowNoSelection(true)
            ->setValues(array(
                'normal' => trlKwf('Normal'),
                'bold' => trlKwf('Bold'),
                'bolder' => trlKwf('Bolder'),
                'lighter' => trlKwf('Lighter')
            ));
        $this->add(new Kwf_Form_Field_Select('font_family', trlKwf('Font Family')))
            ->setShowNoSelection(true)
            ->setValues(array(
                'Arial, Verdana, Helvetica, sans-serif' => 'Arial',
                '"Courier New", Courier, monospace' => 'Courier New',
                'Times, "Times New Roman", Georgia, serif' => 'Times New Roman',
                'Verdana, Arial, Helvetica, sans-serif' => 'Verdana'
            ));
        $this->add(new Kwf_Form_Field_NumberField('font_size', trlKwf('Font size')))
            ->setAllowNegative(false)
            ->setAllowDecimals(false)
            ->setWidth(50);
        $this->add(new Kwf_Form_Field_TextArea('additional', trlKwf('Additional Styles')))
            ->setHeight(50)
            ->setWidth(200);
    }
}
