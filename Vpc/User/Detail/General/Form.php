<?php
class Vpc_User_Detail_General_Form extends Vpc_Abstract_Composite_Form
{
    protected $_useFieldset = true;
    protected $_generalFieldset;
    protected function _init()
    {
        $this->setModel(Zend_Registry::get('userModel'));
        parent::_init();
    }

    protected function _getIdTemplateForChild($key)
    {
        return 'users_{0}-general-'.$key;
    }

    protected function _initFields()
    {
        if ($this->_useFieldset) {
            $this->_generalFieldset = $this->add(new Vps_Form_Container_FieldSet(trlVps('General')));
            $fieldsContainer = $this->_generalFieldset;
        } else {
            $fieldsContainer = $this;
        }

        $fieldsContainer->add(new Vps_Form_Field_TextField('email', trlVps('E-Mail')))
                    ->setVtype('email')
                    ->setAllowBlank(false)
                    ->setWidth(250)
                    ->addValidator(new Vpc_User_Detail_General_Validate_UniqueEmail());

        $fieldsContainer->add(new Vps_Form_Field_TextField('firstname', trlVps('Firstname')))
                    ->setAllowBlank(false)
                    ->setWidth(250);

        $fieldsContainer->add(new Vps_Form_Field_TextField('lastname', trlVps('Lastname')))
                    ->setAllowBlank(false)
                    ->setWidth(250);

        $fieldsContainer->add(new Vps_Form_Field_TextField('title', trlVps('Title')))
                    ->setWidth(250);

        $fieldsContainer->add(new Vps_Form_Field_Select('gender', trlVps('Gender')))
                    ->setShowNoSelection(true)
                    ->setValues(array(
                            'female' => trlVps('Female'),
                            'male'   => trlVps('Male')
                    ));
        parent::_initFields();
    }
}
