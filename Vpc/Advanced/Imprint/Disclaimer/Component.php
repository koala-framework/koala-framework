<?php
class Vpc_Advanced_Imprint_Disclaimer_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Imprint.Disclaimer'),
            'ownModel' => 'Vps_Component_FieldModel',
            'cssClass' => 'webStandard'
        ));
        $ret['assets']['dep'][] = 'VpsSwitchDisplay';
        return $ret;
    }
}
