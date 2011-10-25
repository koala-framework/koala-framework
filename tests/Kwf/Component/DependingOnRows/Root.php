<?php
class Kwf_Component_DependingOnRows_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwf_Component_DependingOnRows_PagesModel';
        $ret['generators']['page']['component'] = array(
            'test' => 'Kwf_Component_DependingOnRows_TestComponent_Component',
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        return $ret;
    }

}
