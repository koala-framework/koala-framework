<?php
class Kwf_Component_Cache_ParentContentPageMove_Root_C3_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Cache_ParentContentPageMove_Root_C3_Child_Component',
        );
        return $ret;
    }
}
