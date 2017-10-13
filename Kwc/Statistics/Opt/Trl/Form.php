<?php
class Kwc_Statistics_Opt_Trl_Form extends Kwc_Abstract_Composite_Trl_Form
{
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextArea('text_opt_in', trlKwf('Text for opt-in')))
            ->setDefaultValue(trlKwf('Cookies are set when visiting this webpage. Click to deactivate cookies.'))
            ->setWidth(500)
            ->setHeight(100);
        $this->add(new Kwf_Form_Field_ShowField('original_text_opt_in', trlKwf('Original Text for opt-in')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('text_opt_in'));
        $this->add(new Kwf_Form_Field_TextArea('text_opt_out', trlKwf('Text for opt-out')))
            ->setDefaultValue(trlKwf('No cookies are set when visiting this webpage. Click to activate cookies.'))
            ->setWidth(500)
            ->setHeight(100);
        $this->add(new Kwf_Form_Field_ShowField('original_text_opt_out', trlKwf('Original Text for opt-out')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('text_opt_out'));
    }
}
