<?php
/**
 * TODO: validators
 *
 * @package Form
 */
class Kwf_Form_Field_MultiCheckbox extends Kwf_Form_Field_Abstract
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

    // setShowCheckAllLinks(true) | default=true, zeigt "alle" und "keine" links an, die alle checkboxes auf einmal setzen
    // setCheckAllText($txt)
    // setCheckNoneText($txt)

    // setValuesBoxLabelField(true) | feldname aus valuesModel fuer boxLabel
    // setAllowBlank(false) nur im FE
    //   setEmptyMessage
    
    // $this->setOutputType($type) nur im FE
    //    $type = 'vertical', otherwise horizontal

    /**
     * Zeigt mehrere Checkboxes an und speichert diese in einer Relationstabelle
     *
     * @param string|Kwf_Model_Abstract $dependetModelRule Kann folgendes sein:
     *               - Die rule vom Datenmodel zur Relationstabelle (string)
     *               - oder das RelationsModel selbst (Kwf_Model_Abstract)
     * @param string $relationToValuesRule Die rule vom Relationsmodel zum Values-model
     */
    public function __construct($dependetModelRule, $relationToValuesRule, $title = null, $fieldKey = null)
    {
        if (!is_string($dependetModelRule)) {
            if (is_object($dependetModelRule) && !($dependetModelRule instanceof Kwf_Model_Abstract)) {
                throw new Kwf_Exception("dependetModelRule must be of type string (Rule) or Kwf_Model_Abstract (RelationModel)");
            }
        }
        $this->setRelModel($dependetModelRule);
        $this->setValuesModel($relationToValuesRule);
        $this->_relationToValuesRule = $relationToValuesRule;

        if (!$fieldKey) $fieldKey = $relationToValuesRule;

        parent::__construct($fieldKey);
        if ($title) {
            $this->setTitle($title);
            $this->setFieldLabel($title);
        }
        $this->setAutoHeight(true);
        $this->setShowCheckAllLinks(true);
        $this->setCheckAllText(trlKwfStatic('All'));
        $this->setCheckNoneText(trlKwfStatic('None'));
        $this->setLayout('form');
        $this->setXtype('multicheckbox');
        $this->setEmptyMessage(trlKwfStatic('Please choose an option'));
        $this->setOutputType('horizontal');
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
            $this->_dataModel = Kwf_Model_Abstract::getInstance($this->_dataModel);
        }
        return $this->_dataModel;
    }

    public function getValuesModel()
    {
        if (is_string($this->_valuesModel)) {
            $relModel = $this->getRelModel();
            if (!$relModel) {
                throw new Kwf_Exception("RelationModel must be set first");
            }
            $ref = $relModel->getReference($this->_valuesModel);
            if ($ref && isset($ref['refModel'])) {
                $this->_valuesModel = $ref['refModel'];
            } else if ($ref && isset($ref['refModelClass'])) {
                $this->_valuesModel = Kwf_Model_Abstract::getInstance($ref['refModelClass']);
            } else {
                throw new Kwf_Exception("Values model cannot be found by reference '{$this->_valuesModel}'");
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
        if (isset($ret['outputType'])) unset($ret['outputType']); //wird von Ext-Form noch nicht unterstützt
        
        return $ret;
    }

    private function _getValues()
    {
        if ($this->getValues() !== null) {
            return $this->getValues();
        } else {
            return $this->getValuesModel()->getRows($this->getValuesSelect());
        }
    }

    public function setValuesSelect(Kwf_Model_Select $select)
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
            if ($this->getValuesModel() instanceof Kwf_Util_Model_Pool) {
                $this->_valuesSelect
                    ->whereEquals('pool', $this->getPool())
                    ->whereEquals('visible', 1)
                    ->order('pos', 'ASC');
            } else {
                throw new Kwf_Exception("setPool with MultiCheckbox only works if relationToValues references to an instance of Kwf_Util_Model_Pool");
            }
        }
        if ($this->getValuesModel()->hasColumn('pos') && !$this->_valuesSelect->getPart(Kwf_Model_Select::ORDER)) {
            $this->_valuesSelect->order('pos', 'ASC');
        }
        return $this->_valuesSelect;
    }

    protected function _getFields()
    {
        if (!isset($this->_fields)) {
            $this->_fields = new Kwf_Collection_FormFields();
            if ($this->_getValues() instanceof Kwf_Model_Rowset_Interface) {
                $pk = $this->_getValues()->getModel()->getPrimaryKey();
            }
            foreach ($this->_getValues() as $key => $i) {
                if (isset($pk)) {
                    $key = $i->$pk;
                }
                if (!is_string($i)) {
                    if ($this->getValuesBoxLabelField()) {
                        $boxLabelField = $this->getValuesBoxLabelField();
                        $i = $i->$boxLabelField;
                    } else {
                        $i = $i->__toString();
                    }
                }
                $this->_fields->add(new Kwf_Form_Field_Checkbox($this->getFieldName().'_'.$key))
                    ->setKey($key)
                    ->setBoxLabel($i)
                    ->setHideLabel(true);
            }
        }
        return $this->_fields;
    }

    public function hasChildren()
    {
        return ($this->_fields instanceof Countable) ? count($this->_fields) > 0 : false;
    }
    public function getChildren()
    {
        return $this->_getFields();
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

    public function load($row, $postData = array())
    {
        if (!$row) return array();

        $dataModel = $row->getModel();
        if ($dataModel) $this->setDataModel($dataModel);

        $ref = $this->getRelModel()->getReference($this->_relationToValuesRule);
        $key = $ref['column'];

        $selectedIds = array();
        if ($this->getSave() !== false && $row) {
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

    
    public function validate($row, $postData)
    {
        $ret = parent::validate($row, $postData);

        $dataModel = $row->getModel();
        if ($dataModel) $this->setDataModel($dataModel);

        if (!is_null($this->getAllowBlank()) && !$this->getAllowBlank()) {
            if (!count($this->_getIdsFromPostData($postData))) {
                $ret[] = array(
                    'message' => $this->getEmptyMessage(),
                    'field' => $this
                );
            }
        }
        return $ret;
    }

    public function prepareSave($row, $postData)
    {
        //TODO remove in later branches?
        if ($this->getSave() === false || $this->getInternalSave() === false) return;

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

        foreach ($new as $id) {
            if (in_array($id, $avaliableKeys)) {
                $i = $row->createChildRow($this->getRelModel());
                $i->$valueKey = $id;
            }
        }
    }

    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'checkAllText';
        $ret[] = 'checkNoneText';
        $ret[] = 'emptyMessage';
        $ret[] = 'title';
        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        $helper = new Kwf_View_Helper_FormField();
        $ret['html']  = '<div class="kwfFormFieldMultiCheckbox kwfFormFieldMultiCheckbox'.ucfirst($this->getOutputType()).'"';
        $ret['html'] .= ' data-fieldname="'.$this->getFieldName().$fieldNamePostfix.'"';
        $ret['html'] .= '>';
        $fields = $this->_getFields()->getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        $i = 0;
        foreach ($fields as $field) {
            $ret['html'] .= '<div class="checkboxItem'.($i==0?' first':'').'">'.$helper->returnFormField($field).'</div>';
            $i++;
        }
        $ret['html'] .= '<div class="checkboxItemEnd"></div>';
        if ($this->getShowCheckAllLinks()) {
            $ret['html'] .=
                '<div class="checkAllLinksWrapper">'
                    .'<a href="#" class="kwfMultiCheckboxCheckAll">'.$this->getCheckAllText().'</a>'
                    .' / '
                    .'<a href="#" class="kwfMultiCheckboxCheckNone">'.$this->getCheckNoneText().'</a>'
                .'</div>';
        }
        $ret['html'] .= '</div>';
        return $ret;
    }


    /**
     * Set the field from ValuesModel that will be used as box label
     *
     * if not set __toString() will be used
     *
     * @param string field name
     * @return $this
     */
    public function setValuesBoxLabelField($value)
    {
        return $this->setProperty('valuesBoxLabelField', $value);
    }

    
}
