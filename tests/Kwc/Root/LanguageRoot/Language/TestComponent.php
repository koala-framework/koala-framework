<?php
class Vpc_Root_LanguageRoot_Language_TestComponent extends Vpc_Root_LanguageRoot_Language_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['category']);
        $ret['generators']['page'] = array(
            'class' => 'Vpc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'empty' => 'Vpc_Basic_Empty_Component',
            ),
            'model' => 'Vpc_Root_LanguageRoot_Language_PagesTestModel'
        );
        return $ret;
    }
}
