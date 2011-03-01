<?php
class Vps_Model_Field extends Vps_Model_Abstract implements Vps_Model_SubModel_Interface
{
    protected $_rowClass = 'Vps_Model_Field_Row';
    protected $_rowsetClass = 'Vps_Model_Field_Rowset';
    protected $_fieldName;
    protected $_columns = array();

    public function __construct(array $config = array())
    {
        if (isset($config['fieldName'])) {
            $this->_fieldName = $config['fieldName'];
        }
        if (isset($config['columns'])) $this->_columns = (array)$config['columns'];
        parent::__construct($config);
    }

    public function getRow($select)
    {
        throw new Vps_Exception('getRow');
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        throw new Vps_Exception('getRows is not possible for Vps_Model_Field');
    }

    public function countRows($select = array())
    {
        throw new Vps_Exception('countRows is not possible for Vps_Model_Field');
    }

    public function isEqual(Vps_Model_Interface $other)
    {
        throw new Vps_Exception('isEqual is not possible for Vps_Model_Field');
    }

    public function getPrimaryKey()
    {
        return null;
    }

    protected function _getOwnColumns()
    {
        return $this->_columns;
    }

    public function getRowBySiblingRow(Vps_Model_Row_Interface $siblingRow)
    {
        $data = $siblingRow->{$this->_fieldName};
        if (is_string($data)) {
            if (substr($data, 0, 13) == 'vpsSerialized') {
                $data = substr($data, 13);
            }
            try {
                $data = json_decode($data);
            } catch (Exception $e) {
                $e = new Vps_Exception($e->getMessage(). " $data");
                $e->logOrThrow();
                $data = false;
            }
        }
        if (!$data) {
            $data = $this->getDefault();
        }
        $data = (array)$data;

        return new $this->_rowClass(array(
            'model' => $this,
            'siblingRow' => $siblingRow,
            'data' => $data
        ));
    }

    public function getFieldName()
    {
        return $this->_fieldName;
    }

    public function getUniqueIdentifier() {
        throw new Vps_Exception("no unique identifier set");
    }
}
