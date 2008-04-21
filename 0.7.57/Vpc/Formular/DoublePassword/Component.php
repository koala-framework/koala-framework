<?php
class Vpc_Formular_DoublePassword_Component extends Vpc_Abstract_Composite_Component
                                            implements Vpc_Formular_Field_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Formular Fields.Double Password';
        $ret['childComponentClasses'] = array(
            'password1' => 'Vpc_Formular_Password_Component',
            'password2' => 'Vpc_Formular_Password_Component'
        );
        $ret['default'] = array(
            'maxlength' => '255',
            'width' => '150',
            'value' => '',
            'validator' => ''
        );
        return $ret;
    }

    public function store($key, $val)
    {
        parent::store($key, $val);

        $c = $this->getChildComponent('password1');
        $c->store($key, $val);

        if ($key == 'fieldLabel') $val = 'Wiederholung:';
        if ($key == 'name') $val = $val.'_repeat';
        $c = $this->getChildComponent('password2');
        $c->store($key, $val);
    }

    public function processInput()
    {
        $this->getChildComponent('password1')->processInput();
        $this->getChildComponent('password2')->processInput();
    }

    public function validateField($mandatory)
    {
        $return = '';
        $return .= $this->getChildComponent('password1')->validateField($mandatory);
        $return .= $this->getChildComponent('password2')->validateField($mandatory);

        if ($this->getChildComponent('password1')->getValue() !=
            $this->getChildComponent('password2')->getValue()
        ) {
            $return = trlVps("Passwords are different. Please try again.");
        }
        return $return;
    }

    public function getValue()
    {
        return $this->getChildComponent('password1')->getValue();
    }
}
