<?php
class Kwf_Component_OutputReplacePlugin_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
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
