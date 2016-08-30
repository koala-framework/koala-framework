<?php
class Kwf_Component_Cache_ParentContentPageMove_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_ParentContentPageMove_Root_PagesModel';
        $ret['generators']['page']['component'] = array(
            'c1' => 'Kwf_Component_Cache_ParentContentPageMove_Root_C1_Component',
            'c2' => 'Kwf_Component_Cache_ParentContentPageMove_Root_C2_Component',
            'c3' => 'Kwf_Component_Cache_ParentContentPageMove_Root_C3_Component',
        );
        return $ret;
    }
}
