<?php
class Kwf_Component_Cache_Mysql extends Kwf_Component_Cache
{
    protected $_models;

    public function __construct()
    {
        $this->_models = array (
            'cache' => 'Kwf_Component_Cache_Mysql_Model',
            'url' => 'Kwf_Component_Cache_Mysql_UrlModel',
            'includes' => 'Kwf_Component_Cache_Mysql_IncludesModel',
        );
    }

    /**
     * @return Kwf_Model_Abstract
     */
    public function getModel($type = 'cache')
    {
        if (!isset($this->_models[$type])) return null;
        if (is_string($this->_models[$type])) {
            $this->_models[$type] = Kwf_Model_Abstract::getInstance($this->_models[$type]);
        }
        return $this->_models[$type];
    }

    public function save(Kwf_Component_Data $component, $content, $renderer='component', $type = 'component', $value = '', $lifetime = null)
    {
        // MySQL
        $data = array(
            'component_id' => (string)$component->componentId,
            'db_id' => (string)$component->dbId,
            'page_db_id' => (string)$component->getPageOrRoot()->dbId,
            'expanded_component_id' => (string)$component->getExpandedComponentId(),
            'component_class' => $component->componentClass,
            'renderer' => $renderer,
            'type' => $type,
            'value' => (string)$value,
            'expire' => is_null($lifetime) ? null : time() + $lifetime,
            'deleted' => false,
            'content' => $content
        );
        $options = array(
            'buffer' => true,
            'replace' => true,
            'skipModelObserver' => true
        );
        $this->getModel('cache')->import(Kwf_Model_Abstract::FORMAT_ARRAY, array($data), $options);

        // APC
        $cacheId = $this->_getCacheId($component->componentId, $renderer, $type, $value);
        $ttl = null;
        if ($lifetime) $ttl = $lifetime;
        Kwf_Component_Cache_Memory::getInstance()->save($content, $cacheId, $ttl);
        return true;
    }

    public function load($componentId, $renderer='component', $type = 'component', $value = '')
    {
        $data = $this->loadWithMetadata($componentId, $renderer, $type, $value);
        if ($data === null) return $data;
        return $data['contents'];
    }

    public function loadWithMetadata($componentId, $renderer='component', $type = 'component', $value = '')
    {
        if ($componentId instanceof Kwf_Component_Data) {
            $componentId = $componentId->componentId;
        }
        $cacheId = $this->_getCacheId($componentId, $renderer, $type, $value);
        $data = Kwf_Component_Cache_Memory::getInstance()->loadWithMetaData($cacheId);
        if ($data === false) {
            Kwf_Benchmark::count('comp cache mysql');
            $select = $this->getModel('cache')->select()
                ->whereEquals('component_id', $componentId)
                ->whereEquals('renderer', $renderer)
                ->whereEquals('type', $type)
                ->whereEquals('deleted', false)
                ->whereEquals('value', $value)
                ->where(new Kwf_Model_Select_Expr_Or(array(
                    new Kwf_Model_Select_Expr_Higher('expire', time()),
                    new Kwf_Model_Select_Expr_IsNull('expire'),
                )));
            $options = array(
                'columns' => array('content', 'expire'),
            );
            $row = $this->getModel('cache')->export(Kwf_Model_Db::FORMAT_ARRAY, $select, $options);
            if (isset($row[0])) {
                Kwf_Benchmark::countLog('viewcache-db');
                $ttl = null;
                if ($row[0]['expire']) {
                    $ttl = $row[0]['expire']-time();
                }
                Kwf_Component_Cache_Memory::getInstance()->save($row[0]['content'], $cacheId, $ttl);
                $data = array(
                    'contents' => $row[0]['content'],
                    'expire' => $row[0]['expire']
                );
            } else {
                Kwf_Benchmark::countLog('viewcache-miss');
                $data = null;
            }
        } else {
            Kwf_Benchmark::countLog('viewcache-mem');
        }

        return $data;
    }

