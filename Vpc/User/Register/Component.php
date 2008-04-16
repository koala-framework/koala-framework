<?php
class Vpc_User_Register_Component extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => '',
            'tablename'  => 'Vpc_Formular_Model',
            'hideInNews' => true,
            'fieldsNotSaved' => array('sbmt'),
            'standardRole' => 'guest',
        ));
        $ret['childComponentClasses']['success'] = 'Vpc_User_Register_Success_Component';
        return $ret;
    }

    protected function _init()
    {
        parent::_init();

        $fieldSettings = array('name'  => 'email',
                               'width' => 200,
                               'value' => '');
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'email');
        $c->store('fieldLabel', 'Email');
        $c->store('isMandatory', true);

        $fieldSettings = array('name'  => 'firstname',
                               'width' => 200,
                               'value' => '');
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'firstname');
        $c->store('fieldLabel', 'Vorname');
        $c->store('isMandatory', true);

        $fieldSettings = array('name'  => 'lastname',
                               'width' => 200,
                               'value' => '');
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'lastname');
        $c->store('fieldLabel', 'Zuname');
        $c->store('isMandatory', true);

        $fieldSettings = array('name'  => 'title',
                               'width' => 200,
                               'value' => '');
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'title');
        $c->store('fieldLabel', 'Titel');
        $c->store('isMandatory', false);

        $genderOptions = array(
            array('value' => 'female', 'text'  => 'Weiblich', 'checked' => 1),
            array('value' => 'male', 'text'  => 'Männlich', 'checked' => 0)
        );
        if (isset($_POST['gender']) && $_POST['gender'] == 'female') {
            $genderOptions[0]['checked'] = 1;
            $genderOptions[1]['checked'] = 0;
        } else if (isset($_POST['gender']) && $_POST['gender'] == 'male') {
            $genderOptions[0]['checked'] = 0;
            $genderOptions[1]['checked'] = 1;
        }

        $c = $this->_createFieldComponent('Select', array('name'=>'gender', 'type' => 'select', 'width'=>200));
        $c->store('name', 'gender');
        $c->setOptions($genderOptions);
        $c->store('fieldLabel', 'Geschlecht');
        $c->store('isMandatory', true);

        $this->_webFields();

        $c = $this->_createFieldComponent('Submit', array(
            'name'=>'sbmt', 'width'=>200, 'text' => 'Account erstellen'
        ));
        $c->store('name', 'sbmt');
        $c->store('fieldLabel', '&nbsp;');
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['formTemplate'] = Vpc_Admin::getComponentFile('Vpc_Formular_Component', '', 'tpl');
        return $ret;
    }

    protected function _webFields()
    {
    }

    protected function _processForm()
    {
        $email = '';
        foreach ($this->getChildComponents() as $c) {
            if ($c instanceof Vpc_Formular_Field_Interface) {
                $name = $c->getStore('name');
                if ($name == 'email') {
                    $email = $c->getValue();
                }
            }
        }

        if ($email) {
            $existsRow = Zend_Registry::get('userModel')->fetchRowByEmail($email);
            if ($existsRow) {
                throw new Vps_ClientException('Ein Benutzer mit dieser Email-Adresse existiert bereits.');
            }
        }

        $fieldsNotSaved = $this->_getSetting('fieldsNotSaved');
        $user = Zend_Registry::get('userModel')->createRow();
        if ($user) {
            foreach ($this->getChildComponents() as $c) {
                if ($c instanceof Vpc_Formular_Field_Interface) {
                    $name = $c->getStore('name');
                    if (!in_array($name, $fieldsNotSaved)) {
                        $user->$name = $c->getValue();
                    }
                }
            }
            $user->role = $this->_getSetting('standardRole');
            $user->save();

            return $user->id;
        }
        return;
    }

}
