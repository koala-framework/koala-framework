<?php
class Vps_Cache_MySql extends Zend_Cache_Backend
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
        $sql = "SELECT lastModified FROM {$this->_options['table']} WHERE id='$id' AND (expire=0 OR expire>" . time() . ')';
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
        $sql = "REPLACE INTO {$this->_options['table']} (id, content, lastModified, expire) VALUES (:id, :data, :mktime, :expire)";
        $res = $this->_adapter->query($sql, array(
            'id' => $id,
            'data' => $data,
            'mktime' => $mktime,
            'expire' => $expire
        ));

        if (!$res) {
            $this->_log("Vps_Cache_Backend_Db::save() : impossible to store the cache id=$id");
            return false;
        }
        if (count($tags) > 0) {
            $this->_log("Vps_Cache_Backend_Db::save() : tags are unsupported by the Db backend");
        }
        return $res;
    }
    public function remove($id)
    {
        return (bool)$this->_adapter->query("DELETE FROM {$this->_options['table']} WHERE id='$id'");
    }

    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
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
