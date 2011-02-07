<?php
// TODO: validators
class Vps_Form_Field_MultiCheckbox extends Vps_Form_Field_Abstract
{
    protected $_fields;
    private $_relationToValuesRule;
    private $_dataModel;
    private $_relModel;
    private $_valuesModel;
    private $_valuesSelect;

    static private $_multiCheckboxes = array();

    /**
     * @see setPool()
     */
    protected $_pool = null;

    /**
     * Zeigt mehrere Checkboxes an und speichert diese in einer Relationstabelle
     *
     * @param string|Vps_Model_Abstract $dependetModelRule Kann folgendes sein:
     *               - Die rule vom Datenmodel zur Relationstabelle (string)
     *               - oder das RelationsModel selbst (Vps_Model_Abstract)
     * @param string $relationToValuesRule Die rule vom Relationsmodel zum Values-model
     */
    public function __construct($dependetModelRule, $relationToValuesRule, $title = null)
    {
        if (!is_string($dependetModelRule)) {
            if (is_object($dependetModelRule) && !($dependetModelRule instanceof Vps_Model_Abstract)) {
                throw new Vps_Exception("dependetModelRule must be of type string (Rule) or Vps_Model_Abstract (RelationModel)");
            }
        }
        $this->setRelModel($dependetModelRule);
        $this->setValuesModel($relationToValuesRule);
        $this->_relationToValuesRule = $relationToValuesRule;

        $fieldKey = $relationToValuesRule;
        $i = 2;
        while (in_array($fieldKey, self::$_multiCheckboxes)) {
            $fieldKey = $relationToValuesRule.$i++;
        }
        self::$_multiCheckboxes[] = $fieldKey;

        parent::__construct($fieldKey);
        if ($title) {
            $this->setTitle($title);
            $this->setFieldLabel($title);
        }
        $this->setAutoHeight(true);
        $this->setLayout('form');
        $this->setXtype('fieldset');
    }

    public function setRelationToData($rel)
    {
        $this->_relations['relationToData'] = $rel;
        return $this;
    }

    public function setValuesModel($valModel)
    {
        $this->_valuesModel = $valModel;
        return $this;
    }

    public function setDataModel($dataModel)
    {
        $this->_dataModel = $dataModel;
        return $this;
    }

    public function getDataModel()
    {
        if (is_string($this->_dataModel)) {
            $this->_dataModel = Vps_Model_Abstract::getInstance($this->_dataModel);
        }
        return $this->_dataModel;
    }

    public function getValuesModel()
    {
        if (is_string($this->_valuesModel)) {
            $relModel = $this->getRelModel();
            if (!$relModel) {
                throw new Vps_Exception("RelationModel must be set first");
            }
            $ref = $relModel->getReference($this->_valuesModel);
            if ($ref && isset($ref['refModel'])) {
                $this->_valuesModel = $ref['refModel'];
            } else if ($ref && isset($ref['refModelClass'])) {
                $this->_valuesModel = Vps_Model_Abstract::getInstance($ref['refModelClass']);
            } else {
                throw new Vps_Exception("Values model cannot be found by reference '{$this->_valuesModel}'");
            }
        }
        return $this->_valuesModel;
    }

    public function setRelModel($relModel)
    {
        $this->_relModel = $relModel;
        return $this;
    }

