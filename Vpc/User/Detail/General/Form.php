<?php
class Vpc_User_Detail_General_Form extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->setTable(Zend_Registry::get('userModel'));
        
        $this->add(new Vps_Form_Field_TextField('email', trlVps('E-Mail')))
                    ->setVType('email')
                    ->setAllowBlank(false)
                    ->setWidth(250);

        $this->add(new Vps_Form_Field_TextField('firstname', trlVps('Firstname')))
                    ->setAllowBlank(false)
                    ->setWidth(250);

        $this->add(new Vps_Form_Field_TextField('lastname', trlVps('Lastname')))
                    ->setAllowBlank(false)
                    ->setWidth(250);

        $this->add(new Vps_Form_Field_TextField('title', trlVps('Title')))
                    ->setWidth(250);

        $this->add(new Vps_Form_Field_Select('gender', trlVps('Gender')))
                    ->setValues(array(
                            'female' => trlVps('Female'),
                            'male'   => trlVps('Male')
                    ));
    }
    
    public function setId()
    {
        parent::setId(Zend_Registry::get('userModel')->getAuthedUser()->id);
    }
}
