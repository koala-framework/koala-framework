<?php
abstract class Vps_Model_Row_Abstract implements Vps_Model_Row_Interface
{
    private $_skipFilters = false; //für saveSkipFilters
    /**
     * @var Vps_Model_Abstract
     **/
    protected $_model;
    private $_internalId;
    protected $_siblingRows;
    
    public function __construct(array $config)
    {
        if (isset($config['siblingRows'])) {
            $this->_siblingRows = (array)$config['siblingRows'];
        }
        $this->_model = $config['model'];
        static $internalId = 0;
        $this->_internalId = $internalId++;
    }

    public function getInternalId()
    {
        return $this->_internalId;
    }

    public function setSiblingRows(array $rows)
    {
        $this->_siblingRows = $rows;
        return $this;
    }

    protected function _getSiblingRows()
    {
        if (!isset($this->_siblingRows)) {
            $this->_siblingRows = array();
            foreach ($this->_model->getSiblingModels() as $k=>$m) {
                if ($m instanceof Vps_Model_SubModel_Interface) {
                    $r = $m->getRowBySiblingRow($this);
                } else {
                    $ref = $m->getReferenceByModelClass(get_class($this->_model), $k);
                    $r = $m->getRow(array('equals'=>array($ref['column']=>$this->{$this->_getPrimaryKey()})));
                    if (!$r) {
                        $r = $m->createRow();
                        $r->{$ref['column']} = $this->{$this->_getPrimaryKey()};
                    }
                }
                $this->_siblingRows[] = $r;
            }
        }
        return $this->_siblingRows;
    }

    public function __isset($name)
    {
        foreach ($this->_getSiblingRows() as $r) {
            if ($r->getModel()->hasColumn($name)) {
                return isset($r->$name);
            }
        }
        return false;
    }

    public function __unset($name)
    {
        foreach ($this->_getSiblingRows() as $r) {
            if ($r->getModel()->hasColumn($name)) {
                unset($r->$name);
                return;
            }
        }
        throw new Vps_Exception("Invalid column '$name'");
    }

    public function __get($name)
    {
        foreach ($this->_getSiblingRows() as $r) {
            if ($r->getModel()->hasColumn($name)) {
                return $r->$name;
            }
        }
        throw new Vps_Exception("Invalid column '$name'");
    }

    public function __set($name, $value)
    {
        if ($this->_model->getColumns() && !in_array($name, $this->_model->getColumns())) {
            foreach ($this->_getSiblingRows() as $r) {
                if ($r->getModel()->hasColumn($name)) {
                    $r->$name = $value;
                    return;
                }
            }
            throw new Vps_Exception("Invalid column '$name'");
        }
    }

    protected function _postSet($name, $value)
    {
        if ($name == $this->_getPrimaryKey()) {
            foreach ($this->_getSiblingRows() as $k=>$r) {
                if (!$r->getModel() instanceof Vps_Model_SubModel_Interface) {
                    $ref = $r->getModel()->getReferenceByModelClass(get_class($this->_model), $k);
                    $r->{$ref['column']} = $value;
                }
            }
        }
    }

    public function save()
    {
        foreach ($this->_getSiblingRows() as $r) {
            $r->save();
        }
        $this->_updateFilters(true);
        return null;
    }

    protected function _postInsert()
    {
        foreach ($this->_getSiblingRows() as $r) {
            $ref = $r->getModel()->getReferenceByModelClass(get_class($this->_model));
            $r->{$ref['column']} = $value;
            $r->save();
        }
    }

    public function delete()
    {
    }

    public function getModel()
    {
        return $this->_model;
    }

    //für Filter_Row_UniqueAscii
    public function getPrimaryKey()
    {
        return $this->_getPrimaryKey();
    }

    protected function _getPrimaryKey()
    {
        return $this->_model->getPrimaryKey();
    }

    public function getChildRows($rule, $select = array())
    {
        $m = $this->_model->getDependentModel($rule);

//         if ($m instanceof Vps_Model_RowsSubModel_Interface) { geht aus irgendeinen komischen grund ned
        if (method_exists($m, 'getRowsByParentRow')) {
            return $m->getRowsByParentRow($this, $select);
        } else {
            $select = $m->select($select);
            $ref = $m->getReferenceByModelClass(get_class($this->_model), $rule);
            $select->whereEquals($ref['column'], $this->{$this->_getPrimaryKey()});
            return $m->getRows($select);
        }
    }

    public function createChildRow($rule, array $data = array())
    {
        $m = $this->_model->getDependentModel($rule);

        //if ($m instanceof Vps_Model_RowsSubModel_Interface) { geht aus irgendeinen komischen grund ned
        if (method_exists($m, 'createRowByParentRow')) {
            return $m->createRowByParentRow($this, $data);
        } else {
            $ret = $m->createRow();
            $ref = $m->getReferenceByModelClass(get_class($this->_model), $rule);
            $ret->{$ref['column']} = $this->{$this->_getPrimaryKey()};
            return $ret;
        }
    }

    public function getParentRow($rule)
    {
        $ref = $this->_model->getReference($rule);
        $id = $this->{$ref['column']};
        return Vps_Model_Abstract::getInstance($ref['refModelClass'])->getRow($id);
    }

    public function toDebug()
    {
        $i = get_class($this);
        if (method_exists($this, '__toString')) {
            $i .= " (".$this->__toString().")\n";
        }
        $ret = print_r($this->_data, true);
        $ret = preg_replace('#^Array#', $i, $ret);
        $ret = "<pre>$ret</pre>";
        return $ret;
    }

    protected function _beforeSave()
    {
    }

    protected function _afterSave()
    {
        $this->_updateFilters(false);
    }

    protected function _beforeInsert()
    {
    }

    protected function _afterInsert()
    {
    }

    private function _updateFilters($filterAfterSave = false)
    {
        if ($this->_skipFilters) return; //für saveSkipFilters

        $filters = $this->getModel()->getFilters();
        foreach($filters as $k=>$f) {
            if ($f instanceof Vps_Filter_Row_Abstract) {
                if ($f->filterAfterSave() != $filterAfterSave) continue;
                $this->$k = $f->filter($this);
            } else {
                $this->$k = $f->filter($this->__toString());
            }
            if ($filterAfterSave) {
                $this->_skipFilters = true;
                $this->save();
            }
        }
    }

    //Speichern und abei die Filter nicht verwenden
    //wird benötigt bei der Nummerierung um eine Endlusschleife zu verhindern
    public function saveSkipFilters()
    {
        $this->_skipFilters = true;
        $this->save();
        $this->_skipFilters = false;
    }
}
