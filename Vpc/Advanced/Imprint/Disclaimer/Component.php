<?php
class Vpc_Advanced_Imprint_Disclaimer_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Imprint.Disclaimer'),
            'modelname' => 'Vps_Component_FieldModel',
            'cssClass' => 'webStandard'
        ));
        $ret['assets']['dep'][] = 'VpsSwitchDisplay';
        $ret['generators']['child']['component']['text'] = 'Vpc_Basic_Text_Component';
        return $ret;
    }
}
