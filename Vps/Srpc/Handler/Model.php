<?php
class Vps_Srpc_Handler_Model extends Vps_Srpc_Handler_Abstract
{
    protected $_model;
    protected $_columns;

    public function __construct(array $config = array())
    {
        if (isset($config['model'])) {
            $this->_model = $config['model'];
        }
        if (isset($config['columns'])) {
            $this->_columns = $config['columns'];
        }
        parent::__construct();
    }

    public function getModel()
    {
        if (!$this->_model) {
            throw new Vps_Srpc_Exception("'model' has not been set for '".get_class($this)."'. Either set it in _init() or use the config option 'model'.");
        }
        if (is_string($this->_model)) {
            $this->_model = Vps_Model_Abstract::getInstance($this->_model);
        }
        return $this->_model;
    }

    public function getRow($id)
    {
        $row = $this->getModel()->getRow($id);
        if (!$row) return null;
        if ($this->_columns) {
            $ret = array();
            foreach ($this->_columns as $c) {
                $ret[$c] = $row->$c;
            }
            return $ret;
        } else {
            return $row->toArray();
        }
    }

    public function countRows($select = array())
    {
        return $this->getModel()->countRows($select);
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        $result = $this->getModel()->getRows($where, $order, $limit, $start);
        if (!$result || !$result->current()) return null;
        if ($this->_columns) {
            $ret = array();
            foreach ($result as $row) {
                $data = array();
                foreach ($this->_columns as $c) {
                    $data[$c] = $row->$c;
                }
                $ret[] = $data;
            }
            return $ret;
        } else {
            return $result->toArray();
        }
    }

    public function getColumns()
    {
        if ($this->_columns) return $this->_columns;
        return $this->getModel()->getColumns();
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

        if ($this->_columns) {
            $ret = array();
            foreach ($this->_columns as $c) {
                $ret[$c] = $row->$c;
            }
            return $ret;
        } else {
            return $row->toArray();
        }
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
        return $this->getModel()->deleteRows($where);
    }

    public function getSupportedImportExportFormats()
    {
        return $this->getModel()->getSupportedImportExportFormats();
    }

    public function copyDataFromModel(Vps_Model_Interface $sourceModel, $select = null, $format = null)
    {
        return $this->getModel()->copyDataFromModel($sourceModel, $select, $format);
    }

    public function export($format, $select = array(), $options = array())
    {
        if ($this->_columns) $options['columns'] = $this->_columns;
        return $this->getModel()->export($format, $select, $options);
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
        $ret = array();
        foreach ($call as $method=>&$arguments) {
            if ($method == 'export') {
                if (!isset($arguments[1])) $arguments[1] = array();
                if (!isset($arguments[2])) $arguments[2] = array();
                $arguments[2]['columns'] = $this->_columns;
            }
        }
        return $this->getModel()->callMultiple($call);
    }
}
