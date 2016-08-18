<?php
class Kwf_Component_Generator_AlternativeComponent_Default_Component extends Kwc_Abstract
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
            'alternative'=>'Kwf_Component_Generator_AlternativeComponent_Alternative_Component'
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
