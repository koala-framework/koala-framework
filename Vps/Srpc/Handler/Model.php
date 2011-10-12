<?php
class Vps_Srpc_Handler_Model extends Vps_Srpc_Handler_Abstract
{
    protected $_model;

    public function __construct(array $config = array())
    {
        if (isset($config['model']) && is_object($config['model']) && $config['model'] instanceof Vps_Model_Interface) {
            $this->_model = $config['model'];
        }
        parent::__construct();
    }

    public function getModel()
    {
        if (!$this->_model) {
            throw new Vps_Srpc_Exception("'model' has not been set for '".get_class($this)."'. Either set it in _init() or use the config option 'model'.");
        }
        return $this->_model;
    }

    public function getRow($id)
    {
        $row = $this->getModel()->getRow($id);
        if (!$row) return null;
        return $row->toArray();
    }

    public function countRows($select = array())
    {
        if (is_array($select) && !empty($select['selectObjVersion'])) {
            $select = Vps_Model_Select::fromArray($select);
        }
        return $this->getModel()->countRows($select);
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        if (is_array($where) && !empty($where['selectObjVersion'])) {
            $where = Vps_Model_Select::fromArray($where);
        }
        $result = $this->getModel()->getRows($where, $order, $limit, $start);
        if (!$result || !$result->current()) return null;
        return $result->toArray();
    }

    public function getColumns()
    {
        return $this->getModel()->getColumns();
    }

    public function getColumnType($column)
    {
        return $this->getModel()->getColumnType($column);
    }

    public function getPrimaryKey()
    {
        return $this->getModel()->getPrimaryKey();
    }

    //TODO: stattdessen insertRow und updateRow verwenden
    public function rowSave($id, $data)
    {
        if (!$data || !is_array($data)) return false;

        if (is_null($id)) {
            $row = $this->getModel()->createRow();
        } else {
            $row = $this->getModel()->getRow($id);
        }

        if (!$row) return false;

        foreach ($data as $col => $value) {
            $row->$col = $value;
        }
        $row->save();

        return $row->toArray();
    }

    public function rowDelete($id)
    {
        $row = $this->getModel()->getRow($id);
        if (!$row) return false;
        $row->delete();
        return true;
    }

    public function deleteRows($where)
    {
        if (is_array($where) && !empty($where['selectObjVersion'])) {
            $where = Vps_Model_Select::fromArray($where);
        }
        return $this->getModel()->deleteRows($where);
    }

    public function getSupportedImportExportFormats()
    {
        return $this->getModel()->getSupportedImportExportFormats();
    }

    public function copyDataFromModel(Vps_Model_Interface $sourceModel, $select = null, $format = null)
    {
        if (is_array($select) && !empty($select['selectObjVersion'])) {
            $select = Vps_Model_Select::fromArray($select);
        }
        return $this->getModel()->copyDataFromModel($sourceModel, $select, $format);
    }

    public function export($format, $select = array())
    {
        if (is_array($select) && !empty($select['selectObjVersion'])) {
            $select = Vps_Model_Select::fromArray($select);
        }
        return $this->getModel()->export($format, $select);
    }

    public function import($format, $data, $options = array())
    {
        return $this->getModel()->import($format, $data, $options);
    }

    public function updateRow(array $data)
    {
        return $this->getModel()->updateRow($data);
    }

    public function insertRow(array $data)
    {
        return $this->getModel()->insertRow($data);
    }

    public function callMultiple(array $call)
    {
        foreach ($call as $method => $args) {
            foreach ($args as $k => $arg) {
                if (is_array($arg) && !empty($arg['selectObjVersion'])) {
                    $call[$method][$k] = Vps_Model_Select::fromArray($arg);
                }
            }
        }
        return $this->getModel()->callMultiple($call);
    }
}
