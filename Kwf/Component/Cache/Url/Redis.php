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
        $this->_redis->sAdd('urlids:pageid:'.$data->page_id, 'url:'.$cacheUrl);
        $this->_redis->sAdd('urlids:expandedid:'.$data->page_id, 'url:'.$cacheUrl);

        $parts = preg_split('/([_\-])/', $data->getExpandedComponentId(), -1, PREG_SPLIT_DELIM_CAPTURE);
        $id = '';
        foreach ($parts as $part) {
            $id .= $part;
            if ($part != '-' && $part != '_' && $id != 'root') {
                $this->_redis->sAdd('urlids:recexpandedid:'.$id, $key);
            }
        }

        $this->_redis->set('url:'.$cacheUrl, $data->kwfSerialize());
        $this->_redis->expire('url:'.$cacheUrl, 365*24*60*60);
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
        $this->_redis->delete($this->_redis->keys('url:*'));
    }
}
