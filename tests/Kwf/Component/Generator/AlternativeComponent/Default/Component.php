<?php
class Vps_Component_Generator_AlternativeComponent_Default_Component extends Vpc_Abstract
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
            'alternative'=>'Vps_Component_Generator_AlternativeComponent_Alternative_Component'
        );
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        $data = $parentData;
        $level = 1;
        while ($data) {
            $data = $data->parent;
            $level++;
        }
        if ($level >= 4) {
            return 'alternative';
        } else {
            return false;
        }
    }
}
