<?php
class Kwc_Statistics_Opt_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextArea('text_opt_in', trlKwf('Text for opt-in')))
            ->setDefaultValue(trlKwf('Cookies are set when visiting this webpage. Click to deactivate cookies.'))
            ->setWidth(500)
            ->setHeight(100);
        $this->add(new Kwf_Form_Field_TextArea('text_opt_out', trlKwf('Text for opt-out')))
            ->setDefaultValue(trlKwf('No cookies are set when visiting this webpage. Click to activate cookies.'))
            ->setWidth(500)
            ->setHeight(100);
    }
}
