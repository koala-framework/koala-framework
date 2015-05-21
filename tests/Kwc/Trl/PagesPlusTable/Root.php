<?php
class Kwc_Trl_PagesPlusTable_Root extends Kwc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        $ret['childModel'] = new Kwc_Trl_RootModel(array(
            'de' => 'Deutsch',
            'en' => 'English',
            'it' => 'Italiano'
        ));
        $ret['generators']['master']['component'] = 'Kwc_Trl_PagesPlusTable_Master';
        $ret['generators']['chained']['component'] = 'Kwc_Root_TrlRoot_Chained_Component.Kwc_Trl_PagesPlusTable_Master';
        return $ret;
    }
}
