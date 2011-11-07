<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_TestComponent1_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasAlternativeComponent'] = true;
        return $ret;
    }

    public static function getAlternativeComponents()
    {
        return array(
            'alternative'=>'Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_TestComponent2_Component'
        );
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        return 'alternative';
    }
}
