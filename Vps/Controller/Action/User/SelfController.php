<?php
class Vps_Controller_Action_User_SelfController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save'=>true, 'edit' => true);

    protected function _initFields()
    {
        $genders = array('male' => 'male', 'female' => 'female');

        $this->_form->setTable(Zend_Registry::get('userModel'));

        // Hauptdaten
        $fs1 = $this->_form->add(new Vps_Auto_Container_FieldSet('Login data'));
        $fs1->setLabelWidth(130);
        $fs1->setStyle('margin:10px;');

        $editor = new Vps_Auto_Field_TextField('email', 'Email');
        $editor->setVtype('email');
        $editor->setWidth(220);
        $fs1->add($editor);

        $editor = new Vps_Auto_Field_TextField('password1', 'Change password');
        $editor->setInputType('password');
        $fs1->add($editor);

        $editor = new Vps_Auto_Field_TextField('password2', 'Repeat password');
        $editor->setInputType('password');
        $fs1->add($editor);

        // Person
        $fs2 = $this->_form->add(new Vps_Auto_Container_FieldSet('Personal data'));
        $fs2->setLabelWidth(80);
        $fs2->setStyle('margin:10px;');

        $editor = new Vps_Auto_Field_ComboBox('gender', 'Gender');
        $editor->setValues($genders)
               ->setEditable(false)
               ->setTriggerAction('all')
               ->setAllowBlank(false)
               ->setLazyRender(true)
               ->setForceSelection(true);
        $fs2->add($editor);

        $fs2->add(new Vps_Auto_Field_TextField('title', 'Title'));
        $fs2->add(new Vps_Auto_Field_TextField('firstname', 'First name'));
        $fs2->add(new Vps_Auto_Field_TextField('lastname', 'Last name'));

    }


}