<?php
class Vpc_FormStatic_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Kontaktformular';
        $ret['placeholder']['submitButton'] = 'Senden';
        return $ret;
    }
}
