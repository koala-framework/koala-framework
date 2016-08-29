<?php
class Kwc_Trl_StaticTextsForm_Translate_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlStatic('Kontaktforumlar');
        $ret['placeholder']['submitButton'] = 'Nachricht absenden';
        return $ret;
    }
}
