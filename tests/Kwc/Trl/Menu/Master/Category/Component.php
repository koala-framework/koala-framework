<?php
class Kwc_Trl_Menu_Master_Category_Component extends Kwc_Root_Category_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['model'] = 'Kwc_Trl_Menu_Master_Category_Model';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_None_Component'
        );
        return $ret;
    }
}
