<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        $ret['generators']['test1'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_TestComponent1_Component',
        );
        return $ret;
    }
}