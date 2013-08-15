<?php
class Kwf_Component_ChainedNeedsParentCc_Master_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_ChainedNeedsParentCc_TestComponent_Component',
        );
        return $ret;
    }
}
