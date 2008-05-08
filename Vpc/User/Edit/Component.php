<?php
class Vpc_User_Edit_Component extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'  => 'Vpc_Formular_Model',
            'fieldsNotSaved' => array('sbmt')
        ));
        $ret['childComponentClasses']['success'] = 'Vpc_User_Edit_Success_Component';
        $ret['componentName'] = '';
        return $ret;
    }

    protected function _init()
    {
        parent::_init();

        $user = $this->_getEditRow();

        $fieldSettings = array('name'  => 'firstname',
                               'width' => 200,
                               'value' => (!$user || !$user->firstname?'':$user->firstname));
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'firstname');
        $c->store('fieldLabel', 'Vorname');
        $c->store('isMandatory', false);

        $fieldSettings = array('name'  => 'lastname',
                               'width' => 200,
                               'value' => (!$user || !$user->lastname?'':$user->lastname));
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'lastname');
        $c->store('fieldLabel', 'Zuname');
        $c->store('isMandatory', false);

        $fieldSettings = array('name'  => 'title',
                               'width' => 200,
                               'value' => (!$user || !$user->title?'':$user->title));
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'title');
        $c->store('fieldLabel', 'Titel');
        $c->store('isMandatory', false);

        $genderOptions = array(
            array('value' => 'female', 'text'  => trlVps('Female'), 'checked' => 1),
            array('value' => 'male', 'text'  => trlVps('Male'), 'checked' => 0)
        );
        if (isset($_POST['gender']) && $_POST['gender'] == 'female' ||
            ($user && $user->gender == 'female')
        ) {
            $genderOptions[0]['checked'] = 1;
            $genderOptions[1]['checked'] = 0;
        } else if (isset($_POST['gender']) && $_POST['gender'] == 'male' ||
            ($user && $user->gender == 'male')
        ) {
            $genderOptions[0]['checked'] = 0;
            $genderOptions[1]['checked'] = 1;
        }

        $c = $this->_createFieldComponent('Select', array('name'=>'gender', 'type' => 'select', 'width'=>200));
        $c->store('name', 'gender');
        $c->setOptions($genderOptions);
        $c->store('fieldLabel', trlVps('Gender'));
        $c->store('isMandatory', true);

        $this->_webFields();

        $c = $this->_createFieldComponent('Submit', array(
            'name'=>'sbmt', 'width'=>200, 'text' => trlVps('Edit account')
        ));
        $c->store('name', 'sbmt');
        $c->store('fieldLabel', '&nbsp;');
    }

    protected function _getEditRow()
    {
        return Zend_Registry::get('userModel')->getAuthedUser();
    }

    protected function _webFields()
    {
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['formTemplate'] = Vpc_Admin::getComponentFile('Vpc_Formular_Component', '', 'tpl');
        $ret['email'] = $this->_getEditRow()->email;
        return $ret;
    }

    protected function _processForm()
    {
        $fieldsNotSaved = $this->_getSetting('fieldsNotSaved');
        $user = $this->_getEditRow();
        if ($user) {
            foreach ($this->getChildComponents() as $c) {
                if ($c instanceof Vpc_Formular_Field_Interface) {
                    $name = $c->getStore('name');
                    if (!in_array($name, $fieldsNotSaved)) {
                        $user->$name = $c->getValue();
                    }
                }
            }
            $user->save();
        }
    }

}
