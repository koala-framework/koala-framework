<?php
class Kwf_Component_Cache_Redis extends Kwf_Component_Cache
{
    protected $_redis;
    public function __construct()
    {
        $this->_redis = new Redis();
        $this->_redis->connect('127.0.0.1', 6379);
    }

    public function save(Kwf_Component_Data $component, $contents, $renderer, $type, $value, $tag, $lifetime)
    {
        $key = self::_getCacheId($component->componentId, $renderer, $type, $value);

        //TODO cleanup unused entries
        $this->_redis->sAdd('viewids:componentid:'.$component->componentId, $key);
        $this->_redis->sAdd('viewids:dbid:'.$component->dbId, $key);
        $this->_redis->sAdd('viewids:pagedbid:'.$component->getPageOrRoot()->dbId, $key);
        $this->_redis->sAdd('viewids:cls:'.$component->componentClass, $key);
        if ($tag) {
            $this->_redis->sAdd('viewids:tag:'.$tag, $key);
        }

        $parts = preg_split('/([_\-])/', $component->getExpandedComponentId(), -1, PREG_SPLIT_DELIM_CAPTURE);
        $id = '';
        foreach ($parts as $part) {
            $id .= $part;
            if ($part != '-' && $part != '_' && $id != 'root') {
                $this->_redis->sAdd('viewids:recexpandedid:'.$id, $key);
            }
        }

        $cacheContent = array(
            'contents' => $contents,
            'expire' => is_null($lifetime) ? null : time()+$lifetime
        );
        $ret = $this->_redis->set($key, serialize($cacheContent));
        if (is_null($lifetime)) {
            //Set a TTL for view contents http://stackoverflow.com/questions/16370278/how-to-make-redis-choose-lru-eviction-policy-for-only-some-of-the-keys
            $this->_redis->expire($key, 365*24*60*60);
        } else {
            $this->_redis->expire($key, $lifetime);
        }

        return $ret;
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
        $key = self::_getCacheId($componentId, $renderer, $type, $value);
        $ret = unserialize($this->_redis->get($key));
        return $ret;
    }

    private function _deleteViewCache(array $updates, $dryRun)
    {
        $sets = array(
            'component_id' => array(
                'prefix' => 'viewids:componentid:'
            ),
            'db_id' => array(
                'prefix' => 'viewids:dbid:'
            ),
            'page_db_id' => array(
                'prefix' => 'viewids:pagedbid:'
            ),
            'component_class' => array(
                'prefix' => 'viewids:cls:'
            ),
            'tag' => array(
                'prefix' => 'viewids:tag:'
            ),
            'expanded_component_id' => array(
                'prefix' => 'viewids:expandedid:'
            ),

            'type' => array(
                'filterClientSide' => true
            )
        );
        if (isset($updates['component_id'])) {
            $updates[] = array(
                'component_id' => $updates['component_id'],
                'type' => 'component'
            );
            unset($updates['component_id']);
        }
        if (isset($updates['master-component_id'])) {
            $updates[] = array(
                'component_id' => $updates['master-component_id'],
                'type' => 'master'
            );
            unset($updates['master-component_id']);
        }

        $ret = 0;
        $checkIncludeIds = array();
        foreach ($updates as $update) {
            $keys = array();
            foreach ($update as $key=>$value) {

                if (!isset($sets[$key])) {
                    throw new Kwf_Exception("Unsupported updates key '$key'");
                }
                if (isset($sets[$key]['filterClientSide'])) {
                    //not in redis query, handled below
                    continue;
                }
                if (!is_array($value)) {
                    if ($key == 'expanded_component_id') {
                        if (substr($value, -1) != '%') {
                            throw new Kwf_Exception("'$key' must have % at the end");
                        }
                    } else if (strpos($value, '%') !== false) {
                        throw new Kwf_Exception("Unsupported % for key '$key'");
                    }
                    $keys[] = $sets[$key]['prefix'].substr($value, 0, -1);
                } else {
                    $tempKey = 'temp:'.md5(implode(':', $value));

                    $args = array(
                        $tempKey //1st arg: destination
                    );
                    foreach ($value as $i) {
                        $args[] = $sets[$key]['prefix'].$i; //key
                    }
                    call_user_func_array(array($this->_redis, 'sUnionStore'), $args);
                    $this->_redis->expire($tempKey, 20);
                    $keys[] = $tempKey;
                }
            }

            if ($update === array()) {
                //using keys command here is ok as that happens only when executing "clear-view-cache --all" on cli
                $keysToDelete = $this->_redis->keys('viewcache:*');
            } else {
                $keysToDelete = $this->_redis->sInter($keys);
            }

            if ($keysToDelete) {
                foreach ($keysToDelete as $keyIndex=>$key) {
                    $key = self::_parseCacheId($key);
                    if (isset($update['type'])) {
                        //not in redis query
                        if ($key['type'] != $update['type']) {
                            unset($keysToDelete[$keyIndex]);
                        }
                    }
                    if ($key['type'] != 'fullPage') {
                        $checkIncludeIds[$key['componentId'].':'.$key['type']] = true;
                    }
                }
                if (!$dryRun) {
                    $ret += $this->_redis->delete($keysToDelete);
                } else {
                    foreach ($keysToDelete as $i) {
                        $ret += $this->_redis->exists($i);
                    }
                }

            }

        }

        // FullPage
        if (!$dryRun && $checkIncludeIds) {
            $ids = array_keys($this->_fetchIncludesTree(array_keys($checkIncludeIds)));
            if ($ids) {
                $keys = array();
                foreach ($ids as $id) {
                    $keys[] = 'viewids:componentid:'.$id;
                }
                $fullPageKeysToDelete = array();
                foreach (call_user_func_array(array($this->_redis, 'sUnion'), $keys) as $i) {
                    $parts = self::_parseCacheId($i);
                    if ($parts['type'] == 'fullPage') {
                        $fullPageKeysToDelete[] = $i;
                    }
                }
                $this->_redis->delete($fullPageKeysToDelete);
            }
        }
        return $ret;
    }

