<?php
class Kwc_User_BoxWithoutLogin_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwc_User_BoxWithoutLogin_Root_PagesModel';
        $ret['generators']['page']['component'] = array();
        $ret['generators']['userBox'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_User_BoxWithoutLogin_Box_Component',
            'inherit' => true
        );

        unset($ret['generators']['title']);
        return $ret;
    }
}
