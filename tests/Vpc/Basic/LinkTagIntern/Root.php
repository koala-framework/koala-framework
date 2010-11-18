<?php
class Vpc_Basic_LinkTagIntern_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vpc_Basic_LinkTagIntern_PagesModel';
        $ret['generators']['page']['component'] = array(
            'link' => 'Vpc_Basic_LinkTagIntern_TestComponent',
            'empty' => 'Vpc_Basic_Empty_Component'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
