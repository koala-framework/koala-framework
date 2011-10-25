<?php
class Kwc_Advanced_SearchEngineReferer_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']);
        $ret['generators']['referer'] = array(
            'component' => 'Kwc_Advanced_SearchEngineReferer_Referer_Component',
            'class' => 'Kwf_Component_Generator_Static'
        );
        $ret['generators']['referer2'] = array(
            'component' => 'Kwc_Advanced_SearchEngineReferer_Referer2_Component',
            'class' => 'Kwf_Component_Generator_Static'
        );
        return $ret;
    }

}
