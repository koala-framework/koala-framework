<?php
class Vpc_Advanced_Imprint_Imprint_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Imprint.Imprint'),
            'modelname' => 'Vps_Component_FieldModel',
            'cssClass' => 'webStandard'
        ));
        return $ret;
    }
}
