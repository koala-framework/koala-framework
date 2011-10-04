<?php
class Vps_Component_Cache_Meta_Static_ModelPartial extends Vps_Component_Cache_Meta_Static_Model
{
    public static function getDeleteWhere($pattern, $row, $dirtyColumns, $params)
    {
        $ret = parent::getDeleteWhere($pattern, $row, $dirtyColumns, $params);
        if (!$ret) return $ret;
        $ret['type'] = array('partial');
        return $ret;
    }
}