    public function countViewCacheEntries($updates)
    {
        return $this->_deleteViewCache($updates, true);
    }

    public function deleteViewCache(array $updates, $progressBarAdapter = null)
    {
        return $this->_deleteViewCache($updates, false);
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

        $keys = array();
        foreach ($ids as $id) {
            $keys[] = 'viewincludes:'.$id;
        }
        $childIds = call_user_func(array($this->_redis, 'sInter'), $keys);

        foreach ($this->_fetchIncludesTree($childIds, $checkedIds) as $i=>$nop) {
            if (!isset($ret[$i])) {
                $ret[$i] = true;
            }
        }

        return $ret;
    }

    protected static function _parseCacheId($key)
    {
        $key = explode(':', $key);
        return array(
            'componentId' => $key[1],
            'renderer' => $key[2],
            'type' => $key[3],
            'value' => $key[4],
        );
    }

    protected static function _getCacheId($componentId, $renderer, $type, $value)
    {
        return 'viewcache:'.$componentId.':'.$renderer.':'.$type.':'.$value;
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

    public function saveIncludes($componentId, $type, $includedComponents)
    {
        $existingTargetIds = $this->_redis->sMembers('viewincludes:'.$componentId.':'.$type);

        $diffTargetIds = array_diff($includedComponents, $existingTargetIds);
        if ($diffTargetIds) {
            //add new includes
            $this->_redis->sAdd('viewincludes:'.$componentId.':'.$type, $diffTargetIds);
        }

        $diffTargetIds = array_diff($existingTargetIds, $includedComponents);
        if ($diffTargetIds) {
            //delete not anymore included
            $this->_redis->sRem('viewincludes:'.$componentId.':'.$type, $diffTargetIds);
        }
    }

    public function handlePageParentChanges(array $pageParentChanges)
    {
        foreach ($pageParentChanges as $changes) {
            $pattern = "viewids:expandedid:$changes[oldParentId]_$changes[componentId]*";
            $it = null;
            while ($keys = $this->_redis->scan($it, $pattern)) {
                foreach ($keys as $key) {
                    $newKey = $changes['newParentId'].substr($key, strlen("viewids:expandedid:$changes[oldParentId]"));
                    $this->_redis->rename($key, $newKey);
                }
            }
        }
    }

    public function collectGarbage($debug)
    {
        $pattern = "viewids:*";
        $it = null;
        while ($keys = $this->_redis->scan($it, $pattern)) {
            foreach ($keys as $key) {
                foreach ($this->_redis->sMembers($key) as $viewId) {
                    if (!$this->_redis->exists($viewId)) {
                        if ($debug) {
                            //echo "removing $viewId from $key\n";
                        }
                        $this->_redis->sRem($key, $viewId);
                    }
                }
            }
        }

    }
}
