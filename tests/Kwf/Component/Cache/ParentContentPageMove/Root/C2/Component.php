<?php
class Kwf_Component_Cache_ParentContentPageMove_Root_C2_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Basic_ParentContent_Component',
        );
        return $ret;
    }
}
