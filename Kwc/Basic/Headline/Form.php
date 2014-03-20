<?php
class Kwc_Basic_Headline_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $headlines = Kwc_Abstract::getSetting($this->getClass(), 'headlines');
        $values = array();
        foreach ($headlines as $key => $headline) {
            $values[] = array($key, $headline['text']);
        }
        $this->fields->add(new Kwf_Form_Field_Select('headline_type', trlKwfStatic('Layer')))
            ->setValues($values);
        $this->fields->add(new Kwf_Form_Field_TextField('headline1', trlKwfStatic('Text 1')))
            ->setWidth(450)
            ->setAllowBlank(false);
        $this->fields->add(new Kwf_Form_Field_TextField('headline2', trlKwfStatic('Text 2')))
            ->setWidth(450);
    }
}
