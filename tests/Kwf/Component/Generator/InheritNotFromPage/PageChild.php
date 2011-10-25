<?php
class Kwf_Component_Generator_InheritNotFromPage_PageChild extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwf_Component_Generator_Inherit_Box',
            'inherit' => true
        );
        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_InheritNotFromPage_Page2',
            'name' => 'page2'
        );
        return $ret;
    }
}
