<?php
class Vpc_Trl_Simple_Root extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        $ret['generators']['de'] = array(
            'class' => 'Vps_Component_Generator_PseudoPage_Static',
            'component' => 'Vpc_Trl_Simple_German',
            'name' => 'de'
        );
        $ret['generators']['en'] = array(
            'class' => 'Vps_Component_Generator_PseudoPage_Static',
            'component' => 'Vpc_Trl_Simple_English.Vpc_Trl_Simple_German',
            'name' => 'en'
        );
        return $ret;
    }
}
