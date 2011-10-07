<?php
class Kwf_Component_Generator_StaticSelect_Banner_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Banner';
        return $ret;
    }
}
