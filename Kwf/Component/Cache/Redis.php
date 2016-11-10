<?php
class Kwf_Component_Cache_Redis extends Kwf_Component_Cache
{
    protected $_redis;
    public function __construct()
    {
        $this->_redis = Kwf_Cache_Simple::getRedis();
    }

    public function save(Kwf_Component_Data $component, $contents, $renderer, $type, $value, $tag, $lifetime)
    {
        $key = self::_getCacheId($component->componentId, $renderer, $type, $value);

        $setKey = $key; //key that will be stored in sets
        if ($type == 'fullPage') {
            $setKey .= ':'.$component->getDomainComponentId().':'.$component->url;
        }

        $this->_redis->sAdd('viewids:componentid:'.$component->componentId, $setKey);
        $this->_redis->sAdd('viewids:dbid:'.$component->dbId, $setKey);
        $this->_redis->sAdd('viewids:pagedbid:'.$component->getPageOrRoot()->dbId, $setKey);
        $this->_redis->sAdd('viewids:cls:'.$component->componentClass, $setKey);
        if ($tag) {
            $this->_redis->sAdd('viewids:tag:'.$tag, $setKey);
        }

        $parts = preg_split('/([_\-])/', $component->getExpandedComponentId(), -1, PREG_SPLIT_DELIM_CAPTURE);
        $id = '';
        foreach ($parts as $part) {
            $id .= $part;
            if ($part != '-' && $part != '_' && $id != 'root') {
                $this->_redis->sAdd('viewids:recexpandedid:'.$id, $setKey);
            }
        }

        $cacheContent = array(
            'contents' => $contents,
            'expire' => is_null($lifetime) ? null : time()+$lifetime
        );

        if (Kwf_Cache_Simple::getBackend() == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
            $ret = Kwf_Cache_Simple::getMemcache()->set($prefix.$key, $cacheContent, MEMCACHE_COMPRESSED, $lifetime);
        } else {
            if (is_null($lifetime)) {
                //Set a TTL for view contents http://stackoverflow.com/questions/16370278/how-to-make-redis-choose-lru-eviction-policy-for-only-some-of-the-keys
                $lifetime = 365*24*60*60;
            }
            $ret = $this->_redis->setEx($key, $lifetime, serialize($cacheContent));
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

        if (Kwf_Cache_Simple::getBackend() == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
            $ret = Kwf_Cache_Simple::getMemcache()->get($prefix.$key);
        } else {
            $ret = unserialize($this->_redis->get($key));
        }

        return $ret;
    }

    private function _deleteViewCache(array $updates, $dryRun)
    {
        $prefixes = array(
            'component_id' => 'viewids:componentid:',
            'db_id' => 'viewids:dbid:',
            'page_db_id' => 'viewids:pagedbid:',
            'component_class' => 'viewids:cls:',
            'tag' => 'viewids:tag:',
            'expanded_component_id' => 'viewids:recexpandedid:',
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

                if ($key == 'type') {
                    //not in redis query, handled below
                    continue;
                }
                if (!isset($prefixes[$key])) {
                    throw new Kwf_Exception("Unsupported updates key '$key'");
                }
                if (!is_array($value)) {
                    if ($key == 'expanded_component_id') {
                        if (substr($value, -1) != '%') {
                            throw new Kwf_Exception("'$key' must have % at the end");
                        }
                    } else if (strpos($value, '%') !== false) {
                        throw new Kwf_Exception("Unsupported % for key '$key'");
                    }
                    $keys[] = $prefixes[$key].substr($value, 0, -1);
                } else {
                    $tempKey = 'temp:'.md5(implode(':', $value));

                    $args = array(
                        $tempKey //1st arg: destination
                    );
                    foreach ($value as $i) {
                        $args[] = $prefixes[$key].$i; //key
                    }
                    call_user_func_array(array($this->_redis, 'sUnionStore'), $args);
                    $this->_redis->expire($tempKey, 20);
                    $keys[] = $tempKey;
                }
            }

            if ($update === array()) {
                //only when executing "clear-view-cache --all" on cli
                $prefixLength = strlen($this->_redis->_prefix(''));
                $it = null;
                $keysToDelete = array();
                while ($keys = $this->_redis->scan($it, $this->_redis->_prefix('viewcache:*'))) {
                    foreach ($keys as $i) {
                        $keysToDelete[] = substr($i, $prefixLength);
                    }
                }

                $keysToDelete = array_unique($keysToDelete);

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
                    } else {
                        //type == fullPage, in this case $key also contains the url which we don't have in the view cache key, so generate cacheId
                        $keysToDelete[$keyIndex] = self::_getCacheId($key['componentId'], $key['renderer'], $key['type'], $key['value']);
                    }
                }
                if (Kwf_Cache_Simple::getBackend() == 'memcache') {
                    if (!$dryRun) {
                        foreach ($keysToDelete as $key) {
                            Kwf_Cache_Simple::getMemcache()->delete($key);
                        }
                    } else {
                        $ret = count(Kwf_Cache_Simple::getMemcache()->get($keysToDelete));
                    }
                } else {
                    if (!$dryRun) {
                        $ret += $this->_redis->delete($keysToDelete);
                    } else {
                        foreach ($keysToDelete as $i) {
                            $ret += $this->_redis->exists($i);
                        }
                    }
                }

            }

        }

        // FullPage
        $fullPageUrls = array();
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
                        $fullPageKeysToDelete[] = self::_getCacheId($parts['componentId'], $parts['renderer'], $parts['type'], $parts['value']);
                        if (!isset($fullPageUrls[$parts['domainComponentId']])) $fullPageUrls[$parts['domainComponentId']] = array();
                        $fullPageUrls[$parts['domainComponentId']][$parts['componentId']] = $parts['url'];
                    }
                }
                if (Kwf_Cache_Simple::getBackend() == 'memcache') {
                    foreach ($fullPageKeysToDelete as $key) {
                        Kwf_Cache_Simple::getMemcache()->delete($key);
                    }
                } else {
                    $this->_redis->delete($fullPageKeysToDelete);
                }
            }
        }

        if ($fullPageUrls) {
            Kwf_Events_Dispatcher::fireEvent(new Kwf_Component_Event_ViewCache_ClearFullPage(get_class($this), $fullPageUrls));
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
        $ret =  array(
            'componentId' => $key[1],
            'renderer' => $key[2],
            'type' => $key[3],
            'value' => $key[4],
        );
        if (isset($key[5])) $ret['domainComponentId'] = $key[5];
        if (isset($key[6])) $ret['url'] = $key[6];
        return $ret;
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
            $pattern = "viewids:recexpandedid:$changes[oldParentId]_$changes[componentId]*";
            $prefixLength = strlen($this->_redis->_prefix(''));
            $it = null;
            while ($keys = $this->_redis->scan($it, $this->_redis->_prefix($pattern))) {
                foreach ($keys as $key) {
                    $key = substr($key, $prefixLength);
                    $newKey = "viewids:recexpandedid:".$changes['newParentId'].substr($key, strlen("viewids:recexpandedid:$changes[oldParentId]"));
                    $this->_redis->rename($key, $newKey);
                }
            }
        }
    }

    public function collectGarbage($debug)
    {
        $pattern = "viewids:*";
        $prefixLength = strlen($this->_redis->_prefix(''));
        $it = null;
        while ($keys = $this->_redis->scan($it, $this->_redis->_prefix($pattern))) {
            foreach ($keys as $key) {
                $key = substr($key, $prefixLength);
                foreach ($this->_redis->sMembers($key) as $viewId) {
                    if (!$this->_redis->exists($viewId)) {
                        if ($debug) {
                            echo "removing $viewId from $key\n";
                        }
                        $this->_redis->sRem($key, $viewId);
                    }
                }
            }
        }
    }
}
