<?php
class Vps_Component_Generator_AlternativeComponent_Default_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['alternativeComponent'] = 'Vps_Component_Generator_AlternativeComponent_Alternative_Component';
        return $ret;
    }

    public static function useAlternativeComponent($parentData)
    {
        $data = $parentData;
        $level = 1;
        while ($data) {
            $data = $data->parent;
            $level++;
        }
        return $level >= 4;
    }
}
