<?php
class Vpc_Basic_LinkTagEvent_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vpc_Basic_LinkTagEvent_PagesModel';
        $ret['generators']['page']['component'] = array(
            'events' => 'Vpc_Events_TestComponent',
            'link' => 'Vpc_Basic_LinkTagEvent_TestComponent'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
