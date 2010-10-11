<?php
class Vpc_Advanced_Imprint_Imprint_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Imprint.Imprint');
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['cssClass'] = 'webStandard';
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_Form';
        return $ret;
    }
}
