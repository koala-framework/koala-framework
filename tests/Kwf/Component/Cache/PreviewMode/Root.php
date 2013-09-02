<?php
class Kwf_Component_Cache_PreviewMode_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_PreviewMode_Test_Component',
        );
        return $ret;
    }
}
