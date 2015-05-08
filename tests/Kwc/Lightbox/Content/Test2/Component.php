<?php
class Kwc_Lightbox_Content_Test2_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Lightbox_Content_TestComponent_Component'
        );
        $ret['viewCache'] = false;
        return $ret;
    }
}
