<?php
class Kwc_Advanced_Imprint_Imprint_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Imprint.Imprint');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['cssClass'] = 'webStandard';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        return $ret;
    }
}
