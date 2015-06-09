<?php
class Kwc_User_Detail_General_Form extends Kwc_Abstract_Composite_Form
{
    protected $_useFieldset = true;
    protected $_generalFieldset;
    protected function _init()
    {
        $this->setModel(Kwf_Registry::get('userModel')->getEditModel());
        parent::_init();
    }

    protected function _getIdTemplateForChild($key)
    {
        return 'users_{0}-general-'.$key;
    }

    protected function _initFields()
    {
        if ($this->_useFieldset) {
            $this->_generalFieldset = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('General')));
            $fieldsContainer = $this->_generalFieldset;
        } else {
            $fieldsContainer = $this;
        }

        $fieldsContainer->add(new Kwf_Form_Field_TextField('email', trlKwf('E-Mail')))
                    ->setVtype('email')
                    ->setAllowBlank(false)
                    ->setWidth(250)
                    ->addValidator(new Kwc_User_Detail_General_Validate_UniqueEmail());

        $fieldsContainer->add(new Kwf_Form_Field_TextField('firstname', trlKwf('Firstname')))
                    ->setAllowBlank(false)
                    ->setWidth(250);

        $fieldsContainer->add(new Kwf_Form_Field_TextField('lastname', trlKwf('Lastname')))
                    ->setAllowBlank(false)
                    ->setWidth(250);

        $fieldsContainer->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
                    ->setWidth(250);

        $fieldsContainer->add(new Kwf_Form_Field_Select('gender', trlKwf('Gender')))
                    ->setShowNoSelection(true)
                    ->setValues(array(
                            'female' => trlKwf('Female'),
                            'male'   => trlKwf('Male')
                    ));
        parent::_initFields();
    }
}
