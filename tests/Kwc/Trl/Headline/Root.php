<?php
class Kwc_Trl_Headline_Root extends Kwc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        $ret['childModel'] = new Kwc_Trl_RootModel(array(
            'de' => 'Deutsch',
            'en' => 'English'
        ));
        $ret['generators']['master']['component'] = 'Kwc_Trl_Headline_German';
        $ret['generators']['chained']['component'] = 'Kwc_Trl_Headline_English.Kwc_Trl_Headline_German';
        return $ret;
    }
}
