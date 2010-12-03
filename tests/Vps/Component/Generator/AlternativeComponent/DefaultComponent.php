<?php
class Vps_Component_Generator_AlternativeComponent_DefaultComponent extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['alternativeComponent'] = 'Vps_Component_Generator_AlternativeComponent_AlternativeComponent';
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
        return $level >= 3;
    }
}
