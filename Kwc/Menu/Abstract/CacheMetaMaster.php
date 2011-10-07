<?php
class Kwc_Menu_Abstract_CacheMetaMaster extends Kwf_Component_Cache_Meta_Static_Model
{
    public static function getDeleteWhere($pattern, $row, $dirtyColumns, $params)
    {
        $ret = parent::getDeleteWhere($pattern, $row, $dirtyColumns, $params);
        $ret['type'] = 'master';
        return $ret;
    }
}
