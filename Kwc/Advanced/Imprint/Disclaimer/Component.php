<?php
class Kwc_Advanced_Imprint_Disclaimer_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = array_merge(parent::getSettings($param), array(
            'componentName' => trlKwfStatic('Imprint').'.'.trlKwfStatic('Disclaimer'),
            'ownModel' => 'Kwf_Component_FieldModel',
            'rootElementClass' => 'kwfUp-webStandard'
        ));
        $ret['componentCategory'] = 'admin';
        $ret['assetsDefer']['dep'][] = 'KwfSwitchDisplay';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        return $ret;
    }
}
