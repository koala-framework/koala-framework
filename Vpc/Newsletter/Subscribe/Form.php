<?php
class Vpc_Newsletter_Subscribe_Form extends Vps_Form
{
    protected $_modelName = 'Vpc_Newsletter_Subscribe_Model';

    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Vps_Form_Field_Radio('gender', trlVps('Gender')))
            ->setAllowBlank(false)
            ->setValues(array(
                'female' => trlVps('Female'),
                'male'   => trlVps('Male')
            ));
        $this->add(new Vps_Form_Field_TextField('title', trlVps('Title')))
            ->setWidth(255);
        $this->add(new Vps_Form_Field_TextField('firstname', trlVps('Firstname')))
            ->setWidth(255)
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_TextField('lastname', trlVps('Lastname')))
            ->setWidth(255)
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_TextField('email', trlVps('E-Mail')))
            ->setWidth(255)
            ->setVtype('email')
            ->setAllowBlank(false)
            ->addValidator(new Vps_Validate_Row_Unique());
        $this->add(new Vps_Form_Field_Radio('format', trlVps('Format')))
            ->setAllowBlank(false)
            ->setValues(array(
                'html' => trlVps('HTML-Format'),
                'text' => trlVps('Text-Format')
            ));
    }
}
