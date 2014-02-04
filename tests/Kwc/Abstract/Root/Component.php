<?php
class Kwc_Abstract_Root_Component extends Kwc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['imageabstract1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'filename' => 'imageabstract1',
            'name' => 'AbstractImage default (300x200)',
            'component' => 'Kwc_Abstract_Image_TestComponent'
        );
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
