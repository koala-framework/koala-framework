<?php
class Vpc_Advanced_Imprint_FacebookDisclaimer_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Imprint.Facebook Disclaimer'),
            'cssClass' => 'webStandard'
        ));
        return $ret;
    }
}
