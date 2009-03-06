<?php
class Vpc_Advanced_Imprint_VividPlanet_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Imprint.Vivid-Planet'),
            'modelname' => 'Vps_Component_FieldModel'
        ));
        $ret['generators']['child']['component']['text'] = 'Vpc_Basic_Text_Component';
        return $ret;
    }
}
