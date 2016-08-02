<?php
abstract class Kwf_Model_Provider_Abstract
{
    public abstract function findModels();

    protected static function _findAllInstancesProcessModel(&$ret, $model)
    {
        $model = Kwf_Model_Abstract::getInstance($model);
        if (isset($ret[$model->getFactoryId()])) {
            return;
        }
        $ret[$model->getFactoryId()] = $model;

        if ($model instanceof Kwf_Model_Proxy) {
            self::_findAllInstancesProcessModel($ret, $model->getProxyModel());
        } else if ($model instanceof Kwf_Model_Union) {
            foreach ($model->getUnionModels() as $subModel) {
                self::_findAllInstancesProcessModel($ret, $subModel);
            }
        }

        foreach ($model->getDependentModels() as $m) {
            self::_findAllInstancesProcessModel($ret, $m);
        }
        foreach ($model->getSiblingModels() as $m) {
            self::_findAllInstancesProcessModel($ret, $m);
        }
        foreach ($model->getReferences() as $rule) {
            $m = $model->getReferencedModel($rule);
            self::_findAllInstancesProcessModel($ret, $m);
        }
    }
}
