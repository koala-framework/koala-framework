<?php
class Kwf_Component_Cache_Url_Redis extends Kwf_Component_Cache_Url_Abstract
{
    protected $_redis;
    public function __construct()
    {
        $this->_redis = Kwf_Cache_Simple::getRedis();
    }

    public function save($cacheUrl, Kwf_Component_Data $data)
    {
        $this->_redis->sAdd('urlids:pageid:'.$data->getPage()->componentId, 'url:'.$cacheUrl);
        $this->_redis->sAdd('urlids:expandedid:'.$data->getExpandedComponentId(), 'url:'.$cacheUrl);

        $parts = preg_split('/([_\-])/', $data->getExpandedComponentId(), -1, PREG_SPLIT_DELIM_CAPTURE);
        $id = '';
        foreach ($parts as $part) {
            $id .= $part;
            if ($part != '-' && $part != '_' && $id != 'root') {
                $this->_redis->sAdd('urlids:recexpandedid:'.$id, 'url:'.$cacheUrl);
            }
        }

        $this->_redis->setEx('url:'.$cacheUrl, 365*24*60*60, $data->kwfSerialize());
    }

    public function load($cacheUrl)
    {
        $ret = $this->_redis->get('url:'.$cacheUrl);
        if ($ret) {
            $ret = Kwf_Component_Data::kwfUnserialize($ret);
        }
        return $ret;
    }

    public function delete(array $constraints)
    {
        foreach ($constraints as $constraint) {
            if ($constraint['field'] == 'expanded_page_id') {
                if (substr($contraint['value'], -1) == '%') {
                    $keys = $this->_redis->sMembers('urlids:recexpandedid:'.$contraint['value']);
                } else {
                    $keys = $this->_redis->sMembers('urlids:expandedid:'.$contraint['value']);
                }
            } else if ($constraint['field'] == 'page_id') {
                $keys = $this->_redis->sMembers('urlids:pageid:'.$contraint['value']);
            } else {
                throw new Kwf_Exception("Unknown field");
            }
            if ($keys) {
                $this->_redis->delete($keys);
            }
        }
    }

    public function clear()
    {
        $pattern = "url:*";
        $prefixLength = strlen($this->_redis->_prefix(''));
        $it = null;
        while ($keys = $this->_redis->scan($it, $this->_redis->_prefix($pattern))) {
            foreach ($keys as $k=>$i) {
                $keys[$k] = substr($i, $prefixLength);
            }
            $this->_redis->delete($keys);
        }

        $pattern = "urlids:*";
        $it = null;
        while ($keys = $this->_redis->scan($it, $this->_redis->_prefix($pattern))) {
            foreach ($keys as $k=>$i) {
                $keys[$k] = substr($i, $prefixLength);
            }
            $this->_redis->delete($keys);
        }
    }

    public function collectGarbage($debug)
    {
        $pattern = "urlids:*";
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
