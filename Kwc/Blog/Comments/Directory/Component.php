<?php
class Kwc_Blog_Comments_Directory_Component extends Kwc_Posts_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'Kwc_Blog_Comments_Detail_Component';
        $ret['generators']['write']['component'] = 'Kwc_Blog_Comments_Write_Component';
        $ret['generators']['quickwrite'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Blog_Comments_QuickWrite_Component'
        );
        return $ret;
    }
}
