<?php
class Vpc_User_Abstract_Form extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'  => 'Vpc_Formular_Model',
            'hideInNews' => true,
            'fieldsNotSaved' => array('sbmt')
        ));
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
        $c->store('isMandatory', true);

        $fieldSettings = array('name'  => 'lastname',
                               'width' => 200,
                               'value' => (!$user || !$user->lastname?'':$user->lastname));
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'lastname');
        $c->store('fieldLabel', 'Zuname');
        $c->store('isMandatory', true);

        $fieldSettings = array('name'  => 'title',
                               'width' => 200,
                               'value' => (!$user || !$user->title?'':$user->title));
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'title');
        $c->store('fieldLabel', 'Titel');
        $c->store('isMandatory', false);

        $genderOptions = array(
            array('value' => 'female', 'text'  => 'Weiblich', 'checked' => 1),
            array('value' => 'male', 'text'  => 'Männlich', 'checked' => 0)
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
        $c->store('fieldLabel', 'Geschlecht');
        $c->store('isMandatory', true);

        $this->_webFields();
    }

    /**
     * Fügt Formularfelder vom Web hinzu
     *
     * Fügt Web-Felder direkt unter der Standardform ein. Kann von Web-Komponente überschrieben werden.
     */
    protected function _webFields()
    {
    }

    /**
     * Wird überschrieben z.B. bei Edit um die Init-Werte fürs Formular zu setzen
     */
    protected function _getEditRow()
    {
    }

    protected function _beforeSave($row)
    {
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
            $this->_beforeSave($user);
            $user->save();
        }
    }
}
