<?php
class Vpc_Trl_StaticTextsForm_Translate_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trl('Kontaktforumlar');
        $ret['placeholder']['submitButton'] = 'Nachricht absenden';
        return $ret;
    }
}
