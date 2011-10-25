<?php
class Kwf_Component_Generator_TwoComponentsWithSamePlugin_Static2 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['plugins'] = array('Kwf_Component_Plugin_Password_Component');
        return $ret;
    }

}
