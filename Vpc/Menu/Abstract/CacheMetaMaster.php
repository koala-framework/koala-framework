<?php
class Vpc_Menu_Abstract_CacheMetaMaster extends Vps_Component_Cache_Meta_Static_Model
{
    public static function getDeleteWhere($pattern, $row, $dirtyColumns, $params)
    {
        $ret = parent::getDeleteWhere($pattern, $row, $dirtyColumns, $params);
        $ret['type'] = 'master';
        return $ret;
    }
}
