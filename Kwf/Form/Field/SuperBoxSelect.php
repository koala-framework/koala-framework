<?php
class Kwf_Form_Field_SuperBoxSelect extends Kwf_Form_Field_ComboBox
{
    public function __construct($dependetModelRule, $relationToValuesRule, $field_label = null)
    {
        parent::__construct($dependetModelRule, $field_label);
        $this->setRelModel($dependetModelRule);
        $this->setRelationToValuesRule($relationToValuesRule);
        $this->setXtype('superboxselect');
    }

    public function load($row, array $postData = array())
    {
        if (!$row) return array();

        $relModel = $row->getModel()->getDependentModel($this->getRelModel());
        $ref = $relModel->getReference($this->getRelationToValuesRule());
        $key = $ref['column'];

        $selectedIds = array();
        if ($this->getSave() !== false && $row) {
            $s = $this->getChildRowsSelect();
            if (!$s) $s = array();
            foreach ($row->getChildRows($relModel, $s) as $i) {
                $selectedIds[] = $i->$key;
            }
        }

        $ret = array();
        $ret[$this->getFieldName()] = implode(',', $selectedIds);
        return $ret;
    }

    private function _getIdsFromPostData($postData)
    {
        if (!array_key_exists($this->getFieldName(), $postData) || !$postData[$this->getFieldName()]) return array();
        return explode(',', $postData[$this->getFieldName()]);
    }

    public function prepareSave(Kwf_Model_Row_Interface $row, $postData)
    {
        //TODO remove in later branches?
        if ($this->getSave() === false || $this->getInternalSave() === false) return;

        $new = $this->_getIdsFromPostData($postData);

//         $avaliableKeys = array(6,20,17);
        //foreach ($this->_getFields() as $field) {
//             $avaliableKeys[] = $field->getKey();
//         }

        $relModel = $row->getModel()->getDependentModel($this->getRelModel());
        $ref = $relModel->getReference($this->getRelationToValuesRule());
        $valueKey = $ref['column'];

        $s = $this->getChildRowsSelect();
        if (!$s) $s = array();
        foreach ($row->getChildRows($this->getRelModel(), $s) as $savedRow) {
            $id = $savedRow->$valueKey;
            if (true || in_array($id, $avaliableKeys)) {
                if (!in_array($id, $new)) {
                    $savedRow->delete();
                } else {
                    unset($new[array_search($id, $new)]);
                }
            }
        }

        foreach ($new as $id) {
            if (true || in_array($id, $avaliableKeys)) {
                $i = $row->createChildRow($this->getRelModel());
                $i->$valueKey = $id;
            }
        }

    }
}
