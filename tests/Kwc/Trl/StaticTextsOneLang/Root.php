<?php
class Kwc_Trl_StaticTextsOneLang_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['page']['model'] = 'Kwc_Trl_StaticTextsOneLang_PagesModel';
        $ret['generators']['page']['component'] = array(
            'trltest' => 'Kwc_Trl_StaticTextsOneLang_Translate_Component'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
