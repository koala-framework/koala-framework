<?php
class Kwc_Statistics_Opt_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Cookie Opt In / Opt Out');
        $ret['viewCache'] = false;
        return $ret;
    }
}

