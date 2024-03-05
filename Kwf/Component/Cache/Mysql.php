<?php
class Kwf_Component_Cache_Mysql extends Kwf_Component_Cache
{
    protected $_models;

    public function __construct()
    {
        $this->_models = array (
            'cache' => 'Kwf_Component_Cache_Mysql_Model',
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

    public function save(Kwf_Component_Data $component, $content, $renderer, $type, $value, $tag, $lifetime)
    {
        $microtime = $this->_getMicrotime();
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
            'tag' => (string)$tag,
            'microtime' => $microtime,
            'expire' => is_null($lifetime) ? null : time() + $lifetime,
            'deleted' => false,
            'content' => $content,
            'url' => $type == 'fullPage' ? $component->url : null,
            'domain_component_id' => $type == 'fullPage' ? $component->getDomainComponentId() : null,
        );
        $options = array(
            'replace' => true,
            'skipModelObserver' => true
        );
        $this->getModel('cache')->import(Kwf_Model_Abstract::FORMAT_ARRAY, array($data), $options);

        // APC
        $cacheId = $this->_getCacheId($component->componentId, $renderer, $type, $value);
        $ttl = null;
        if ($lifetime) $ttl = $lifetime;
        Kwf_Component_Cache_Memory::getInstance()->save($content, $cacheId, $ttl, $microtime);
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
        if ($data === false || !is_array($data)) {
            Kwf_Benchmark::count('comp cache mysql');
            $select = $this->getModel('cache')->select()
                ->whereEquals('component_id', (string)$componentId)
                ->whereEquals('renderer', $renderer)
                ->whereEquals('type', $type)
                ->whereEquals('deleted', false)
                ->whereEquals('value', $value)
                ->where(new Kwf_Model_Select_Expr_Or(array(
                    new Kwf_Model_Select_Expr_Higher('expire', time()),
                    new Kwf_Model_Select_Expr_IsNull('expire'),
                )));
            if ($data !== false) {
                $select->where(new Kwf_Model_Select_Expr_Higher('microtime', $data));
            }
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

    public function countViewCacheEntries($updates)
    {
        $select = $this->_buildSelectForDelete($updates);
        return $this->getModel()->countRows($select);
    }

    protected function _buildSelectForDelete($updates)
    {
        $or = array();
        foreach ($updates as $key => $values) {
            if ($key === 'component_id') {
                $or[] = new Kwf_Model_Select_Expr_And(array(
                    new Kwf_Model_Select_Expr_Equal('component_id', array_unique($values)),
                    new Kwf_Model_Select_Expr_Equal('type', 'component'),
                ));
            } else if ($key === 'master-component_id') {
                $or[] = new Kwf_Model_Select_Expr_And(array(
                    new Kwf_Model_Select_Expr_Equal('component_id', array_unique($values)),
                    new Kwf_Model_Select_Expr_Equal('type', 'master'),
                ));
            } else {
                $and = array();
                foreach ($values as $k => $v) {
                    if (!is_array($v)) $v = array($v);

                    $ors = array();
                    foreach ($v as $value) {
                        if (substr($value, -1) == '%'                   //ends with %
                            && strpos($value, '%') === strlen($value)-1 //ending % is the only %
                            && in_array($k, array('component_id', 'db_id', 'expanded_component_id'))
                        ) {
                            $value = substr($value, 0, -1);
                            $ors[] = new Kwf_Model_Select_Expr_Equal($k, $value);
                            $ors[] = new Kwf_Model_Select_Expr_Like($k, $value.'-%');
                            $ors[] = new Kwf_Model_Select_Expr_Like($k, $value.'_%');
                        } else if (strpos($value, '%') !== false) {
                            $ors[] = new Kwf_Model_Select_Expr_Like($k, $value);
                        } else {
                            $ors[] = new Kwf_Model_Select_Expr_Equal($k, $value);
                        }
                    }
                    $and[] = new Kwf_Model_Select_Expr_Or($ors);
                }
                if ($and) {
                    $and = new Kwf_Model_Select_Expr_And($and);
                    if (!in_array($and, $or)) {
                        $or[] = $and;
                    }
                }
            }
        }
        $select = new Kwf_Model_Select();
        if ($or) {
            $select->where($or[0]);
            unset($or[0]);
            foreach ($or as $i) {
                $s = new Kwf_Model_Select();
                $s->where($i);
                $select->union($s);
            }
        }
        $select->whereEquals('deleted', false);
        return $select;
    }

    public function deleteViewCache(array $updates, $progressBarAdapter = null)
    {
        $select = $this->_buildSelectForDelete($updates);
        return $this->_deleteViewCacheBySelect( $select, $progressBarAdapter);
    }

    private function _deleteViewCacheBySelect(Kwf_Model_Select $select, $progressBarAdapter = null)
    {
        //execute select
        $microtime = $this->_getMicrotime();
        $model = $this->getModel();
        $log = Kwf_Events_Log::getInstance();
        $cacheIds = array();
        $options = array(
            'columns' => array('component_id', 'renderer', 'type', 'value', 'domain_component_id', 'url'),
        );
        $partialIds = array();
        $deleteIds = array();
        $checkIncludeIds = array();
        $rows = $model->export(Kwf_Model_Abstract::FORMAT_ARRAY, $select, $options);
        $progress = null;
        if ($progressBarAdapter) {
            $count = count($rows);
            $steps = (int)((count($rows) * 2)/100 + 3);
            $step = 0;
            $progress = new Zend_ProgressBar($progressBarAdapter, 0, $steps);
        }
        $fullPageUrls = array();
        foreach ($rows as $key => $row) {
            if ($progress && ($key%100) == 0) {
                $step += 100;
                $progress->next(1, "viewcache $step / $count");
            }
            $cacheIds[] = $this->_getCacheId($row['component_id'], $row['renderer'], $row['type'], $row['value']);
            Kwf_Benchmark::countLog('viewcache-delete-'.$row['type']);
            if ($row['type'] != 'fullPage' && !isset($checkIncludeIds[$row['component_id']])) {
                $checkIncludeIds[$row['component_id'].':'.$row['type']] = true;
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
            if ($type == 'fullPage') {
                if (!isset($fullPageUrls[$row['domain_component_id']])) $fullPageUrls[$row['domain_component_id']] = array();
                $fullPageUrls[$row['domain_component_id']][$row['component_id']] = $row['url'];
            }
        }

        // Memcache
        $this->_beforeMemcacheDelete($select); // For unit testing - DO NOT DELETE!
        if ($progress) { $step = 0; }
        foreach ($cacheIds as $key => $cacheId) {
            if ($progress && ($key%100) == 0) {
                $step += 100;
                $progress->next(1, "memcache $step / $count");
            }
            Kwf_Component_Cache_Memory::getInstance()->remove($cacheId, $microtime);
        }
        $this->_afterMemcacheDelete($select); // For unit testing - DO NOT DELETE!

        // FullPage
        if ($progress) { $progress->next(1, "fullPage"); }
        $s = new Kwf_Model_Select();
        $s->whereEquals('type', 'fullPage');
        if ($checkIncludeIds) {
            $ids = array_keys($this->_fetchIncludesTree(array_keys($checkIncludeIds)));
            if ($ids) {
                foreach ($ids as &$id) {
                    $id = (string)$id;
                }
                unset($id);
                $s->whereEquals('component_id', $ids);
                if ($log) {
                    foreach ($ids as $id) {
                        $log->log("type=fullPage component_id={$id}", Zend_Log::INFO);
                    }
                }
                $this->_deleteViewCacheBySelect($s);
            }
        }

        // Database
        $this->_beforeDatabaseDelete($select); // For unit testing - DO NOT DELETE!
        $deletedCount = 0;
        if ($progress) { $progress->next(1, "partialIds"); }
        foreach ($partialIds as $componentId => $values) {
            $deletedCount += count($values);
            $select = $model->select();
            $select->where(new Kwf_Model_Select_Expr_And(array(
                new Kwf_Model_Select_Expr_Equals('component_id', (string)$componentId),
                new Kwf_Model_Select_Expr_Equals('type', 'partial'),
                new Kwf_Model_Select_Expr_Equals('value', $values),
                new Kwf_Model_Select_Expr_LowerEqual('microtime', $microtime)
            )));
            $model->updateRows(array('deleted' => true), $select);
        }
        if ($progress) { $progress->next(1, "deleteIds"); }
        foreach ($deleteIds as $type => $componentIds) {
            $deletedCount += count($componentIds);
            $select = $model->select();
            $select->where(new Kwf_Model_Select_Expr_And(array(
                new Kwf_Model_Select_Expr_Equals('component_id', $componentIds),
                new Kwf_Model_Select_Expr_Equals('type', $type),
                new Kwf_Model_Select_Expr_LowerEqual('microtime', $microtime)
            )));
            $model->updateRows(array('deleted' => true), $select);
        }

        if ($fullPageUrls) {
            foreach ($fullPageUrls as $domainComponentId=>$urls) {
                Kwf_Events_Dispatcher::fireEvent(new Kwf_Component_Event_ViewCache_ClearFullPage(get_class($this), $domainComponentId, $urls));
            }
        }

        $this->_afterDatabaseDelete($select); // For unit testing - DO NOT DELETE!

        if ($progress) $progress->finish();
        file_put_contents('log/clear-view-cache', date('Y-m-d H:i:s').' '.round(microtime(true)-Kwf_Benchmark::$startTime, 2).'s; '.Kwf_Events_Dispatcher::$eventsCount.' events; '.$deletedCount.' view cache entries deleted; '.(isset($_SERVER['REQUEST_URI'])?substr($_SERVER['REQUEST_URI'], 0, 100):'')."\n", FILE_APPEND);
        return count($cacheIds);
    }

    private function _fetchIncludesTree($componentIds, &$checkedIds = array())
    {
        $ret = array();
        $ids = array();

        foreach ($componentIds as $componentId) {

            $i = (string)$componentId;
            $type = substr($i, strrpos($i, ':')+1);
            $i = substr($i, 0, strrpos($i, ':'));
            if (!isset($checkedIds[$i.':'.$type])) {
                $checkedIds[$i.':'.$type] = true;
                $ids[] = $i.':'.$type;
            }
            while (strrpos($i, '-') && strrpos($i, '-') > strrpos($i, '_')) {
                $i = substr($i, 0, strrpos($i, '-'));
                if (!isset($checkedIds[$i.':'.$type])) {
                    $checkedIds[$i.':'.$type] = true;
                    $ids[] = $i.':'.$type;
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
            ->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s, array('columns'=>array('component_id', 'target_id', 'type')));
        $childIds = array();
        $childIdsDontRecurse = array();
        foreach ($imports as $row) {
            $childIds[] = $row['component_id'].':'.$row['type'];
        }
        $childIds = array_unique($childIds);

        foreach ($this->_fetchIncludesTree($childIds, $checkedIds) as $i=>$nop) {
            if (!isset($ret[$i])) {
                $ret[$i] = true;
            }
        }

        return $ret;
    }

    private function _getMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return (string)($sec . substr($usec, 2, 4));
    }

    protected static function _getCacheId($componentId, $renderer, $type, $value)
    {
        return "cc_".str_replace('-', '__', $componentId)."_{$renderer}_{$type}_{$value}";
    }

    public static function getCacheId($componentId, $renderer, $type, $value)
    {
        return self::_getCacheId($componentId, $renderer, $type, $value);
    }

    // wird nur von Kwf_Component_View_Renderer->saveCache() verwendet
    public function test($componentId, $renderer, $type = 'component', $value = '')
    {
        return !is_null($this->load($componentId, $renderer, $type, $value));
    }

    protected function _beforeMemcacheDelete($select) {} // For unit testing - DO NOT DELETE!
    protected function _afterMemcacheDelete($select) {} // For unit testing - DO NOT DELETE!
    protected function _beforeDatabaseDelete($select) {} // For unit testing - DO NOT DELETE!
    protected function _afterDatabaseDelete($select) {} // For unit testing - DO NOT DELETE!

    public function saveIncludes($componentId, $type, $includedComponents)
    {
        $m = $this->getModel('includes');
        $s = $m->select()
            ->whereEquals('component_id', $componentId)
            ->whereEquals('type', $type);
        $existingTargetIds = array();
        foreach ($m->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s, array('columns'=>array('id', 'target_id', 'type'))) as $i) {
            $existingTargetIds[$i['id']] = $i['target_id'];
        }
        $newTargetIds = array();
        if ($includedComponents) {
            $data = array();
            foreach ($includedComponents as $includedComponent) {
                $cmp = Kwf_Component_Data_Root::getInstance()
                    ->getComponentById($componentId, array('ignoreVisible' => true));
                $id = substr($includedComponent, 0, strrpos($includedComponent, ':'));
                $targetCmp = Kwf_Component_Data_Root::getInstance()
                    ->getComponentById($id, array('ignoreVisible' => true));
                if ($cmp->getInheritsParent() !== $targetCmp->getInheritsParent()) {
                    if (!in_array($includedComponent, $existingTargetIds)) {
                        $c = array(
                            'target_id' => $includedComponent,
                            'type' => $type,
                            'component_id' => $componentId,
                        );
                        $data[] = $c;
                    }
                    $newTargetIds[] = $includedComponent;
                }
            }
            $m->import(Kwf_Model_Abstract::FORMAT_ARRAY, $data);
        }
        $diffTargetIds = array_diff($existingTargetIds, $newTargetIds);
        if ($diffTargetIds) {
            //delete not anymore included
            $m = $this->getModel('includes');
            $s = $m->select()
                ->whereEquals('component_id', $componentId)
                ->whereEquals('type', $type)
                ->whereEquals('target_id', $diffTargetIds);
            $m->deleteRows($s);
        }
    }

    public function handlePageParentChanges(array $pageParentChanges)
    {
        foreach ($pageParentChanges as $changes) {
            $oldParentId = $changes['oldParentId'];
            $newParentId = $changes['newParentId'];
            $componentId = $changes['componentId'];
            $length = strlen($oldParentId);
            $like = $oldParentId . '_' . $componentId;
            $model = Kwf_Component_Cache::getInstance()->getModel();
            while ($model instanceof Kwf_Model_Proxy) $model = $model->getProxyModel();
            if ($model instanceof Kwf_Model_Db) {
                $db = Kwf_Registry::get('db');
                $newParentId = $db->quote($newParentId);
                $where[] = 'expanded_component_id = ' . $db->quote($like);
                $where[] = 'expanded_component_id LIKE ' . str_replace('_', '\_', $db->quote($like . '-%'));
                $where[] = 'expanded_component_id LIKE ' . str_replace('_', '\_', $db->quote($like . '_%'));
                $sql = "UPDATE cache_component
                    SET expanded_component_id=CONCAT(
                        $newParentId, SUBSTRING(expanded_component_id, $length)
                    )
                    WHERE " . implode(' OR ', $where);
                $model->executeSql($sql);
                $this->_log("expanded_component_id={$like}%->{$newParentId}");
            } else {
                $model = Kwf_Component_Cache::getInstance()->getModel();
                $select = $model->select()->where(
                    new Kwf_Model_Select_Expr_Like('expanded_component_id', $like . '%')
                );
                foreach ($model->getRows($select) as $row) {
                    $oldExpandedId = $row->expanded_component_id;
                    $newExpandedId = $newParentId . substr($oldExpandedId, $length);
                    $row->expanded_component_id = $newExpandedId;
                    $row->save();
                    $this->_log("expanded_component_id={$oldExpandedId}->{$newExpandedId}");
                }
            }
        }
    }

    private function _log($msg)
    {
        $log = Kwf_Events_Log::getInstance();
        if ($log) {
            $log->log("view cache clear $msg", Zend_Log::INFO);
        }
    }

    public function writeBuffer()
    {
        foreach ($this->_models as $m) {
            if (is_object($m)) $m->writeBuffer();
        }
    }

    public function collectGarbage($debug)
    {
        $model = $this->getModel();
        $includesModel = $this->getModel('includes');

        $s = new Kwf_Model_Select();
        $s->whereEquals('deleted', true);
        $s->where(new Kwf_Model_Select_Expr_Lower('microtime', (time()-3*24*60*60)*1000));
        $options = array(
            'columns' => array('component_id')
        );
        if ($debug) {
            echo "querying for garbage in cache_component...\n";
        }
        foreach ($model->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s, $options) as $row) {
            if ($debug) {
                echo "deleting ".$row['component_id']."\n";
            }
            $s = new Kwf_Model_Select();
            $s->whereEquals('component_id', $row['component_id']);
            $model->deleteRows($s);


            $s = new Kwf_Model_Select();
            $s->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Equal('component_id', $row['component_id']),
                new Kwf_Model_Select_Expr_Like('target_id', $row['component_id'].':%'),
            )));
            $includesModel->deleteRows($s);
        }
    }

}
