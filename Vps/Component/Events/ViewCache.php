<?php
class Vps_Component_Events_ViewCache extends Vps_Component_Events
{
    private $_updatedDbIds = array();
    private $_modelClass = 'Vps_Component_Cache_Mysql_Model';

    public static function setModelClass($modelClass)
    {
        $instance = Vps_Component_Events_ViewCache::getInstance('Vps_Component_Events_ViewCache');
        $instance->_modelClass = $modelClass;
    }

    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'event' => Vps_Component_Events::EVENT_COMPONENT_CONTENT_CHANGE,
            'callback' => 'onContentChange'
        );
        $ret[] = array(
            'event' => Vps_Component_Events::EVENT_ROW_UPDATES_FINISHED,
            'callback' => 'onRowUpdatesFinished'
        );
        return $ret;
    }

    public function onContentChange($event, $data)
    {
        $this->_updatedDbIds[] = $data;
    }

    public function onRowUpdatesFinished($event, $data)
    {
        $model = Vps_Model_Abstract::getInstance($this->_modelClass);
        $select = $model->select()->whereEquals('db_id', array_unique($this->_updatedDbIds));
        foreach ($model->export(Vps_Model_Abstract::FORMAT_ARRAY, $select) as $row) {
            $cacheId = $this->_getCacheId($row['component_id'], 'component', null);
            apc_delete($cacheId);
        }
        $model->updateRows(array('deleted' => true), $select);
    }

    protected static function _getCacheId($componentId, $type, $value)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix() . '-cc-';
        return $prefix . "$componentId/$type/$value";
    }
}
