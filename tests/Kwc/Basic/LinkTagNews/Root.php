<?php
class Vpc_Basic_LinkTagNews_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vpc_Basic_LinkTagNews_PagesModel';
        $ret['generators']['page']['component'] = array(
            'news' => 'Vpc_News_TestComponent',
            'link' => 'Vpc_Basic_LinkTagNews_TestComponent'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
