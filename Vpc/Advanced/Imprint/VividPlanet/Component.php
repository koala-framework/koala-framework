<?php
class Vpc_Advanced_Imprint_VividPlanet_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Imprint.Vivid-Planet'),
            'ownModel' => 'Vps_Component_FieldModel',
            'cssClass' => 'webStandard'
        ));
        return $ret;
    }
}
