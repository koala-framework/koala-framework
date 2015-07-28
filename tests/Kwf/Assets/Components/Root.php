<?php
class Kwf_Assets_Components_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Assets_Components_TestComponent2_Component',
        );
        $ret['generators']['page3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Assets_Components_TestComponent3_Component',
        );
        return $ret;
    }
}
