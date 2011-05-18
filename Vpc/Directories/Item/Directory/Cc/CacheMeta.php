<?php
class Vpc_Directories_Item_Directory_Cc_CacheMeta extends Vps_Component_Cache_Meta_Static_Model
{
    public static function getDeleteWhere($pattern, $row, $dirtyColumns, $params)
    {
        $ret = parent::getDeleteWhere($pattern, $row, $dirtyColumns, $params);
        $dbId = $row->component_id;
        $dbId = substr($dbId, 0, max(strrpos($dbId, '-'), strrpos($dbId, '_')));
        $dbId .= '%-view';
        $ret['db_id'] = $dbId;
        return $ret;
    }
}
