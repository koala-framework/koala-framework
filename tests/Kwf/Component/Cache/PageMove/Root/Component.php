<?php
class Kwf_Component_Cache_PageMove_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_PageMove_Root_PagesModel';
        $ret['generators']['page']['component'] = array(
            'c1' => 'Kwc_Basic_Empty_Component',
            'c2' => 'Kwc_Basic_Empty_Component',
            'c3' => 'Kwc_Basic_Empty_Component',
        );
        return $ret;
    }
}
