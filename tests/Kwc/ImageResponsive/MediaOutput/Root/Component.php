<?php
class Kwc_ImageResponsive_MediaOutput_Root_Component extends Kwc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['imageabstract1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'filename' => 'imageabstract1',
            'name' => 'AbstractImage default (300x200)',
            'component' => 'Kwc_ImageResponsive_MediaOutput_Image_TestComponent'
        );
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        unset($ret['generators']['category']);
        return $ret;
    }
}
