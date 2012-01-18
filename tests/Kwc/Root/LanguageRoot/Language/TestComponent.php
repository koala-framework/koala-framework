<?php
class Kwc_Root_LanguageRoot_Language_TestComponent extends Kwc_Root_LanguageRoot_Language_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['category']);
        $ret['generators']['page'] = array(
            'class' => 'Kwc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'empty' => 'Kwc_Basic_None_Component',
            ),
            'model' => 'Kwc_Root_LanguageRoot_Language_PagesTestModel'
        );
        return $ret;
    }
}
