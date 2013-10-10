<?php
class Kwc_Advanced_Youtube_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $validator = new Zend_Validate_Regex(array(
            'pattern' => Kwc_Advanced_Youtube_Component::REGEX
        ));
        $validator->setMessage(trlKwf('No valid youtube url'), Zend_Validate_Regex::NOT_MATCH);
        $this->add(new Kwf_Form_Field_UrlField('url', trlKwfStatic('URL')))
            ->addValidator($validator)
            ->setAllowBlank(false)
            ->setWidth(400);
        $this->add(new Kwf_Form_Field_TextField('videoWidth', trlKwfStatic('Width')));
        $this->add(new Kwf_Form_Field_Select('dimensions', trlStatic('Dimensionen')))
            ->setDefaultValue('16x9')
            ->setValues(array(
                '16x9' => trlStatic('16:9'),
                '4x3' => trlStatic('4:3')
            ));
        $this->add(new Kwf_Form_Field_Checkbox('autoplay', trlKwfStatic('Autoplay')));
    }
}
