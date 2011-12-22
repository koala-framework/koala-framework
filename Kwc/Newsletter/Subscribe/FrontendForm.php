<?php
class Kwc_Newsletter_Subscribe_FrontendForm extends Kwf_Form
{
    protected $_modelName = 'Kwc_Newsletter_Subscribe_Model';

    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Kwf_Form_Field_Radio('gender', trlKwfStatic('Gender')))
            ->setAllowBlank(false)
            ->setValues(array(
                'female' => trlKwfStatic('Female'),
                'male'   => trlKwfStatic('Male')
            ))
            ->setCls('kwf-radio-group-transparent');
        $this->add(new Kwf_Form_Field_TextField('title', trlKwfStatic('Title')))
            ->setWidth(255);
        $this->add(new Kwf_Form_Field_TextField('firstname', trlKwfStatic('Firstname')))
            ->setWidth(255)
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('lastname', trlKwfStatic('Lastname')))
            ->setWidth(255)
            ->setAllowBlank(false);

        $validator = new Kwf_Validate_Row_Unique();
        $validator->addSelectExpr(new Kwf_Model_Select_Expr_Equal('unsubscribed', 0));
        $this->add(new Kwf_Form_Field_TextField('email', trlKwfStatic('E-Mail')))
            ->setWidth(255)
            ->setVtype('email')
            ->setAllowBlank(false)
            ->addValidator($validator, 'email');
        $this->add(new Kwf_Form_Field_Radio('format', trlKwfStatic('Format')))
            ->setAllowBlank(false)
            ->setValues(array(
                'html' => trlKwfStatic('HTML-Format'),
                'text' => trlKwfStatic('Text-Format')
            ))
            ->setCls('kwf-radio-group-transparent');
    }
}
