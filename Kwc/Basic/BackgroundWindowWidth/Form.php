<?php
class Kwc_Basic_BackgroundWindowWidth_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $backgroundColors = Kwc_Abstract::getSetting($this->getClass(), 'backgroundColors');
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Standard Adjustments')));
        $fs->add(new Kwf_Form_Field_Select('background_color', trlKwf('Background Color')))
            ->setAllowBlank(true)
            ->setValues($backgroundColors);
        $fs->add(new Kwf_Form_Field_NumberField('margin_bottom', trlKwf('Margin Bottom')))
            ->setAllowBlank(true)
            ->setWidth(30)
            ->setComment('px');
    }
}

