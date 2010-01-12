<?php
class Vpc_News_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vpc_News_PagesModel';
        $ret['generators']['page']['component'] = array(
            'news' => 'Vpc_News_TestComponent'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
