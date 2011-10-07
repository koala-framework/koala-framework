<?php
class Vpc_Trl_StaticTextsOneLang_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['page']['model'] = 'Vpc_Trl_StaticTextsOneLang_PagesModel';
        $ret['generators']['page']['component'] = array(
            'trltest' => 'Vpc_Trl_StaticTextsOneLang_Translate_Component'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
