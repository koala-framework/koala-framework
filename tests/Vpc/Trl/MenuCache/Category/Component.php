<?php
class Vpc_Trl_MenuCache_Category_Component extends Vpc_Root_Category_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['component'] = array(
            'test' => 'Vpc_Trl_MenuCache_TestComponent_Component',
        );
        $ret['generators']['page']['model'] = 'Vpc_Trl_MenuCache_Category_PagesTestModel';
        return $ret;
    }
}
