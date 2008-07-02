<?php
class Vps_Controller_Action_User_SelfController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save'=>true, 'edit' => true);

    protected function _initFields()
    {
        $genders = array('male' => 'male', 'female' => 'female');

        $this->_form->setTable(Zend_Registry::get('userModel'));

        // Hauptdaten
        $fs1 = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Login data')));
        $fs1->setLabelWidth(130);
        $fs1->setStyle('margin:10px;');

        $editor = new Vps_Form_Field_TextField('email', trlVps('Email'));
        $editor->setVtype('email');
        $editor->setWidth(220);
        $fs1->add($editor);

        $editor = new Vps_Form_Field_Password('password1', trlVps('Change password'));
        $fs1->add($editor);

        $editor = new Vps_Form_Field_Password('password2', trlVps('Repeat password'));
        $fs1->add($editor);

        // Person
        $fs2 = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Personal data')));
        $fs2->setLabelWidth(80);
        $fs2->setStyle('margin:10px;');

        $editor = new Vps_Form_Field_ComboBox('gender', trlVps('Gender'));
        $editor->setValues($genders)
               ->setEditable(false)
               ->setTriggerAction('all')
               ->setAllowBlank(false)
               ->setLazyRender(true)
               ->setForceSelection(true);
        $fs2->add($editor);

        $fs2->add(new Vps_Form_Field_TextField('title', trlVps('Title')));
        $fs2->add(new Vps_Form_Field_TextField('firstname', trlVps('First name')));
        $fs2->add(new Vps_Form_Field_TextField('lastname', trlVps('Last name')));

        $config = Zend_Registry::get('config');
        if ($config->languages && count($config->languages) > 1) {
            $data = array();
            foreach ($config->languages as $key => $value){
                $data[$key] = $value;
            }
            $fs2->add(new Vps_Form_Field_Select('language', trlVps('Language')))
            ->setValues($data);
        }

    }


}