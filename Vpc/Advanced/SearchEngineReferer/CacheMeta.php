<?php
class Vpc_Advanced_SearchEngineReferer_CacheMeta extends Vps_Component_Cache_Meta_Static_Model
{
    public static function getDeleteWhere($pattern, $row, $dirtyColumns, $params)
    {
        $ret = parent::getDeleteWhere($pattern, $row, $dirtyColumns, $params);
        $dbId = Vps_Component_Data_Root::getInstance()->getComponentById($row->component_id)->dbId;
        $ret['db_id'] = str_replace('{component_id}', $dbId, $pattern);
        return $ret;
    }

}
