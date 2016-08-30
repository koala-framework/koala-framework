<?php
class Kwf_Component_OutputReplacePlugin_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['generators']['page']);

        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_OutputReplacePlugin_TestComponent_Component',
        );
        $ret['generators']['test2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_OutputReplacePlugin_TestComponent_Component',
        );
        return $ret;
    }
}
