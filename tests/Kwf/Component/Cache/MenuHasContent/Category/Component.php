<?php
class Kwf_Component_Cache_MenuHasContent_Category_Component extends Kwc_Root_Category_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_MenuHasContent_Category_PagesModel';
        return $ret;
    }
}