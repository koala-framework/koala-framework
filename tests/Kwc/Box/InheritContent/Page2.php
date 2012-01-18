<?php
class Kwc_Box_InheritContent_Page2 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_None_Component',
            'name' => 'page3'
        );
        return $ret;
    }

}
