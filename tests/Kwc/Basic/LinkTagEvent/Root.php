<?php
class Kwc_Basic_LinkTagEvent_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwc_Basic_LinkTagEvent_PagesModel';
        $ret['generators']['page']['component'] = array(
            'events' => 'Kwc_Events_TestComponent',
            'link' => 'Kwc_Basic_LinkTagEvent_TestComponent'
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
