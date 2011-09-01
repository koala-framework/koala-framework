<?php
class Vps_Component_Cache_Meta_Static_ModelPartialId extends Vps_Component_Cache_Meta_Static_ModelPartial
{
    public static function getDeleteWhere($pattern, $row, $dirtyColumns, $params)
    {
        $ret = parent::getDeleteWhere($pattern, $row, $dirtyColumns, $params);
        if (!$ret) return $ret;
        if ($row->hasColumn('id')) $ret['value'] = $row->id;
        return $ret;
    }
}