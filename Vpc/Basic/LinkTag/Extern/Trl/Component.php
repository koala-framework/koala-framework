<?php
class Vpc_Basic_LinkTag_Extern_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_Extern_Trl_Data';
        return $ret;
    }

}
