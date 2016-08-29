<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_TestComponent1_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
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
