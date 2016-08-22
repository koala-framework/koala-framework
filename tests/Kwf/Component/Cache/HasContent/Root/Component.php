<?php
class Kwf_Component_Cache_HasContent_Root_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_HasContent_Root_Child_Component',
            'name' => 'child'
        );
        return $ret;
    }
}
