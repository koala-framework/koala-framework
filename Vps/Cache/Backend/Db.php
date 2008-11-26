<?php
class Vps_Cache_Backend_Db extends Zend_Cache_Backend
{
    /**
     * Available options
     *
     * =====> (string) table :
     * - tablename
     *
     * =====> (??) adapter :
     *
     * @var array Available options
     */
    protected $_options = array(
        'table' => null,
        'adapter' => null
    );

    /**
     * DB adapter
     *
     * @var mixed $_adapter
     */
    private $_adapter = null;

    public function __construct($options = array())
    {
        parent::__construct($options);
        if (is_null($this->_options['table'])) {
            Zend_Cache::throwException('table option has to set');
        }
        $this->_adapter = $this->_options['adapter'];
    }

    public function load($id, $doNotTestCacheValidity = false)
    {
        $sql = "SELECT content FROM {$this->_options['table']} WHERE id='$id'";
        if (!$doNotTestCacheValidity) {
            $sql = $sql . " AND (expire=0 OR expire>" . time() . ')';
        }
        $stmt = $this->_adapter->query($sql);
        $ret = $stmt->fetchColumn();
        return $ret;
    }

    public function test($id)
    {
        $sql = "SELECT last_modified FROM {$this->_options['table']} WHERE id='$id' AND (expire=0 OR expire>" . time() . ')';
        $stmt = $this->_adapter->query($sql);
        return $stmt->fetchColumn();
    }

    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $lifetime = $this->getLifetime($specificLifetime);
        $mktime = time();
        if (is_null($lifetime)) {
            $expire = 0;
        } else {
            $expire = $mktime + $lifetime;
        }

        $componentClass = isset($tags['componentClass']) ? $tags['componentClass'] : '';
        $pageId = isset($tags['pageId']) ? $tags['pageId'] : '';
        $sql = "REPLACE INTO {$this->_options['table']} (id, content, last_modified, expire, component_class, page_id)";
        $sql .= "VALUES (:id, :data, :mktime, :expire, :componentClass, :pageId)";
        $res = $this->_adapter->query($sql, array(
            'id' => $id,
            'data' => $data,
            'mktime' => $mktime,
            'expire' => $expire,
            'componentClass' => $componentClass,
            'pageId' => $pageId
        ));

        if (!$res) {
            $this->_log("Vps_Cache_Backend_Db::save() : impossible to store the cache id=$id");
            return false;
        }
        return $res;
    }
    public function remove($id)
    {
        return (bool)$this->_adapter->query("DELETE FROM {$this->_options['table']} WHERE id='$id'");
    }

    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        if ($mode == Vps_Component_Cache::CLEANING_MODE_COMPONENT_CLASS) {
            if (!is_string($tags)) {
                throw new Vps_Exception("second argument must be a component class name");
            }
            $res = $this->_adapter->query("DELETE FROM {$this->_options['table']} WHERE component_class=?", array($tags));
            return (bool)$res;
        }
        if ($mode == Vps_Component_Cache::CLEANING_MODE_ID_PATTERN) {
            if (!is_array($tags) || !isset($tags['idPattern'])) {
                throw new Vps_Exception("second argument must be an array");
            }
            $tags['idPattern'] = str_replace('_', '\_', $tags['idPattern']);
            $vars = array($tags['idPattern'], $tags['idPattern'] . '%\_\_master');
            $sql = "DELETE FROM {$this->_options['table']} WHERE id LIKE ? AND id NOT LIKE ?";
            if (isset($tags['componentClass']) && $tags['componentClass']) {
                $sql .= " AND component_class=?";
                $vars[] = $tags['componentClass'];
            }
            $res = $this->_adapter->query($sql, $vars);
            return (bool)$res;
        }
        if ($mode==Vps_Component_Cache::CLEANING_MODE_SELECT) {
            if ($tags instanceof Zend_Db_Select) {
                $tags = $tags->__toString();
            }
            $sql = "DELETE FROM {$this->_options['table']}";
            $sql .= " WHERE id IN (".$tags.")";
            $res = $this->_adapter->query($sql);
            return (bool)$res;
        }
        if ($mode==Zend_Cache::CLEANING_MODE_ALL) {
            $res = $this->_adapter->query("DELETE FROM {$this->_options['table']}");
            return (bool)$res;
        }
        if ($mode==Zend_Cache::CLEANING_MODE_OLD) {
            $mktime = time();
            $res = $this->_adapter->query("DELETE FROM {$this->_options['table']} WHERE expire>0 AND expire<=$mktime");
            return (bool)$res;
        }
        if ($mode==Zend_Cache::CLEANING_MODE_MATCHING_TAG) {
            $this->_log("Vps_Cache_Backend_Db::clean() : tags are unsupported by the Db backend");
        }
        if ($mode==Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG) {
            $this->_log("Vps_Cache_Backend_Db::clean() : tags are unsupported by the Db backend");
        }
        return false;
    }
}
