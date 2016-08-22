<?php
class Kwf_Component_RenderTwig_Root_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['testCmp'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_RenderTwig_TestCmp_Component',
        );
        return $ret;
    }
}
