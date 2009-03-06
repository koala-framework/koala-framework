<?php
class Vpc_Basic_Feed_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);

        $ret['generators']['feed'] = array(
            'component' => 'Vpc_Basic_Feed_Feed',
            'class' => 'Vps_Component_Generator_Page_Static'
        );
        return $ret;
    }
}
