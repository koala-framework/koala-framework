<?php
class Vps_Form_Field_MultiFields extends Vps_Form_Field_Abstract
{
    public $fields;
    private $_model;
    private $_references;
    private $_referenceName;

    public function __construct($reference = null)
    {
        if (is_object($reference)) {
            $model = $reference;
        } else if (class_exists($reference) && is_instance_of($reference, 'Zend_Db_Table_Abstract')) {
            $model = new $reference();
        } else {
            $this->_referenceName = $reference;
        }
        parent::__construct(is_object($reference) ? get_class($reference) : $reference);
        if (isset($model)) {
            if (!($model instanceof Vps_Model_Interface)) {
                $model = new Vps_Model_Db(array(
                    'table' => $model
                ));
            }
            $this->setModel($model);
        }
        $this->fields = new Vps_Collection_FormFields();
        $this->setBorder(false);
        $this->setBaseCls('x-plain');
        $this->setXtype('multifields');
        $this->setMinEntries(1);
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
        return $this;
    }

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        $ret['multiItems'] = $this->fields->getMetaData($model);
        if (!isset($ret['position'])) {
            if (isset($this->_referenceName)) {
                $m = $model->getDependentModel($this->_referenceName);
            } else {
                $m = $model;
            }
            $ret['position'] = $m->hasColumn('pos');
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
        $pk = $row->getModel()->getPrimaryKey();
        if (!$row->$pk) {
            //neuer eintrag (noch keine id)
            return array();
        }
        if (isset($this->_referenceName)) {
            $select = new Vps_Model_Select();
            if ($row->getModel()->getDependentModel($this->_referenceName)->hasColumn('pos')) {
                $select->order('pos');
            }
            $rows = $row->getChildRows($this->_referenceName, $select);
        } else {
            $ref = $this->_getReferences($row);
            $where = array();
            foreach (array_keys($ref['columns']) as $k) {
                $where["{$ref['columns'][$k]} = ?"] = $row->{$ref['refColumns'][$k]};
            }
            if ($this->_model->hasColumn('pos')) $where['order'] = 'pos';
            $rows = $this->_model->fetchAll($where);
        }
        return $rows;
    }
    public function load($row, $postData = array())
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
                    if (isset($this->_referenceName)) {
                        $r = $row->createChildRow($this->_referenceName);
                    } else {
                        $r = $this->_model->createRow();
                    }
                }
                $retRow['id'] = $r->id;
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
                $retRow['id'] = $r->id;
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

    public function processInput($row, $postData)
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

        $fieldPostData = array();
        if (isset($postData[$this->getFieldName()])) {
            $fieldPostData = $postData[$this->getFieldName()];
        }


        $postData[$this->getFieldName()] = array(
            'save' => array(),
            'delete' => array()
        );

        $rows = $this->_getRowsByRow($row);
        foreach ($rows as $k=>$r) {
            $found = false;
            foreach ($fieldPostData as $postDataKey=>$rowPostData) {
                if (isset($rowPostData['id']) && $rowPostData['id'] == $r->id) {
                    $postData[$this->getFieldName()]['save'][] = array('row'=>$r, 'data'=>$rowPostData, 'insert'=>false, 'pos'=>$postDataKey+1);
                    unset($fieldPostData[$postDataKey]);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $postData[$this->getFieldName()]['delete'][] = $r;
            }
        }

        foreach ($fieldPostData as $postDataKey=>$rowPostData) {
            if (isset($this->_referenceName)) {
                $r = $row->createChildRow($this->_referenceName);
            } else {
                $r = $this->_model->createRow();
            }
            $postData[$this->getFieldName()]['save'][] = array('row'=>$r, 'data'=>$rowPostData, 'insert'=>true, 'pos'=>$postDataKey+1);
        }

        foreach ($postData[$this->getFieldName()]['save'] as &$d) {
            foreach ($this->fields as $field) {
                $d['data'] = $field->processInput($d['row'], $d['data']);
            }
        }

        return $postData;
    }

    public function prepareSave(Vps_Model_Row_Interface $row, $postData)
    {
        if (!isset($postData[$this->getFieldName()])) {
            throw new Vps_Exception("No postData found '".$this->getFieldName()."'");
        }
        $postData = $postData[$this->getFieldName()];
        foreach ($postData['save'] as $d) {
            foreach ($this->fields as $field) {
                $field->prepareSave($d['row'], $d['data']);
            }
            if ($d['row']->getModel()->hasColumn('pos')) {
                $d['row']->pos = $d['pos'];
            }
        }

        foreach ($postData['delete'] as $d) {
            $d->delete();
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

        $cnt = count($postData['save']);
        $name = $this->getFieldLabel();
        if (!$name) $name = $this->getName();
        foreach ($this->getValidators() as $v) {
            if (!$v->isValid($cnt)) {
                $ret[] = $name.": ".implode("<br />\n", $v->getMessages());
            }
        }

        foreach ($postData['save'] as $d) {
            foreach ($this->fields as $field) {
                $ret = array_merge($ret, $field->validate($d['row'], $d['data']));
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
        $postData = $postData[$this->getFieldName()];

        foreach ($postData['save'] as $i) {
            $r = $i['row'];
            $rowPostData = $i['data'];
            foreach ($this->fields as $field) {
                $field->save($r, $rowPostData);
            }

            if ($i['insert']
                && !isset($this->_referenceName) //models speichern childRows selbst wenn sie per getChildRows od. createChildRow erstellt wurden
            ) {
                if ($this->_model instanceof Vps_Model_FieldRows) {
                    //nichts zu tun, keine parent_id muss gesetzt werden
                } else {
                    $ref = $this->_getReferences($row);
                    $where = array();
                    foreach (array_keys($ref['columns']) as $k) {
                        $r->{$ref['columns'][$k]} = $row->{$ref['refColumns'][$k]};
                    }
                    $r->save();
                }
            }
        }
    }

    public function getTemplateVars($values, $namePostfix = '')
    {
        $ret = parent::getTemplateVars($values);
        $name = $this->getFieldName();
        $value = $values[$name];

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
