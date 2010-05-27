<?php
class Vpc_List_Gallery_Trl_Component extends Vpc_Abstract_List_Trl_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }
}
