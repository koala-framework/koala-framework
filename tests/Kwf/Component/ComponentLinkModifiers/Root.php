<?php
class Kwf_Component_ComponentLinkModifiers_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_ComponentLinkModifiers_Page_Component',
            'name' => 'page1'
        );
        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_ComponentLinkModifiers_TestComponent_Component',
            'name' => 'test'
        );
        return $ret;
    }

}
