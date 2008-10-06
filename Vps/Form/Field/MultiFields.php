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
        } else {
            throw new Vps_Exception("Invalid table or model: '$tableName'");
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
    public function load(Vps_Model_Row_Interface $row, $postData = array())
    {
        if (isset($postData[$this->getFieldName()])) {
            $postData = $postData[$this->getFieldName()];
        } else {
            $postData = false;
        }

        if (!$row) return array();
        $ret = array($this->getFieldName()=>array());

        $rows = $this->_getRowsByRow($row);

        if ($postData!==false) {
            $pos = array();
            $rowsArray = array();
            foreach ($rows as $r) {
                $rowsArray[] = $r;
            }
            foreach ($postData as $i=>$rPostData) {
                $retRow = array();
                if (isset($rowsArray[$i]) && (!isset($rPostData['isNewRow']) || !$rPostData['isNewRow'])) {
                    $r = $rowsArray[$i];
                } else {
                    $r = $this->_model->createRow();
                }
                $rPostData = $postData[$i];
                foreach ($this->fields as $field) {
                    $retRow = array_merge($retRow, $field->load($r, $rPostData));
                }
                $ret[$this->getFieldName()][] = $retRow;
                if (isset($r->pos)) {
                        //funktioniert ned gscheit mit hinzufügen im frontend
//                     $pos[] = $r->pos;
                }
            }
        } else {
            $pos = array();
            foreach ($rows as $i=>$r) {
                $retRow = array();
                foreach ($this->fields as $field) {
                    $retRow = array_merge($retRow, $field->load($r));
                }
                $ret[$this->getFieldName()][] = $retRow;
                if (isset($r->pos)) {
                    $pos[] = $r->pos;
                }
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

    public function processInput($postData)
    {
        if (isset($postData[$this->getFieldName().'_num'])) {
            $ret = array();
            $postData[$this->getFieldName()] = array();
            for ($i = 0; $i < $postData[$this->getFieldName().'_num']; $i++) {
                if (isset($postData[$this->getFieldName().'_del']) && $postData[$this->getFieldName().'_del'] == $i) {
                    continue;
                }
                $postRow = array();
                foreach ($postData as $fieldName=>$values) {
                    if (is_array($values) && isset($values[$i])) {
                        $postRow[$fieldName] = $values[$i];
                        unset($postData[$fieldName][$i]);
                    }
                }
                $postData[$this->getFieldName()][] = $postRow;
            }
            if (isset($postData[$this->getFieldName().'_add'])) {
                $postData[$this->getFieldName()][] = array('isNewRow' => true);
            }
        } else if (isset($postData[$this->getFieldName()])) {
            if (is_string($postData[$this->getFieldName()])) {
                $postData[$this->getFieldName()] = Zend_Json::decode($postData[$this->getFieldName()]);
            }
        }
        if (isset($postData[$this->getFieldName()])) {
            foreach ($postData[$this->getFieldName()] as $i=>$rowPostData) {
                foreach ($this->fields as $item) {
                    $postData[$this->getFieldName()][$i] = $item->processInput($rowPostData);
                }
            }
        }
        return $postData;
    }

    public function prepareSave(Vps_Model_Row_Interface $row, $postData)
    {
        if (!isset($postData[$this->getFieldName()])) {
            throw new Vps_Exceception("No postData found");
        }
        $postData = $postData[$this->getFieldName()];

        $rows = $this->_getRowsByRow($row);

        $id = $row->getInternalId();
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
    public function validate($row, $postData)
    {
        $ret = array();

        $this->_addValidators();
        if (!isset($postData[$this->getFieldName()])) {
            $postData = array();
        } else {
            $postData = $postData[$this->getFieldName()];
        }

        $cnt = count($postData);
        $name = $this->getFieldLabel();
        if (!$name) $name = $this->getName();
        foreach ($this->getValidators() as $v) {
            if (!$v->isValid($cnt)) {
                $ret[] = $name.": ".implode("<br />\n", $v->getMessages());
            }
        }

        foreach ($this->fields as $field) {
            foreach ($postData as $d) {
                $ret = array_merge($ret, $field->validate(null, $d));
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
        $id = $row->getInternalId();

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

    public function getTemplateVars($values, $namePostfix = '')
    {
        $ret = parent::getTemplateVars($values);
        $name = $this->getFieldName();
        if (isset($values[$name])) {
            $value = $values[$name];
        }
        $ret = parent::getTemplateVars($values);
        $ret['preHtml'] = '<input type="hidden" name="'.$name.'_num'.$namePostfix.'" value="'.count($value).'" />';
        $ret['postHtml'] = '<div class="addLayer"><div class="submitWrapper"><span class="beforeButton"></span><span class="button"><button class="submit add" type="submit" name="'.$name.'_add'.$namePostfix.'" value="1">'.trlVps("New Entry").'</button></span><span class="afterButton"></span></div></div>';

        $ret['items'] = array();
        foreach ($value as $i=>$rowValues) {
            $ret['items'][] = array('preHtml' => "<div class=\"vpsFormFieldMultiFieldsRow\">\n", 'item' => null);
            $ret['items'] = array_merge($ret['items'], $this->fields->getTemplateVars($rowValues, $namePostfix."[$i]"));
            $ret['items'][] = array('postHtml' => "</div>\n", 'html' => '<button class="delete" type="submit" name="'.$name.'_del'.$namePostfix.'" value="'.$i.'">'.trlVps("Delete Entry").'</button>', 'item' => null);
        }
        return $ret;
    }
}
