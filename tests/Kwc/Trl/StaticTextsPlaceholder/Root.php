<?php
class Vpc_Trl_StaticTextsPlaceholder_Root extends Vpc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);

        $ret['generators']['master']['component'] =
            'Vpc_Trl_StaticTextsPlaceholder_GermanMaster';
        $ret['generators']['chained']['component'] =
            'Vpc_Root_TrlRoot_Chained_Component.Vpc_Trl_StaticTextsPlaceholder_GermanMaster';

        $ret['childModel'] = new Vpc_Trl_RootModel(array(
            'de' => 'Deutsch',
            'en' => 'English'
        ));

        return $ret;
    }
}
