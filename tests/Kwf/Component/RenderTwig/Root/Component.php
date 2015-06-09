<?php
class Kwf_Component_RenderTwig_Root_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['testCmp'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_RenderTwig_TestCmp_Component',
        );
        return $ret;
    }
}
