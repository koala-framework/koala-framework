<?php
/**
 * @package Form
 */
class Kwf_Form_Field_MultiFields extends Kwf_Form_Field_Abstract
{
    public $fields;

    public function __construct($reference = null, $fieldname = null)
    {
        if (is_object($reference)) {
            $model = $reference;
        } else if (class_exists($reference) && is_instance_of($reference, 'Zend_Db_Table_Abstract')) {
            $model = new $reference();
        } else {
            $this->setReferenceName($reference);
        }
        if (!$fieldname) {
            $fieldname = is_object($reference) ? get_class($reference) : $reference;
        }
        parent::__construct($fieldname);
        if (isset($model)) {
            if (!($model instanceof Kwf_Model_Interface)) {
                $model = new Kwf_Model_Db(array(
                    'table' => $model
                ));
            }
            $this->setModel($model);
        }
        $this->fields = new Kwf_Collection_FormFields();
        $this->setBorder(false);
        $this->setBaseCls('x2-plain');
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

    /**
     * Set reference of MultiFields model from parent model
     *
     * Normally passed as first constructor argument
     *
     * @param string
     */
    public function setReferenceName($name)
    {
        return $this->setProperty('referenceName', $name);
    }

    public function getReferenceName()
    {
        return $this->getProperty('referenceName');
    }

    /**
     * Set model in which child rows are saved.
     *
     * Normally automatically set by using the reference passed in constructor
     *
     * @param Kwf_Model_Interface
     */
    public function setModel($model)
    {
        return $this->setProperty('model', $model);
    }

    public function getModel()
    {
        return $this->getProperty('model');
    }

    /**
     * Manually set references required for model
     *
     * Normally automatically read from Model references
     */
    public function setReferences($select)
    {
        return $this->setProperty('references', $select);
    }

    public function getReferences()
    {
        return $this->getProperty('references');
    }

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        if ($this->getReferenceName()) {
            $model = $model->getDependentModel($this->getReferenceName());
        } else {
            $model = $model;
        }
        $ret['multiItems'] = $this->fields->getMetaData($model);
        if (!isset($ret['position'])) {
            $ret['position'] = $model->hasColumn('pos');
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

    /**
     * Set select used for getting available rows
     *
     * If not set all rows are used
     */
    public function setSelect(Kwf_Model_Select $select)
    {
        return $this->setProperty('select', $select);
    }

    public function getSelect()
    {
        return $this->getProperty('select');
    }

    protected function _getRowsByRow(Kwf_Model_Row_Interface $row)
    {
        $pk = $row->getModel()->getPrimaryKey();
        if (!$row->$pk) {
            //neuer eintrag (noch keine id)
            return array();
        }
        if ($this->getReferenceName()) {
            if ($this->getSelect()) {
                $select = $this->getSelect();
            } else {
                $select = new Kwf_Model_Select();
            }
            if ($row->getModel()->getDependentModel($this->getReferenceName())->hasColumn('pos')) {
                $select->order('pos');
            }
            $rows = $row->getChildRows($this->getReferenceName(), $select);
        } else {
            $ref = $this->_getReferences($row);
            $where = array();
            foreach (array_keys($ref['columns']) as $k) {
                $where["{$ref['columns'][$k]} = ?"] = $row->{$ref['refColumns'][$k]};
            }
            if ($this->getModel()->hasColumn('pos')) $where['order'] = 'pos';
            $rows = $this->getModel()->fetchAll($where);
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
                    if ($this->getReferenceName()) {
                        $r = $row->createChildRow($this->getReferenceName());
                    } else {
                        $r = $this->getModel()->createRow();
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
            if (!$found && (is_null($this->getAllowDelete()) || $this->getAllowDelete())) {
                $postData[$this->getFieldName()]['delete'][] = $r;
            }
        }

        if (is_null($this->getAllowAdd()) || $this->getAllowAdd()) {
            foreach ($fieldPostData as $postDataKey=>$rowPostData) {
                if ($this->getReferenceName()) {
                    $r = $row->createChildRow($this->getReferenceName());
                } else {
                    $r = $this->getModel()->createRow();
                }
                $postData[$this->getFieldName()]['save'][] = array('row'=>$r, 'data'=>$rowPostData, 'insert'=>true, 'pos'=>$postDataKey+1);
            }
        }

        foreach ($postData[$this->getFieldName()]['save'] as &$d) {
            foreach ($this->fields as $field) {
                $d['data'] = $field->processInput($d['row'], $d['data']);
            }
        }

        return $postData;
    }

    public function prepareSave(Kwf_Model_Row_Interface $row, $postData)
    {
        if (!isset($postData[$this->getFieldName()])) {
            throw new Kwf_Exception("No postData found '".$this->getFieldName()."'");
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
                $ret[] = array(
                    'messages' => $v->getMessages(),
                    'field' => $this
                );
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
        if ($this->getReferences()) {
            return $this->getReferences();
        } else if ($this->getModel() instanceof Kwf_Model_Db && $row instanceof Kwf_Model_Db_Row) {
            return $this->getModel()->getTable()
                        ->getReference(get_class($row->getRow()->getTable()));
        } else {
            throw new Kwf_Exception('Couldn\'t read references for Multifields. Either use Kwf_Model_FieldRows/Kwf_Model_Db or set the References by setReferences().');
        }
    }

    public function save(Kwf_Model_Row_Interface $row, $postData)
    {
        $postData = $postData[$this->getFieldName()];

        foreach ($postData['save'] as $i) {
            $r = $i['row'];
            $rowPostData = $i['data'];
            foreach ($this->fields as $field) {
                $field->save($r, $rowPostData);
            }

            if ($i['insert']
                && !$this->getReferenceName() //models speichern childRows selbst wenn sie per getChildRows od. createChildRow erstellt wurden
            ) {
                if ($this->getModel() instanceof Kwf_Model_FieldRows) {
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

            if (!$this->getReferenceName()) {
                //models speichern childRows selbst wenn sie per getChildRows od. createChildRow erstellt wurden
                $r->save();
            }
            foreach ($this->fields as $field) {
                $field->save($r, $rowPostData);
            }
        }
    }

    public function getTemplateVars($values, $namePostfix = '', $idPrefix = '')
    {
        $ret = parent::getTemplateVars($values, $namePostfix, $idPrefix);
        $name = $this->getFieldName();
        $value = $values[$name];

        $ret['preHtml'] = '<input type="hidden" name="'.$name.'_num'.$namePostfix.'" value="'.count($value).'" />';
        if (is_null($this->getAllowAdd()) || $this->getAllowAdd()) {
            $ret['postHtml'] = '<div class="addLayer"><div class="submitWrapper"><span class="beforeButton"></span><span class="button"><button class="submit add" type="submit" name="'.$name.'_add'.$namePostfix.'" value="1">'.trlKwf("New Entry").'</button></span><span class="afterButton"></span></div></div>';
        }

        $ret['items'] = array();
        foreach ($value as $i=>$rowValues) {
            $ret['items'][] = array('preHtml' => "<div class=\"kwfFormFieldMultiFieldsRow\">\n", 'item' => null);
            $ret['items'] = array_merge($ret['items'], $this->fields->getTemplateVars($rowValues, $namePostfix."[$i]", $idPrefix));
            if (is_null($this->getAllowDelete()) || $this->getAllowDelete()) {
                $ret['items'][] = array('postHtml' => "</div>\n", 'html' => '<button class="delete" type="submit" name="'.$name.'_del'.$namePostfix.'" value="'.$i.'">'.trlKwf("Delete Entry").'</button>', 'item' => null);
            }
        }
        return $ret;
    }
}
