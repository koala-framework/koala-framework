<?php
class Kwf_Component_Acl_TestComponent extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['blub'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_None_Component',
        );
        $ret['generators']['pseudoPage'] = array(
            'class' => 'Kwf_Component_Generator_PseudoPage_Static',
            'component' => 'Kwc_Basic_None_Component',
        );
        return $ret;
    }
}
