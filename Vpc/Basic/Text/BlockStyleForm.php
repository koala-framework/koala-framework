<?php
class Vpc_Basic_Text_BlockStyleForm extends Vpc_Basic_Text_InlineStyleForm
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_NumberField('margin_top', trlVps('Margin top')))
            ->setAllowNegative(false)
            ->setAllowDecimals(false)
            ->setWidth(50);
        $this->add(new Vps_Form_Field_NumberField('margin_bottom', trlVps('Margin bottom')))
            ->setAllowNegative(false)
            ->setAllowDecimals(false)
            ->setWidth(50);
        $this->add(new Vps_Form_Field_Select('text_align', trlVps('Text Align')))
            ->setShowNoSelection(true)
            ->setValues(array(
                'left' => trlVps('Left'),
                'right' => trlVps('Right'),
                'center' => trlVps('Center'),
                'justify' => trlVps('Justify')
            ));
    }
}
