<?php
class Kwc_Advanced_Imprint_GoogleAnalytics_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlKwfStatic('Imprint').'.Google-Analytics',
            'cssClass' => 'webStandard'
        ));
        return $ret;
    }
}
