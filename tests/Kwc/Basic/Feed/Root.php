<?php
class Kwc_Basic_Feed_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);

        $ret['generators']['feed'] = array(
            'component' => 'Kwc_Basic_Feed_Feed',
            'class' => 'Kwf_Component_Generator_Page_Static'
        );
        return $ret;
    }
}
