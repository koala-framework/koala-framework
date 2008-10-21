<?php
abstract class Vps_Model_Abstract implements Vps_Model_Interface
{
    protected $_rowClass = 'Vps_Model_Row_Abstract';
    protected $_rowsetClass = 'Vps_Model_Rowset_Abstract';
    protected $_default = array();
    protected $_siblingModels = array();
    protected $_dependentModels = array();
    protected $_referenceMap = array();
    /**
     * Row-Filters für automatisch befüllte Spalten
     *
     * Anwendungsbeispiele:
     * _filters = 'filename' //verwendet autom. Vps_Filter_Ascii
     * _filters = array('filename') //verwendet autom. Vps_Filter_Ascii
     * _filters = array('pos')      //Vps_Filter_Row_Numberize
     * _filters = array('pos' => 'MyFilter')
     * _filters = array('pos' => new MyFilter($settings))
     */
    protected $_filters = array();


    protected $_rows = array();

    public function __construct(array $config = array())
    {
        if (isset($config['default'])) $this->_default = (array)$config['default'];
        if (isset($config['siblingModels'])) $this->_siblingModels = (array)$config['siblingModels'];
        if (isset($config['dependentModels'])) $this->_dependentModels = (array)$config['dependentModels'];
        if (isset($config['filters'])) $this->_filters = (array)$config['filters'];
        $this->_init();
    }

    /**
     * @return Vps_Model_Abstract
     **/
    public static function getInstance($modelName)
    {
        static $instances = array();
        if (!isset($instances[$modelName])) {
            $instances[$modelName] = new $modelName();
        }
        return $instances[$modelName];
    }

    protected function _init()
    {
        foreach ($this->_siblingModels as $k=>$i) {
            if (is_string($i)) $this->_siblingModels[$k] = Vps_Model_Abstract::getInstance($i);
        }
        $this->_setupFilters();
    }

    protected function _setupFilters()
    {
    }

    public function getFilters()
    {
        if (is_string($this->_filters)) $this->_filters = array($this->_filters);
        foreach($this->_filters as $k=>$f) {
            if (is_int($k)) {
                unset($this->_filters[$k]);
                $k = $f;
                if ($k == 'pos') {
                    $f = 'Vps_Filter_Row_Numberize';
                } else {
                    $f = 'Vps_Filter_Ascii';
                }
            }
            if (is_string($f)) {
                $f = new $f();
            }
            if ($f instanceof Vps_Filter_Row_Abstract) {
                $f->setField($k);
            }
            $this->_filters[$k] = $f;
        }
        return $this->_filters;
    }

    public function createRow(array $data=array())
    {
        return $this->_createRow($data);
    }
    protected function _createRow(array $data=array(), array $rowConfig = array())
    {
        $rowConfig['model'] = $this;
        $rowConfig['data'] = $this->_default;
        $ret = new $this->_rowClass($rowConfig);

        $siblingRows = array();
        foreach ($this->_siblingModels as $m) {
            if ($m instanceof Vps_Model_SubModel_Interface) {
                $siblingRows[] = $m->getRowBySiblingRow($ret);
            } else {
                $siblingRows[] = $m->createRow();
            }
        }
        $ret->setSiblingRows($siblingRows);
        foreach ($data as $k=>$i) {
            $ret->$k = $i;
        }
        if (!isset($ret->{$this->getPrimaryKey()})) $ret->{$this->getPrimaryKey()} = null;
        return $ret;
    }

    protected function _getRowConfig($data)
    {
        return array(
            'data' => $this->_data[$index],
            'model' => $this->getModel()
        );
    }

    public function getRow($select)
    {
        if (!is_object($select)) {
            $select = $this->select($select);
        }
        $select->limit(1);
        return $this->getRows($select)->current();
    }

    public function countRows($select = array())
    {
        return count($this->getRows($select));
    }


    public function getDefault()
    {
        return $this->_default;
    }

    public function isEqual(Vps_Model_Interface $other)
    {
        throw new Vps_Exception("Method 'isEqual' is not yet implemented in '".get_class($this)."'");
    }

    public function select($where = array(), $order = null, $limit = null, $start = null)
    {
        if (!is_array($where)) {
            $ret = new Vps_Model_Select();
            if ($where) {
                $ret->whereEquals($this->getPrimaryKey(), $where);
            }
        } else {
            $ret = new Vps_Model_Select($where);
        }
        if ($order) $ret->order($order);
        if ($limit || $start) $ret->limit($limit, $start);
        return $ret;
    }

    public function hasColumn($col)
    {
        if (!$this->getColumns()) return true;
        if (in_array($col, $this->getColumns())) return true;
        foreach ($this->getSiblingModels() as $m) {
            if ($m->hasColumn($col)) return true;
        }
        return false;
    }

    public function getSiblingModels()
    {
        return $this->_siblingModels;
    }

    public function getReferenceByModelClass($modelClassName, $rule)
    {
        $ret = array();
        foreach ($this->_referenceMap as $k=>$ref) {
            if ($ref['refModelClass'] == $modelClassName) {
                $ret[$k] = $ref;
            }
        }
        if (count($ret) > 1) {
            if (isset($ret[$rule])) {
                return $ret[$rule];
            } else {
                throw new Vps_Exception("Multiple references from '".get_class($this)."' to '$modelClassName' found, but none with rule-name '$rule'");
            }
        } else if (count($ret) == 1) {
            return array_pop($ret);
        } else {
            throw new Vps_Exception("No reference from '".get_class($this)."' to '$modelClassName'");
        }
    }

    public function getReference($rule)
    {
        return $this->_referenceMap[$rule];
    }

    public function getDependentModel($rule)
    {
        if (!isset($this->_dependentModels[$rule])) {
            throw new Vps_Exception("dependent Model with rule '$rule' does not exist for '".get_class($this)."'");
        }
        $m = $this->_dependentModels[$rule];
        if ($m instanceof Vps_Model_Abstract) return $m;
        return Vps_Model_Abstract::getInstance($m);
    }

    public function getRowsetClass()
    {
        return $this->_rowsetClass;
    }


    public function find($id)
    {
        return $this->getRows(array('equals'=>array($this->getPrimaryKey()=>$id)));
    }

    public function fetchAll($where=null, $order=null, $limit=null, $start=null)
    {
        return $this->getRows($where, $order, $limit, $start);
    }

    public function fetchCount($where = array())
    {
        return $this->countRows($where);
    }
}
