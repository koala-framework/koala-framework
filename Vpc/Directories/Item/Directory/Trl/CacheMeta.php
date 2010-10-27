<?php
class Vpc_Directories_Item_Directory_Trl_CacheMeta extends Vps_Component_Cache_Meta_Static_Model
{
    public static function createComponentId($pattern, $row)
    {
        return $ret;
    }

    public static function getDeleteWhere($pattern, $row)
    {
        $ret = parent::getDeleteWhere($pattern, $row);
        $dbId = $row->component_id;
        $dbId = substr($dbId, 0, max(strrpos($dbId, '-'), strrpos($dbId, '_')));
        $dbId .= '%-view';
        $ret['db_id'] = $dbId;
        return $ret;
    }
}
