<?php
class Kwc_Advanced_Youtube_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_ShowField('original_url', trlKwf('Original Url')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('url'));
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Own Url')));
        $fs->setCheckboxToggle(true);
        $fs->setCheckboxName('own_url');

        $validator = new Zend_Validate_Regex(array(
            'pattern' => Kwc_Advanced_Youtube_Component::REGEX
        ));
        $validator->setMessage(trlKwf('No valid youtube url'), Zend_Validate_Regex::NOT_MATCH);
        $fs->add(new Kwf_Form_Field_UrlField('url', trlKwfStatic('URL')))
            ->addValidator($validator)
            ->setAllowBlank(false)
            ->setWidth(400);
    }
}