    public function getRelModel()
    {
        if (is_string($this->_relModel)) {
            $m = $this->getDataModel();
            $this->_relModel = $m->getDependentModel($this->_relModel);
        }
        return $this->_relModel;
    }

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        if ($model) $this->setDataModel($model);
        $ret['items'] = $this->_getFields()->getMetaData($model);
        if (!$ret['items']) unset($ret['items']);
        if (isset($ret['tableName'])) unset($ret['tableName']);
        if (isset($ret['modelName'])) unset($ret['modelName']);
        if (isset($ret['values'])) unset($ret['values']);
        return $ret;
    }

    private function _getValues()
    {
        if ($this->getValues()) {
            return $this->getValues();
        } else {
            return $this->getValuesModel()->getRows($this->getValuesSelect());
        }
    }

    public function setValuesSelect(Vps_Model_Select $select)
    {
        $this->_valuesSelect = $select;
        return $this;
    }

    public function getValuesSelect()
    {
        if (is_null($this->_valuesSelect)) {
            $this->_valuesSelect = $this->getValuesModel()->select();
        }
        if ($this->getPool()) {
            if ($this->getValuesModel() instanceof Vps_Util_Model_Pool) {
                $this->_valuesSelect
                    ->whereEquals('pool', $this->getPool())
                    ->whereEquals('visible', 1)
                    ->order('pos', 'ASC');
            } else {
                throw new Vps_Exception("setPool with MultiCheckbox only works if relationToValues references to an instance of Vps_Util_Model_Pool");
            }
        }
        if ($this->getValuesModel()->hasColumn('pos') && !$this->_valuesSelect->getPart(Vps_Model_Select::ORDER)) {
            $this->_valuesSelect->order('pos', 'ASC');
        }
        return $this->_valuesSelect;
    }

    protected function _getFields()
    {
        if (!isset($this->_fields)) {
            $this->_fields = new Vps_Collection_FormFields();
            if ($this->_getValues() instanceof Vps_Model_Rowset_Interface) {
                $pk = $this->_getValues()->getModel()->getPrimaryKey();
            }
            foreach ($this->_getValues() as $key => $i) {
                if (isset($pk)) {
                    $key = $i->$pk;
                }
                if (!is_string($i)) $i = $i->__toString();
                $this->_fields->add(new Vps_Form_Field_Checkbox($this->getFieldName().'_'.$key))
                    ->setKey($key)
                    ->setBoxLabel($i)
                    ->setHideLabel(true);
            }
        }
        return $this->_fields;
    }

    public function hasChildren()
    {
        return count($this->_fields) > 0;
    }
    public function getChildren()
    {
        return $this->_fields;
    }

    public function setPool($pool)
    {
        $this->_pool = $pool;
        return $this;
    }

    public function getPool()
    {
        return $this->_pool;
    }

    public function load(Vps_Model_Row_Interface $row, $postData = array())
    {
        if (!$row) return array();

        $dataModel = $row->getModel();
        if ($dataModel) $this->setDataModel($dataModel);

        $ref = $this->getRelModel()->getReference($this->_relationToValuesRule);
        $key = $ref['column'];

        $selectedIds = array();
        if ($this->getSave() !== false && $this->getInternalSave() !== false) {
            foreach ($row->getChildRows($this->getRelModel()) as $i) {
                $selectedIds[] = $i->$key;
            }
        }

        $ret = array();
        foreach ($this->_getFields() as $field) {
            if (isset($postData[$field->getFieldName()]) && $postData[$field->getFieldName()]) {
                $ret[$field->getFieldName()] = true;
            } else {
                $ret[$field->getFieldName()] = in_array($field->getKey(), $selectedIds);
            }
        }
        return $ret;
    }

    protected function _getIdsFromPostData($postData)
    {
        $new = array();
        foreach ($this->_getFields() as $f) {
            if (isset($postData[$f->getFieldName()]) && $postData[$f->getFieldName()]) {
                $new[] = substr($f->getFieldName(), strlen($this->getFieldName())+1);
            }
        }
        return $new;
    }

    public function prepareSave(Vps_Model_Row_Interface $row, $postData)
    {
        $dataModel = $row->getModel();
        if ($dataModel) $this->setDataModel($dataModel);

        $new = $this->_getIdsFromPostData($postData);

        $avaliableKeys = array();
        foreach ($this->_getFields() as $field) {
            $avaliableKeys[] = $field->getKey();
        }

        $ref = $this->getRelModel()->getReference($this->_relationToValuesRule);
        $valueKey = $ref['column'];

        foreach ($row->getChildRows($this->getRelModel()) as $savedRow) {
            $id = $savedRow->$valueKey;
            if (in_array($id, $avaliableKeys)) {
                if (!in_array($id, $new)) {
                    $savedRow->delete();
                    continue;
                } else {
                    unset($new[array_search($id, $new)]);
                }
            }
        }

        $dataPrimaryKey = $this->getDataModel()->getPrimaryKey();
        foreach ($new as $id) {
            if (in_array($id, $avaliableKeys)) {
                $i = $row->createChildRow($this->getRelModel());
                $i->$valueKey = $id;
            }
        }
    }

    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix);
        $helper = new Vps_View_Helper_FormField();
        $ret['html'] = '';
        $fields = $this->_getFields()->getTemplateVars($values, $fieldNamePostfix);
        $i = 0;
        foreach ($fields as $field) {
            $ret['html'] .= '<div class="checkboxItem'.($i==0?' first':'').'">'.$helper->returnFormField($field).'</div>';
            $i++;
        }
        $ret['html'] .= '<div class="checkboxItemEnd"></div>';
        return $ret;
    }
}
