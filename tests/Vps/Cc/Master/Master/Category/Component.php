<?php
class Vps_Cc_Master_Master_Category_Component extends Vpc_Root_Category_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vps_Cc_Master_Master_Category_PagesModel';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component'
        );
        return $ret;
    }
}