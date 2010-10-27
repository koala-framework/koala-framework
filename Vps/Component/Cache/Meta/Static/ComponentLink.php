<?php
class Vps_Component_Cache_Meta_Static_ComponentLink extends Vps_Component_Cache_Meta_Static_Model
{
    public function __construct($model, $pattern = '{id}')
    {
        parent::__construct($model, $pattern);
    }

    public static function getDeleteWhere($pattern, $row)
    {
        $ret = parent::getDeleteWhere($pattern, $row);
        $ret['type'] = array('componentlink');
        if ($row->getModel() instanceof Vpc_Root_Category_GeneratorModel) {
            $ret['db_id'] = array_merge(
                array($ret['db_id']),
                self::_getRecursiveIds($ret['db_id'], $row->getModel())
            );
        }
        return $ret;
    }

    private static function _getRecursiveIds($parentId, $model)
    {
        $ret = $model->getIds(
            $model->select()->whereEquals('parent_id', $parentId)
        );
        foreach ($ret as $id) {
            $ret = array_merge($ret, self::_getRecursiveIds($id, $model));
        }
        return $ret;
    }
}