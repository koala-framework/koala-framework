<?php
class Kwc_FormStatic_Form_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = 'Kontaktformular';
        $ret['placeholder']['submitButton'] = 'Senden';
        return $ret;
    }
}
