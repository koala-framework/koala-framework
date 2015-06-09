<?php
class Kwc_Advanced_Imprint_Disclaimer_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlKwfStatic('Imprint').'.'.trlKwfStatic('Disclaimer'),
            'ownModel' => 'Kwf_Component_FieldModel',
            'cssClass' => 'webStandard'
        ));
        $ret['componentCategory'] = 'admin';
        $ret['assetsDefer']['dep'][] = 'KwfSwitchDisplay';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        return $ret;
    }
}
