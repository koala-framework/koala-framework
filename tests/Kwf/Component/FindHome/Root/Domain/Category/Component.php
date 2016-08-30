<?php
class Kwf_Component_FindHome_Root_Domain_Category_Component extends Kwc_Root_Category_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_None_Component',
            'text' => 'Kwc_Basic_Text_Component'
        );
        $ret['generators']['page']['model'] = 'Kwf_Component_FindHome_Root_Domain_Category_Model';
        return $ret;
    }
}
