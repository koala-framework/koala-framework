<?php
class Vps_Component_DependingOnRows_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vps_Component_DependingOnRows_PagesModel';
        $ret['generators']['page']['component'] = array(
            'test' => 'Vps_Component_DependingOnRows_TestComponent_Component',
            'empty' => 'Vpc_Basic_Empty_Component',
        );
        return $ret;
    }

}
