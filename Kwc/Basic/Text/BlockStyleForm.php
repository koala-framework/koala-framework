<?php
class Kwc_Basic_Text_BlockStyleForm extends Kwc_Basic_Text_InlineStyleForm
{
    protected function _init()
    {
        parent::_init();
        $tag = $this->fields->insertAfter('name', new Kwf_Form_Field_Select('tag', trlKwf('Tag')))
            ->setValues(array(
                'p'    => trlKwf('Normal (p)'),
                'h1'   => trlKwf('Headline 1 (h1)'),
                'h2'   => trlKwf('Headline 2 (h2)'),
                'h3'   => trlKwf('Headline 3 (h3)'),
                'h4'   => trlKwf('Headline 4 (h4)'),
                'h5'   => trlKwf('Headline 5 (h5)'),
                'h6'   => trlKwf('Headline 6 (h6)')
             ))
            ->setAllowBlank(false)
            ->setDefaultValue('p');

        $this->add(new Kwf_Form_Field_NumberField('margin_top', trlKwf('Margin top')))
            ->setAllowNegative(false)
            ->setAllowDecimals(false)
            ->setWidth(50);
        $this->add(new Kwf_Form_Field_NumberField('margin_bottom', trlKwf('Margin bottom')))
            ->setAllowNegative(false)
            ->setAllowDecimals(false)
            ->setWidth(50);
        $this->add(new Kwf_Form_Field_Select('text_align', trlKwf('Text Align')))
            ->setShowNoSelection(true)
            ->setValues(array(
                'left' => trlKwf('Left'),
                'right' => trlKwf('Right'),
                'center' => trlKwf('Center'),
                'justify' => trlKwf('Justify')
            ));
    }
}
