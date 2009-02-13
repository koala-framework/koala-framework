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

    public function saveMeta($vars)
    {
        $ret = false;
        foreach ($vars as $meta) {
            if ($meta && $meta['model'] && $meta['model'] != 'Vps_Model_FnF') {
                $table = $this->_options['table'] . '_meta';
                // Löschen
                $delete = array();
                foreach ($meta as $k => $d) {
                    $delete[] = $d ? "$k = '$d'" : "ISNULL($k)";
                }
                $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $delete);
                $this->_adapter->query($sql);
                // Einfügen
                $sql = "REPLACE INTO {$table} (model, id, cache_id, component_class)";
                $sql .= "VALUES (:model, :id, :cache_id, :component_class)";
                $this->_adapter->query($sql, array(
                    'model' => $meta['model'],
                    'id' => $meta['id'],
                    'cache_id' => $meta['cache_id'],
                    'component_class' => $meta['component_class']
                ));
            }
        }
    }

    public function remove($id)
    {
        return (bool)$this->_adapter->query("DELETE FROM {$this->_options['table']} WHERE id='$id'");
    }

    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        if (!$this->_adapter) return;
        if ($mode == Vps_Component_Cache::CLEANING_MODE_DEFAULT) {
            if (!is_array($tags) || count($tags) != 2) {
                throw new Vps_Exception("second argument must be an array containing model an id");
            }
            $sql = "SELECT cache_id, component_class FROM {$this->_options['table']}_meta WHERE model=? AND (ISNULL(id) OR id=?)";
            foreach ($this->_adapter->fetchAll($sql, $tags) as $row) {
                if ($row['cache_id']) {
                    $this->clean(Vps_Component_Cache::CLEANING_MODE_ID, $row['cache_id']);
                }
                if ($row['component_class']) {
                    $this->clean(Vps_Component_Cache::CLEANING_MODE_COMPONENT_CLASS, $row['component_class']);
                }
            }
            return true;
        }
        if ($mode == Vps_Component_Cache::CLEANING_MODE_COMPONENT_CLASS) {
            if (!is_string($tags)) {
                throw new Vps_Exception("second argument must be a component class name");
            }
            p("Kompletter Cache für Komponente '$tags' gelöscht.");
            return (bool)$this->_adapter->query("DELETE FROM {$this->_options['table']} WHERE component_class=?", array($tags));
        }
        if ($mode == Vps_Component_Cache::CLEANING_MODE_ID) {
            if (!is_string($tags)) {
                throw new Vps_Exception("second argument must be an id");
            }
            p("Cache für Komponente '$tags' gelöscht.");
            $sql = "DELETE FROM {$this->_options['table']} WHERE id = ?";
            return (bool) $this->_adapter->query($sql, array($tags));
        }
        if ($mode==Zend_Cache::CLEANING_MODE_ALL) {
            $this->_adapter->query("DELETE FROM {$this->_options['table']}_meta");
            return (bool)$this->_adapter->query("DELETE FROM {$this->_options['table']}");
        }
        if ($mode==Zend_Cache::CLEANING_MODE_OLD) {
            $mktime = time();
            return (bool)$this->_adapter->query("DELETE FROM {$this->_options['table']} WHERE expire>0 AND expire<=$mktime");
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
