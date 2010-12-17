<?php
class Vpc_Basic_ParentContent_CacheMeta extends Vps_Component_Cache_Meta_Static_Model
{
    public static function getDeleteWhere($pattern, $row, $dirtyColumns, $params)
    {
        if (!$dirtyColumns || !in_array('parent_id', $dirtyColumns)) return null;
        $ret = parent::getDeleteWhere($pattern, $row, $dirtyColumns, $params);
        $ret['db_id'] = array();
        foreach (self::_getRecursiveChildIds($row->id, $row->getModel()) as $id) {
            $ret['db_id'][] = $id . '%';
        }
        return $ret;
    }

    private static function _getRecursiveChildIds($id, $model)
    {
        $ret = array($id);
        $childIds =  $model->getIds(
            $model->select()->whereEquals('parent_id', $id)
        );
        foreach ($childIds as $childId) {
            $ret = array_merge($ret, self::_getRecursiveChildIds($childId, $model));
        }
        return $ret;
    }
}
