<?php
class Kwc_Trl_StaticTextsForm_Translate_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trl('Kontaktforumlar');
        $ret['placeholder']['submitButton'] = 'Nachricht absenden';
        return $ret;
    }
}
