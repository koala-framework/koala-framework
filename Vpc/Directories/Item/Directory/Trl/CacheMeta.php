<?php
class Vpc_Directories_Item_Directory_Trl_CacheMeta extends Vps_Component_Cache_Meta_Static_Model
{
    public static function createComponentId($pattern, $row)
    {
        $ret = $row->component_id;
        $ret = substr($ret, 0, max(strrpos($ret, '-'), strrpos($ret, '_')));
        $ret .= '%-view';
        return $ret;
    }
}