    public function deleteViewCache($select)
    {
        $select->whereEquals('deleted', false);
        $model = $this->getModel();
        $log = Kwf_Component_Events_Log::getInstance();
        $cacheIds = array();
        $options = array(
            'columns' => array('component_id', 'renderer', 'type', 'value'),
        );
        $partialIds = array();
        $deleteIds = array();
        $checkIncludeIds = array();
        foreach ($model->export(Kwf_Model_Abstract::FORMAT_ARRAY, $select, $options) as $row) {
            $cacheIds[] = $this->_getCacheId($row['component_id'], $row['renderer'], $row['type'], $row['value']);
            Kwf_Benchmark::countLog('viewcache-delete-'.$row['type']);
            if ($row['type'] != 'fullPage' && !in_array($row['component_id'], $checkIncludeIds)) {
                $checkIncludeIds[] = $row['component_id'];
            }
            if ($log) {
                $log->log("delete view cache $row[component_id] $row[renderer] $row[type] $row[value]", Zend_Log::INFO);
            }
            $type = $row['type'];
            $value = $row['value'];
            $cId = $row['component_id'];
            if ($type == 'partial' && $value != '') {
                if (!isset($partialIds[$cId])) $partialIds[$cId] = array();
                $partialIds[$cId][] = $value;
            } else if ($value == '') {
                if (!isset($deleteIds[$type])) $deleteIds[$type] = array();
                $deleteIds[$type][] = $cId;
            } else {
                throw new Kwf_Exception('Should not happen.');
            }
        }
        foreach ($partialIds as $componentId => $values) {
            $select = $model->select();
            $select->where(new Kwf_Model_Select_Expr_And(array(
                new Kwf_Model_Select_Expr_Equals('component_id', $componentId),
                new Kwf_Model_Select_Expr_Equals('type', 'partial'),
                new Kwf_Model_Select_Expr_Equals('value', $values)
            )));
            $model->updateRows(array('deleted' => true), $select);
        }
        foreach ($deleteIds as $type => $componentIds) {
            $select = $model->select();
            $select->where(new Kwf_Model_Select_Expr_And(array(
                new Kwf_Model_Select_Expr_Equals('component_id', $componentIds),
                new Kwf_Model_Select_Expr_Equals('type', $type)
            )));
            $model->updateRows(array('deleted' => true), $select);
        }

        foreach ($cacheIds as $cacheId) {
            Kwf_Component_Cache_Memory::getInstance()->remove($cacheId);
        }

        $s = new Kwf_Model_Select();
        $s->whereEquals('type', 'fullPage');
        if ($checkIncludeIds) {
            $ids = array_keys($this->_fetchIncludesTree($checkIncludeIds));
            if ($ids) {
                $s->whereEquals('component_id', $ids);
                if ($log) {
                    foreach ($ids as $id) {
                        $log->log("type=fullPage component_id={$id}", Zend_Log::INFO);
                    }
                }
                $this->deleteViewCache($s);
            }
        }

        file_put_contents('log/clear-view-cache', date('Y-m-d H:i:s').' '.round(microtime(true)-Kwf_Benchmark::$startTime, 2).'s; '.Kwf_Component_Events::$eventsCount.' events; '.count($deleteIds).' view cache entries deleted; '.(isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'')."\n", FILE_APPEND);

        return count($cacheIds);
    }

    private function _fetchIncludesTree($componentIds, &$checkedIds = array())
    {
        $ret = array();
        $ids = array();

        foreach ($componentIds as $componentId) {

            $i = $componentId;
            if (!isset($checkedIds[$i])) {
                $checkedIds[$i] = true;
                $ids[] = $i;
            }
            while (strrpos($i, '-') && strrpos($i, '-') > strrpos($i, '_')) {
                $i = substr($i, 0, strrpos($i, '-'));
                if (!isset($checkedIds[$i])) {
                    $checkedIds[$i] = true;
                    $ids[] = $i;
                }
            }

            $ret[$i] = true;

        }

        if (!$ids) {
            return $ret;
        }

        $s = new Kwf_Model_Select();
        $s->whereEquals('target_id', $ids);
        $imports = Kwf_Component_Cache::getInstance()
            ->getModel('includes')
            ->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s, array('columns'=>array('component_id')));
        $childIds = array();
        foreach ($imports as $row) {
            $childIds[] = $row['component_id'];
        }
        $childIds = array_unique($childIds);

        foreach ($this->_fetchIncludesTree($childIds, $checkedIds) as $i=>$nop) {
            if (!isset($ret[$i])) {
                $ret[$i] = true;
            }
        }

        return $ret;
    }
    protected static function _getCacheId($componentId, $renderer, $type, $value)
    {
        return "cc_".str_replace('-', '__', $componentId)."_{$renderer}_{$type}_{$value}";
    }

    public static function getCacheId($componentId, $renderer, $type, $value)
    {
        return self::_getCacheId($componentId, $renderer, $type, $value);
    }
}
