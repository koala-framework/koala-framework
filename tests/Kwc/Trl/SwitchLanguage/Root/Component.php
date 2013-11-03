<?php
class Kwc_Trl_SwitchLanguage_Root_Component extends Kwc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        $ret['childModel'] = 'Kwc_Trl_SwitchLanguage_Root_LanguagesModel';
        $ret['generators']['master']['component'] = 'Kwc_Trl_SwitchLanguage_Master_Component';
        $ret['generators']['chained']['component'] = 'Kwc_Root_TrlRoot_Chained_Component.Kwc_Trl_SwitchLanguage_Master_Component';
        return $ret;
    }
}
