<?php
class Kwc_Blog_Comments_Directory_Component extends Kwc_Posts_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'Kwc_Blog_Comments_Detail_Component';
        unset($ret['generators']['write']);

        //either use write (on child page):
        //$ret['generators']['write']['component'] = 'Kwc_Blog_Comments_Write_Component';

        //or quickwrite (default):
        $ret['generators']['quickwrite'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Blog_Comments_QuickWrite_Component'
        );
        return $ret;
    }
}
