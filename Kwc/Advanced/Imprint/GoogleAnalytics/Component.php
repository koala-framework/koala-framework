<?php
class Vpc_Advanced_Imprint_GoogleAnalytics_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Imprint.Google-Analytics'),
            'cssClass' => 'webStandard'
        ));
        return $ret;
    }
}
