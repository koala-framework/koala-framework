<?php
class Vpc_Advanced_Imprint_Imprint_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Imprint.Imprint'),
            'modelname' => 'Vps_Component_FieldModel',
            'cssClass' => 'webStandard'
        ));
        $ret['generators']['child']['component']['text'] = 'Vpc_Basic_Text_Component';
        return $ret;
    }
}
