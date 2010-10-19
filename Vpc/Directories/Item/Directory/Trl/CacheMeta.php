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
        $componentId = $row->component_id;
        $componentId = substr($componentId, 0, max(strrpos($componentId, '-'), strrpos($componentId, '_')));
        $componentId .= '%-view';
        $ret['componentId'] = $componentId;
        return $ret;
    }
}
