<?php
class Vps_Component_Cache_Meta_Static_ModelPartialId extends Vps_Component_Cache_Meta_Static_ModelPartial
{
    public static function getDeleteWhere($pattern, $row)
    {
        $ret = parent::getDeleteWhere($pattern, $row);
        $ret['value'] = $row->id;
        return $ret;
    }
}