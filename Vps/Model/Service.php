<?php
class Vps_Model_Service extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_Service_Row';
    protected $_client;
    protected $_data = array();

    protected $_primaryKey;

    public function __construct(array $config = array())
    {
        if (empty($config['client'])) {
            if (empty($config['serverUrl'])) {
                throw new Vps_Exception("Either config option 'client' or 'serverUrl' must be set when using '".get_class($this)."'");
            }
            $config['client'] = new Vps_Srpc_Client(array('serverUrl' => $config['serverUrl']));
        }
        if (!($config['client'] instanceof Vps_Srpc_Client)) {
            throw new Vps_Exception("Client must be of type 'Vps_Srpc_Client' in '".get_class($this)."'");
        }
        $this->_client = $config['client'];
    }

    public function update(Vps_Model_Row_Interface $row, $rowData)
    {
        $pk = $this->getPrimaryKey();
        if (isset($row->$pk)) {
            $rowData = $this->_client->rowSave($row->getCleanDataPrimary(), $rowData);
            $this->_data[$row->$pk] = $rowData;
            return $rowData[$pk];
        }
        throw new Vps_Exception("Can't find entry");
    }

    public function insert(Vps_Model_Row_Interface $row, $rowData)
    {
        $savedRowData = $this->_client->rowSave(null, $rowData);

        $pk = $this->getPrimaryKey();

        $this->_data[$savedRowData[$pk]] = $savedRowData;
        $row->$pk = $savedRowData[$pk];
        $this->_rows[$savedRowData[$pk]] = $row;

        return $rowData[$pk];
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
        foreach ($data as $row) {
            if (!isset($this->_data[$row[$pk]])) {
                $this->_data[$row[$pk]] = $row;
            }
            $keys[] = $row[$pk];
        }

        return new $this->_rowsetClass(array(
            'dataKeys' => $keys,
            'model' => $this
        ));
    }

    public function getColumns()
    {
        return $this->_client->getColumns();
    }

    public function getPrimaryKey()
    {
        if (!$this->_primaryKey) {
            $this->_primaryKey = $this->_client->getPrimaryKey();
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

    public function getUniqueIdentifier() {
        $url = $this->_client->getServerUrl();
        if (!empty($url)) {
            return $url;
        } else {
            throw new Vps_Exception("no uniqueIdentifier set in ".get_class($this));
        }
    }

}