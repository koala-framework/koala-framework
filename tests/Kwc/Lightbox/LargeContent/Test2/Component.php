<?php
class Kwc_Lightbox_LargeContent_Test2_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Lightbox_LargeContent_TestComponent_Component'
        );
        $ret['viewCache'] = false;
        return $ret;
    }
}
