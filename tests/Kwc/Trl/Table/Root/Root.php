<?php
class Kwc_Trl_Table_Root_Root extends Kwc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        $ret['childModel'] = new Kwc_Trl_RootModel(array(
            'master' => 'Master',
            'de' => 'Deutsch',
            'en' => 'English'
        ));
        $ret['generators']['master']['component'] = 'Kwc_Trl_Table_Root_Master';
        $ret['generators']['chained']['component'] = 'Kwc_Trl_Table_Root_Chained.Kwc_Trl_Table_Root_Master';
        return $ret;
    }
}
