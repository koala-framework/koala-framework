<?php
class Kwf_Component_Generator_IsVisible_Child extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_Visible_Root_Child_Component'
        );
        return $ret;
    }
}
?>