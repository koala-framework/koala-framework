<?php
class Vpc_Advanced_SearchEngineReferer_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']);
        $ret['generators']['referer'] = array(
            'component' => 'Vpc_Advanced_SearchEngineReferer_Referer_Component',
            'class' => 'Vps_Component_Generator_Static'
        );
        $ret['generators']['referer2'] = array(
            'component' => 'Vpc_Advanced_SearchEngineReferer_Referer2_Component',
            'class' => 'Vps_Component_Generator_Static'
        );
        return $ret;
    }

}
