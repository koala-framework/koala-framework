<?php
class Vpc_Formular_Contact_SettingsForm extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->fields->add(new Vps_Form_Field_TextField('receiver_mail', 'Receiver'))
                ->setVtype('email');
    }
}
