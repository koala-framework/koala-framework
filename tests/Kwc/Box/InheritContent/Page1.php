<?php
class Kwc_Box_InheritContent_Page1 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Box_InheritContent_Page2',
            'name' => 'page2'
        );
        return $ret;
    }

}
