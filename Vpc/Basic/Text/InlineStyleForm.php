<?php
class Vpc_Basic_Text_InlineStyleForm extends Vps_Form
{
    protected function _init()
    {
        parent::_init();

        $this->add(new Vps_Form_Field_NumberField('pos', trlVps('Position')))
            ->setAllowDecimals(false)
            ->setAllowNegative(false)
            ->setWidth(50);

        $this->add(new Vps_Form_Field_TextField('name', trlVps('Name')))
            ->setAllowBlank(false);

        $this->add(new Vps_Form_Field_ColorField('color', trlVps('Color')))
            ->setDefaultValue('000000');
        $this->add(new Vps_Form_Field_Select('font_weight', trlVps('Font Weight')))
            ->setShowNoSelection(true)
            ->setValues(array(
                'normal' => trlVps('Normal'),
                'bold' => trlVps('Bold'),
                'bolder' => trlVps('Bolder'),
                'lighter' => trlVps('Lighter')
            ));
        $this->add(new Vps_Form_Field_Select('font_family', trlVps('Font Family')))
            ->setShowNoSelection(true)
            ->setValues(array(
                'Arial, Verdana, Helvetica, sans-serif' => 'Arial',
                '"Courier New", Courier, monospace' => 'Courier New',
                'Times, "Times New Roman", Georgia, serif' => 'Times New Roman',
                'Verdana, Arial, Helvetica, sans-serif' => 'Verdana'
            ));
        $this->add(new Vps_Form_Field_NumberField('font_size', trlVps('Font size')))
            ->setAllowNegative(false)
            ->setAllowDecimals(false)
            ->setWidth(50);
        $this->add(new Vps_Form_Field_TextArea('additional', trlVps('Additional Styles')))
            ->setHeight(50)
            ->setWidth(200);
    }
}
