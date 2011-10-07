<?php
class Vpc_Trl_Paragraphs_Root extends Vpc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        $ret['childModel'] = new Vpc_Trl_RootModel(array(
            'de' => 'Deutsch',
            'en' => 'English'
        ));
        $ret['generators']['master']['component'] = 'Vpc_Trl_Paragraphs_German';
        $ret['generators']['chained']['component'] = 'Vpc_Trl_Paragraphs_English.Vpc_Trl_Paragraphs_German';
        return $ret;
    }
}
