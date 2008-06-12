<?php
class Vps_Form_Field_MultiFields extends Vps_Form_Field_Abstract
{
    public $fields;
    private $_updatedRows = array();
    private $_deletedRows = array();
    private $_insertedRows = array();
    private $_model;
    private $_references;
    
    public function __construct($tableName = null)
    {
        if (is_object($tableName)) {
            $model = $tableName;
        } else if (class_exists($tableName)) {
            $model = new $tableName();
        }
        parent::__construct(get_class($model));
        if ($model instanceof Zend_Db_Table_Abstract) {
            $model = new Vps_Model_Db(array(
                'table' => $model
            ));
        }
        $this->setModel($model);
        $this->fields = new Vps_Collection_FormFields();
        $this->setBorder(false);
        $this->setXtype('multifields');
    }
    
    protected function _addValidators()
    {
        parent::_addValidators();
        if ($this->getMaxEntries()) {
            $this->addValidator(new Zend_Validate_LessThan($this->getMaxEntries()+0.000001));
        }
        if ($this->getMinEntries()) {
            $this->addValidator(new Zend_Validate_GreaterThan($this->getMinEntries()-0.000001));
        }
    }

    public function setModel($model)
    {
        $this->_model = $model;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function setReferences($references)
    {
        $this->_references = $references;
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        $ret['multiItems'] = $this->fields->getMetaData();
        if (!isset($ret['position'])) {
            if ($this->_model instanceof Vps_Model_Db) {
                $info = $this->_model->getTable()->info();
                $ret['position'] = in_array('pos', $info['cols']);
            } else {
                $ret['position'] = false;
            }
        }
        return $ret;
    }

    public function hasChildren()
    {
        return sizeof($this->fields) > 0;
    }
    public function getChildren()
    {
        return $this->fields;
    }

    protected function _getRowsByRow(Vps_Model_Row_Interface $row)
    {
        if ($this->_model instanceof Vps_Model_FieldRows) {
            $rows = $this->_model->fetchByParentRow($row);
        } else {
            $pk = $row->getModel()->getPrimaryKey();
            if (!$row->$pk) {
                //neuer eintrag (noch keine id)
                return array();
            }
            $ref = $this->_getReferences($row);
            $where = array();
            foreach (array_keys($ref['columns']) as $k) {
                $where["{$ref['columns'][$k]} = ?"] = $row->{$ref['refColumns'][$k]};
            }
            $rows = $this->_model->fetchAll($where);
        }
        return $rows;
    }
    public function load(Vps_Model_Row_Interface $row)
    {
        if (!$row) return array();
        $ret = array($this->getFieldName()=>array());

        $rows = $this->_getRowsByRow($row);

        $pos = array();
        foreach ($rows as $r) {
            $retRow = array();
            foreach ($this->fields as $field) {
                $retRow = array_merge($retRow, $field->load($r));
            }
            $ret[$this->getFieldName()][] = $retRow;
            if (isset($r->pos)) {
                $pos[] = $r->pos;
            }
        }
        if (count($pos)) {
            //händisch per php sortieren
            //todo: kann verbessert werden wenn findDependentRowset ein 3. parameter ein db_select aktzeptiert
            //(ist im moment noch im zend incubator)
            array_multisort($pos, SORT_ASC, SORT_NUMERIC,
                            $ret[$this->getFieldName()]);
        }
        return $ret;
    }

    public function prepareSave(Vps_Model_Row_Interface $row, $postData)
    {
        $postData = $postData[$this->getFieldName()];
        if (is_string($postData)) { $postData = Zend_Json::decode($postData); }

        $rows = $this->_getRowsByRow($row);
        $id = $row->{$row->getModel()->getPrimaryKey()};
        $this->_updatedRows[$id] = array();
        $this->_deletedRows[$id] = array();
        $this->_insertedRows[$id] = array();
        $pos = 0;

        foreach ($rows as $k=>$r) {
            if (isset($postData[$k])) {
                $rowPostData = $postData[$k];
                $this->_updatedRows[$id][] = array('row'=>$r, 'data'=>$rowPostData);
                foreach ($this->fields as $field) {
                    $field->prepareSave($r, $rowPostData);
                }
                $pos++;
                if (isset($r->pos)) {
                    $r->pos = $pos;
                }
                unset($postData[$k]);
            } else {
                $this->_deletedRows[$id][] = $r;
            }
        }

        foreach ($postData as $k=>$rowPostData) {
            $k = (int)$k;
            $r = $this->_model->createRow();
            $this->_insertedRows[$id][] = array('row'=>$r, 'data'=>$rowPostData);
            foreach ($this->fields as $field) {
                $field->prepareSave($r, $rowPostData);
            }
            $pos++;
            if (isset($r->pos)) {
                $r->pos = $pos;
            }
        }
    }
    public function validate($postData)
    {
        $ret = array();

        $this->_addValidators();
        $postData = $postData[$this->getFieldName()];
        if (is_string($postData)) { $postData = Zend_Json::decode($postData); }

        $cnt = count($postData);
        $name = $this->getFieldLabel();
        if (!$name) $name = $this->getName();
        foreach ($this->getValidators() as $v) {
            if (!$v->isValid($cnt)) {
                $ret[] = $name.": ".implode("<br />\n", $v->getMessages());
            }
        }
        return $ret;
    }
    
    protected function _getReferences($row)
    {
        if ($this->_references) {
            return $this->_references;
        } else if ($this->_model instanceof Vps_Model_Db && $row instanceof Vps_Model_Db_Row) {
            return $this->_model->getTable()
                        ->getReference(get_class($row->getRow()->getTable()));
        } else {
            throw new Vps_Exception('Couldn\'t read references for Multifields. Either use Vps_Model_FieldRows/Vps_Model_Db or set the References by setReferences().');
        }
    }

    public function save(Vps_Model_Row_Interface $row, $postData)
    {
        $id = $row->{$row->getModel()->getPrimaryKey()};
        foreach ($this->_deletedRows[$id] as $r) {
            $r->delete();
        }

        foreach ($this->_insertedRows[$id] as $i) {
            $r = $i['row'];
            $rowPostData = $i['data'];

            if ($this->_model instanceof Vps_Model_FieldRows) {
                //nichts zu tun, keine parent_id muss gesetzt werden
            } else {
                $ref = $this->_getReferences($row);
                $where = array();
                foreach (array_keys($ref['columns']) as $k) {
                    $r->{$ref['columns'][$k]} = $row->{$ref['refColumns'][$k]};
                }
            }
            $r->save();
            foreach ($this->fields as $field) {
                $field->save($r, $rowPostData);
            }
        }

        foreach ($this->_updatedRows[$id] as $i) {
            $r = $i['row'];
            $rowPostData = $i['data'];
            $r->save();
            foreach ($this->fields as $field) {
                $field->save($r, $rowPostData);
            }
        }

    }
}
