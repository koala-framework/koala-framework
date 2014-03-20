<?php
class Kwc_Basic_Headline_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_Select('headline_type', trlKwfStatic('Layer')))
            ->setValues($this->_getHeadlines());
        $this->fields->add(new Kwf_Form_Field_TextField('headline1', trlKwfStatic('Text 1')))
            ->setWidth(450)
            ->setAllowBlank(false);
        $this->fields->add(new Kwf_Form_Field_TextField('headline2', trlKwfStatic('Text 2')))
            ->setWidth(450);
    }

    protected function _getHeadlines()
    {
        return array(
            'h1' => trlKwfStatic('Headline {0}', 1),
            'h2' => trlKwfStatic('Headline {0}', 2),
            'h3' => trlKwfStatic('Headline {0}', 3),
            'h4' => trlKwfStatic('Headline {0}', 4),
            'h5' => trlKwfStatic('Headline {0}', 5),
            'h6' => trlKwfStatic('Headline {0}', 6)
        );
    }
}
