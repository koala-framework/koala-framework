<?php
class Vps_Component_ComponentLinkModifiers_Root extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page1'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_ComponentLinkModifiers_Page_Component',
            'name' => 'page1'
        );
        $ret['generators']['test'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_ComponentLinkModifiers_TestComponent_Component',
            'name' => 'test'
        );
        return $ret;
    }

}
