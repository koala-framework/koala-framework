<?php
abstract class Vps_Component_Cache_Meta_Abstract
{
    const META_TYPE_DEFAULT = 'default';
    const META_TYPE_CALLBACK = 'callback';
    const META_TYPE_CLEANURLCACHE = 'cleanUrlCache';

    public static function getMetaType()
    {
        return self::META_TYPE_DEFAULT;
    }

    /**
     * @return Vps_Model_Abstract
     */
    protected static function _getModel($model)
    {
        if (!is_object($model)) {
            if (is_instance_of($model, 'Vps_Model_Abstract')) {
                $model = Vps_Model_Abstract::getInstance($model);
            } else if (is_instance_of($model, 'Zend_Db_Table_Abstract')) {
                $model = new $model();
            }
        }
        if ($model instanceof Zend_Db_Table_Abstract) {
            $model = new Vps_Model_Db(array(
                'table' => $model
            ));
        }
        if (!$model instanceof Vps_Model_Abstract) {
            throw new Vps_Exception('Model must be instance of Vps_Model_Abstract');
        }
        return $model;
    }

    protected static function _getModelname($model)
    {
        $model = self::_getModel($model);
        if (get_class($model) == 'Vps_Model_Db') $model = $model->getTable();
        return get_class($model);
    }
}