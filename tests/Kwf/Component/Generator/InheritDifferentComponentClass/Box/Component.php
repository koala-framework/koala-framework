<?php
class Kwf_Component_Generator_InheritDifferentComponentClass_Box_Component extends Kwc_Abstract
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
            'inherit'=>'Kwf_Component_Generator_InheritDifferentComponentClass_Box_Inherit_Component'
        );
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        $c = $parentData;
        while (!$c->inherits) $c = $c->parent;

        $c = $c->parent;
        if (!$c) return false;
        while (!$c->inherits) $c = $c->parent;

        $instances = Kwf_Component_Generator_Abstract::getInstances($c, array(
                'inherit' => true
        ));
        if (in_array($generator, $instances, true)) {
            //wir wurden geerbt weils über uns ein parentData mit dem gleichen generator gibt
            return 'inherit';
        } else {
            return false;
        }
    }
}
