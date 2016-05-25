<?php
class Kwc_Statistics_Opt_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextArea('text_opt_in', trlKwf('Text for opt-in')))
            ->setDefaultValue(trlKwf('Tracking cookies are set when visiting this webpage. Click to deactivate tracking cookies.'))
            ->setWidth(500)
            ->setHeight(100);
        $this->add(new Kwf_Form_Field_TextArea('text_opt_out', trlKwf('Text for opt-out')))
            ->setDefaultValue(trlKwf('No tracking cookies are set when visiting this webpage. Click to activate tracking cookies.'))
            ->setWidth(500)
            ->setHeight(100);
    }
}
