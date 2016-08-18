<?php
class Kwf_Component_Cache_ParentContentPageMove_Root_C1_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Cache_ParentContentPageMove_Root_C1_Child_Component',
        );
        return $ret;
    }
}
