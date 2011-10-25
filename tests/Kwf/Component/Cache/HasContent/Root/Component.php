<?php
class Kwf_Component_Cache_HasContent_Root_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_HasContent_Root_Child_Component',
            'name' => 'child'
        );
        return $ret;
    }
}
