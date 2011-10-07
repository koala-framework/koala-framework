<?php
class Kwc_Trl_StaticTextsForm_Root extends Kwc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);

        $ret['generators']['master']['component'] =
            'Kwc_Trl_StaticTextsForm_GermanMaster';
        $ret['generators']['chained']['component'] =
            'Kwc_Root_TrlRoot_Chained_Component.Kwc_Trl_StaticTextsForm_GermanMaster';

        $ret['childModel'] = new Kwc_Trl_RootModel(array(
            'de' => 'Deutsch',
            'en' => 'English'
        ));

        return $ret;
    }
}
