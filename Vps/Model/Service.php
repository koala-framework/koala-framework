<?php
class Vps_Model_Service extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_Service_Row';
    protected $_client;
    protected $_serverUrl;
    protected $_serverConfig;
    protected $_data = array();

    private $_primaryKey;
    private $_columns;
    private $_supportedImportExportFormatsCache;

    public function __construct(array $config = array())
    {
        if (!empty($config['client'])) {
            $this->_client = $config['client'];
        } else if (!empty($config['serverUrl'])) {
            $this->_serverUrl = $config['serverUrl'];
        } else if (!empty($config['serverConfig'])) {
            $this->_serverConfig = $config['serverConfig'];
        }

        // wenn aus config verwendet, z.B.: service.xxx.url
        if ($this->_serverConfig && !$this->_serverUrl) {
            $cfg = Vps_Registry::get('config');
            $this->_serverUrl = $cfg->service->{$this->_serverConfig}->url;
            if ($cfg->service->{$this->_serverConfig}->proxy) {
                if (!empty($cfg->service->{$this->_serverConfig}->proxy->host)) {
                    $config['proxyHost'] = $cfg->service->{$this->_serverConfig}->proxy->host;
                }
                if (!empty($cfg->service->{$this->_serverConfig}->proxy->port)) {
                    $config['proxyPort'] = $cfg->service->{$this->_serverConfig}->proxy->port;
                }
                if (!empty($cfg->service->{$this->_serverConfig}->proxy->user)) {
                    $config['proxyUser'] = $cfg->service->{$this->_serverConfig}->proxy->user;
                }
                if (!empty($cfg->service->{$this->_serverConfig}->proxy->password)) {
                    $config['proxyPassword'] = $cfg->service->{$this->_serverConfig}->proxy->password;
                }
            }
        }
        if ($this->_serverUrl) {
            $srpcClientConfig = array('serverUrl' => $this->_serverUrl);
            if (!empty($config['proxyHost'])) $srpcClientConfig['proxyHost'] = $config['proxyHost'];
            if (!empty($config['proxyPort'])) $srpcClientConfig['proxyPort'] = $config['proxyPort'];
            if (!empty($config['proxyUser'])) $srpcClientConfig['proxyUser'] = $config['proxyUser'];
            if (!empty($config['proxyPassword'])) $srpcClientConfig['proxyPassword'] = $config['proxyPassword'];
            $this->_client = new Vps_Srpc_Client($srpcClientConfig);
        }

        $this->_init();

        if (!$this->_client) {
            throw new Vps_Exception("No client or serverUrl has been set in '".get_class($this)."'");
        } else if (!($this->_client instanceof Vps_Srpc_Client)) {
            throw new Vps_Exception("Client must be of type 'Vps_Srpc_Client' in '".get_class($this)."'");
        }

        if (!empty($config['timeout']) && is_integer($config['timeout'])) {
            $this->_client->setTimeout($config['timeout']);
        }
    }

    protected function _init()
    {
    }

    private static function _getMetadataCache()
    {
        static $ret;
        if (!isset($ret)) {
            $frontendOptions = array(
                'automatic_serialization' => true,
                'write_control' => false,
            );
            if (class_exists('Memcache')) {
                $backendOptions = array();
                $backend = 'Memcached';
            } else {
                $backendOptions = array(
                    'cache_dir' => 'application/cache/model',
                    'file_name_prefix' => 'servicemeta'
                );
                $backend = 'File';
            }
            $ret = Vps_Cache::factory('Core', $backend, $frontendOptions, $backendOptions);
        }
        return $ret;
    }

    public function update(Vps_Model_Row_Interface $row, $rowData)
    {
        $pk = $this->getPrimaryKey();
        if (isset($row->$pk)) {
            $rowData = $this->_client->rowSave($row->getCleanDataPrimary(), $rowData);
            $this->_data[$row->$pk] = $rowData;
            foreach ($rowData as $k=>$v) {
                $row->$k = $v;
            }
            return $rowData[$pk];
        }
        throw new Vps_Exception("Can't find entry");
    }

    public function insert(Vps_Model_Row_Interface $row, $rowData)
    {
        $savedRowData = $this->_client->rowSave(null, $rowData);

        $pk = $this->getPrimaryKey();

        $this->_data[$savedRowData[$pk]] = $savedRowData;
        foreach ($savedRowData as $k=>$v) {
            $row->$k = $v;
        }
        $this->_rows[$savedRowData[$pk]] = $row;

        return $savedRowData[$pk];
    }

    public function delete(Vps_Model_Row_Interface $row)
    {
        $pk = $this->getPrimaryKey();
        if (isset($row->$pk)) {
            $this->_client->rowDelete($row->$pk);
            unset($this->_data[$row->$pk], $this->_rows[$row->$pk]);
            return;
        }
        throw new Vps_Exception("Can't find entry");
    }

    public function getRowByDataKey($key)
    {
        if (!isset($this->_rows[$key])) {
            $this->_rows[$key] = new $this->_rowClass(array(
                'data' => $this->_data[$key],
                'model' => $this
            ));
        }
        return $this->_rows[$key];
    }

    public function getClient()
    {
        return $this->_client;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function countRows($where = array())
    {
        return $this->_client->countRows($where);
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        $pk = $this->getPrimaryKey();
        $keys = array();
        $data = $this->_client->getRows($where, $order, $limit, $start);
        if ($data) {
            foreach ($data as $row) {
                if (!isset($this->_data[$row[$pk]])) {
                    $this->_data[$row[$pk]] = $row;
                }
                $keys[] = $row[$pk];
            }
        }

        return new $this->_rowsetClass(array(
            'dataKeys' => $keys,
            'model' => $this
        ));
    }

    protected function _getOwnColumns()
    {
        if (!$this->_columns) {
            $cache = self::_getMetadataCache();
            $cacheId = md5($this->getUniqueIdentifier()).'_columns';
            if (!$this->_columns = $cache->load($cacheId)) {
                $this->_columns = $this->_client->getColumns();
                $cache->save($this->_columns, $cacheId);
            }
        }
        return $this->_columns;
    }

    public function getPrimaryKey()
    {
        if (!$this->_primaryKey) {
            $cache = self::_getMetadataCache();
            $cacheId = md5($this->getUniqueIdentifier()).'_primaryKey';
            if (!$this->_primaryKey = $cache->load($cacheId)) {
                $this->_primaryKey = $this->_client->getPrimaryKey();
                $cache->save($this->_primaryKey, $cacheId);
            }
        }
        return $this->_primaryKey;
    }

    public function isEqual(Vps_Model_Interface $other)
    {
        if ($other instanceof Vps_Model_Service &&
            $other->getClient() == $this->getClient()
        ) {
            return true;
        }
        return false;
    }

    public function getUniqueIdentifier()
    {
        $url = $this->_client->getServerUrl();
        if (!empty($url)) {
            return $url;
        } else {
            throw new Vps_Exception("no uniqueIdentifier set in ".get_class($this));
        }
    }

    public function deleteRows($where)
    {
        return $this->_client->deleteRows($where);
    }

    public function getSupportedImportExportFormats()
    {
        if (!isset($this->_supportedImportExportFormatsCache)) {
            $cache = self::_getMetadataCache();
            $cacheId = md5($this->getUniqueIdentifier()).'_supportedImportExportFormats';
            if (($this->_supportedImportExportFormatsCache = $cache->load($cacheId))===false) {
                $this->_supportedImportExportFormatsCache = $this->_client->getSupportedImportExportFormats();
                $cache->save($this->_supportedImportExportFormatsCache, $cacheId);
            }
        }
        return $this->_supportedImportExportFormatsCache;
    }

    public function export($format, $select = array())
    {
        return $this->_client->export($format, $select);
    }

    public function import($format, $data, $options = array())
    {
        $this->_client->import($format, $data, $options);
    }

    public function updateRow(array $data)
    {
        return $this->_client->updateRow($data);
    }

    public function callMultiple(array $call)
    {
        return $this->_client->callMultiple($call);
    }
}